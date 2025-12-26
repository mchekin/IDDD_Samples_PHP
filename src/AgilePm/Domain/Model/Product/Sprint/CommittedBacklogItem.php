<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\Sprint;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use App\AgilePm\Domain\Model\Tenant\TenantId;

class CommittedBacklogItem extends Entity
{
    private BacklogItemId $backlogItemId;
    private int $ordering;
    private SprintId $sprintId;
    private TenantId $tenantId;

    public function __construct(
        TenantId $aTenantId = null,
        SprintId $aSprintId = null,
        BacklogItemId $aBacklogItemId = null,
        int $anOrdering = 0
    ) {
        if ($aTenantId === null && $aSprintId === null && $aBacklogItemId === null) {
            parent::__construct();
            return;
        }

        parent::__construct();

        $this->setBacklogItemId($aBacklogItemId);
        $this->setOrdering($anOrdering);
        $this->setSprintId($aSprintId);
        $this->setTenantId($aTenantId);
    }

    public function backlogItemId(): BacklogItemId
    {
        return $this->backlogItemId;
    }

    public function ordering(): int
    {
        return $this->ordering;
    }

    public function sprintId(): SprintId
    {
        return $this->sprintId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function equals(object $anObject): bool
    {
        $equalObjects = false;

        if ($anObject !== null && $this::class === $anObject::class) {
            $equalObjects =
                $this->tenantId()->equals($anObject->tenantId()) &&
                $this->sprintId()->equals($anObject->sprintId()) &&
                $this->backlogItemId()->equals($anObject->backlogItemId());
        }

        return $equalObjects;
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            (282891 * 53)
            + $this->tenantId()->hashCode()
            + $this->sprintId()->hashCode()
            + $this->backlogItemId()->hashCode();

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return 'CommittedBacklogItem [sprintId=' . $this->sprintId . ', ordering=' . $this->ordering . ']';
    }

    protected function reorderFrom(BacklogItemId $anId, int $anOrderOfPriority): void
    {
        if ($this->backlogItemId()->equals($anId)) {
            $this->setOrdering($anOrderOfPriority);
        } elseif ($this->ordering() >= $anOrderOfPriority) {
            $this->setOrdering($this->ordering() + 1);
        }
    }

    protected function setOrdering(int $anOrdering): void
    {
        $this->ordering = $anOrdering;
    }

    private function setBacklogItemId(BacklogItemId $aBacklogItemId): void
    {
        $this->assertArgumentNotNull($aBacklogItemId, 'The backlog item id must be provided.');

        $this->backlogItemId = $aBacklogItemId;
    }

    private function setSprintId(SprintId $aSprintId): void
    {
        $this->assertArgumentNotNull($aSprintId, 'The sprint id must be provided.');

        $this->sprintId = $aSprintId;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenant id must be provided.');

        $this->tenantId = $aTenantId;
    }
}
