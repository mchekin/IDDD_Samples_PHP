<?php declare(strict_types=1);

namespace App\Common\Port\Adapter\Persistence\SQLite;

use App\Common\Domain\Model\Process\ProcessId;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTracker;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerCollection;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;
use PDO;

/**
 * SQLite-based TimeConstrainedProcessTrackerRepository - matches Java's LevelDB version
 * Uses UnitOfWork pattern for transaction management and identity map
 */
class SQLiteTimeConstrainedProcessTrackerRepository implements TimeConstrainedProcessTrackerRepository
{
    public function __construct(string $databasePath)
    {
        // Ensure database is initialized
        SQLiteProvider::instance()->databaseFrom($databasePath);
    }

    public function add(TimeConstrainedProcessTracker $aTimeConstrainedProcessTracker): void
    {
        $this->save($aTimeConstrainedProcessTracker);
    }

    public function allTimedOut(): TimeConstrainedProcessTrackerCollection
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $trackers = [];

        // Query all trackers
        $stmt = $unitOfWork->database()->prepare(
            'SELECT key, data FROM entities WHERE class = ?'
        );
        $stmt->execute([TimeConstrainedProcessTracker::class]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row) || !isset($row['data']) || !is_string($row['data'])) {
                continue;
            }
            /** @var TimeConstrainedProcessTracker $tracker */
            $tracker = $unitOfWork->deserializeFromJson($row['data'], TimeConstrainedProcessTracker::class);
            if (!$tracker->isCompleted()) {
                $trackers[] = $tracker;
            }
        }

        return new TimeConstrainedProcessTrackerCollection($trackers);
    }

    public function allTimedOutOf(string $aTenantId): TimeConstrainedProcessTrackerCollection
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $trackers = [];

        // Query all trackers for this tenant
        $stmt = $unitOfWork->database()->prepare(
            'SELECT key, data FROM entities WHERE class = ? AND key LIKE ?'
        );
        $stmt->execute([
            TimeConstrainedProcessTracker::class,
            $aTenantId . ':%'
        ]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row) || !isset($row['data']) || !is_string($row['data'])) {
                continue;
            }
            /** @var TimeConstrainedProcessTracker $tracker */
            $tracker = $unitOfWork->deserializeFromJson($row['data'], TimeConstrainedProcessTracker::class);
            if ($tracker->tenantId() === $aTenantId && !$tracker->isCompleted()) {
                $trackers[] = $tracker;
            }
        }

        return new TimeConstrainedProcessTrackerCollection($trackers);
    }

    public function allTrackers(string $aTenantId): TimeConstrainedProcessTrackerCollection
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $trackers = [];

        // Query all trackers for this tenant
        $stmt = $unitOfWork->database()->prepare(
            'SELECT key, data FROM entities WHERE class = ? AND key LIKE ?'
        );
        $stmt->execute([
            TimeConstrainedProcessTracker::class,
            $aTenantId . ':%'
        ]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row) || !isset($row['data']) || !is_string($row['data'])) {
                continue;
            }
            /** @var TimeConstrainedProcessTracker $tracker */
            $tracker = $unitOfWork->deserializeFromJson($row['data'], TimeConstrainedProcessTracker::class);
            if ($tracker->tenantId() === $aTenantId) {
                $trackers[] = $tracker;
            }
        }

        return new TimeConstrainedProcessTrackerCollection($trackers);
    }

    public function save(TimeConstrainedProcessTracker $aTimeConstrainedProcessTracker): void
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aTimeConstrainedProcessTracker->tenantId(), $aTimeConstrainedProcessTracker->processId());

        $unitOfWork->write($key, $aTimeConstrainedProcessTracker);
    }

    public function trackerOfProcessId(string $aTenantId, ProcessId $aProcessId): TimeConstrainedProcessTracker
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aTenantId, $aProcessId);

        $tracker = $unitOfWork->read($key, TimeConstrainedProcessTracker::class);

        if ($tracker === null) {
            throw new \RuntimeException('TimeConstrainedProcessTracker not found for process id: ' . $aProcessId->id());
        }

        assert($tracker instanceof TimeConstrainedProcessTracker);
        return $tracker;
    }

    private function keyFor(string $aTenantId, ProcessId $aProcessId): string
    {
        return $aTenantId . ':' . $aProcessId->id();
    }
}
