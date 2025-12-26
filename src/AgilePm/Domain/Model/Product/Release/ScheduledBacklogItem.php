<?php

declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\Release;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use App\AgilePm\Domain\Model\Tenant\TenantId;

class ScheduledBacklogItem extends Entity
{
    private BacklogItemId $backlogItemId;
    private int $ordering;
    private ReleaseId $releaseId;
    private TenantId $tenantId;

    public function __construct(
        ?TenantId $aTenantId = null,
        ?ReleaseId $aReleaseId = null,
        ?BacklogItemId $aBacklogItemId = null,
        int $anOrdering = 0
    ) {
        if ($aTenantId === null && $aReleaseId === null && $aBacklogItemId === null) {
            parent::__construct();
            return;
        }

        parent::__construct();

        $this->setBacklogItemId($aBacklogItemId);
        $this->setOrdering($anOrdering);
        $this->setReleaseId($aReleaseId);
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

    public function equals(object $anObject): bool
    {
        $equalObjects = false;

        if ($anObject !== null && $this::class === $anObject::class) {
            $equalObjects =
                $this->tenantId()->equals($anObject->tenantId()) &&
                $this->releaseId()->equals($anObject->releaseId()) &&
                $this->backlogItemId()->equals($anObject->backlogItemId());
        }

        return $equalObjects;
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            (73281 * 47)
            + $this->tenantId()->hashCode()
            + $this->releaseId()->hashCode()
            + $this->backlogItemId()->hashCode();

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return 'ScheduledBacklogItem [tenantId=' . $this->tenantId
            . ', releaseId=' . $this->releaseId
            . ', backlogItemId=' . $this->backlogItemId
            . ', ordering=' . $this->ordering . ']';
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

    private function releaseId(): ReleaseId
    {
        return $this->releaseId;
    }

    private function setReleaseId(ReleaseId $aReleaseId): void
    {
        if ($aReleaseId === null) {
            throw new \InvalidArgumentException('The release id is required.');
        }

        $this->releaseId = $aReleaseId;
    }

    private function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        if ($aTenantId === null) {
            throw new \InvalidArgumentException('The tenant id is required.');
        }

        $this->tenantId = $aTenantId;
    }
}
