<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use App\AgilePm\Domain\Model\Tenant\TenantId;

class ProductBacklogItem extends Entity
{
    private BacklogItemId $backlogItemId;
    private int $ordering;
    private ProductId $productId;
    private TenantId $tenantId;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        BacklogItemId $aBacklogItemId,
        int $anOrdering
    ) {
        parent::__construct();

        $this->setBacklogItemId($aBacklogItemId);
        $this->setOrdering($anOrdering);
        $this->setProductId($aProductId);
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

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function equals(object $anObject): bool
    {
        $equalObjects = false;

        if ($anObject !== null && get_class($this) === get_class($anObject)) {
            $typedObject = $anObject;
            $equalObjects =
                $this->tenantId()->equals($typedObject->tenantId()) &&
                $this->productId()->equals($typedObject->productId()) &&
                $this->backlogItemId()->equals($typedObject->backlogItemId());
        }

        return $equalObjects;
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (15389 * 97)
            + $this->tenantId()->hashCode()
            + $this->productId()->hashCode()
            + $this->backlogItemId()->hashCode();

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return "ProductBacklogItem [tenantId={$this->tenantId}"
                . ", productId={$this->productId}"
                . ", backlogItemId={$this->backlogItemId}"
                . ", ordering={$this->ordering}]";
    }

    public function reorderFrom(BacklogItemId $anId, int $anOrdering): void
    {
        if ($this->backlogItemId()->equals($anId)) {
            $this->setOrdering($anOrdering);
        } elseif ($this->ordering() >= $anOrdering) {
            $this->setOrdering($this->ordering() + 1);
        }
    }

    private function setBacklogItemId(BacklogItemId $aBacklogItemId): void
    {
        $this->assertArgumentNotNull($aBacklogItemId, 'The backlog item id must be provided.');

        $this->backlogItemId = $aBacklogItemId;
    }

    private function setOrdering(int $anOrdering): void
    {
        $this->ordering = $anOrdering;
    }

    private function setProductId(ProductId $aProductId): void
    {
        $this->assertArgumentNotNull($aProductId, 'The product id must be provided.');

        $this->productId = $aProductId;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenant id must be provided.');

        $this->tenantId = $aTenantId;
    }
}