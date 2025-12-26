<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Infrastructure;

use App\Common\Domain\Model\Process\ProcessId;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTracker;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerCollection;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;

class InMemoryTimeConstrainedProcessTrackerRepository implements TimeConstrainedProcessTrackerRepository
{
    private array $trackers = [];

    public function add(TimeConstrainedProcessTracker $aTimeConstrainedProcessTracker): void
    {
        $this->save($aTimeConstrainedProcessTracker);
    }

    public function allTimedOut(): TimeConstrainedProcessTrackerCollection
    {
        $timedOut = array_filter($this->trackers, function (TimeConstrainedProcessTracker $tracker) {
            return !$tracker->isCompleted();
        });

        return new TimeConstrainedProcessTrackerCollection($timedOut);
    }

    public function allTimedOutOf(string $aTenantId): TimeConstrainedProcessTrackerCollection
    {
        $timedOut = array_filter($this->trackers, function (TimeConstrainedProcessTracker $tracker) use ($aTenantId) {
            return $tracker->tenantId() === $aTenantId && !$tracker->isCompleted();
        });

        return new TimeConstrainedProcessTrackerCollection($timedOut);
    }

    public function allTrackers(string $aTenantId): TimeConstrainedProcessTrackerCollection
    {
        $trackers = array_filter($this->trackers, function (TimeConstrainedProcessTracker $tracker) use ($aTenantId) {
            return $tracker->tenantId() === $aTenantId;
        });

        return new TimeConstrainedProcessTrackerCollection($trackers);
    }

    public function save(TimeConstrainedProcessTracker $aTimeConstrainedProcessTracker): void
    {
        $key = $this->keyFor($aTimeConstrainedProcessTracker->tenantId(), $aTimeConstrainedProcessTracker->processId());
        $this->trackers[$key] = $aTimeConstrainedProcessTracker;
    }

    public function trackerOfProcessId(string $aTenantId, ProcessId $aProcessId): TimeConstrainedProcessTracker
    {
        $key = $this->keyFor($aTenantId, $aProcessId);
        $tracker = $this->trackers[$key] ?? null;

        if ($tracker === null) {
            throw new \RuntimeException('TimeConstrainedProcessTracker not found for process id: ' . $aProcessId->id());
        }

        return $tracker;
    }

    public function clear(): void
    {
        $this->trackers = [];
    }

    private function keyFor(string $aTenantId, ProcessId $aProcessId): string
    {
        return $aTenantId . ':' . $aProcessId->id();
    }
}
