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

        if ($aBacklogItemId !== null) {
            $this->setBacklogItemId($aBacklogItemId);
        }
        $this->setOrdering($anOrdering);
        if ($aReleaseId !== null) {
            $this->setReleaseId($aReleaseId);
        }
        if ($aTenantId !== null) {
            $this->setTenantId($aTenantId);
        }
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
        if (!$anObject instanceof self) {
            return false;
        }

        return $this->tenantId()->equals($anObject->tenantId()) &&
            $this->releaseId()->equals($anObject->releaseId()) &&
            $this->backlogItemId()->equals($anObject->backlogItemId());
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
        $this->assertArgumentNotNull($aReleaseId, 'The release id is required.');

        $this->releaseId = $aReleaseId;
    }

    private function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenant id is required.');

        $this->tenantId = $aTenantId;
    }
}
