<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\BacklogItem;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Tenant\TenantId;

class BacklogItem extends Entity
{
    private BacklogItemId $backlogItemId;
    private string $category;
    private ProductId $productId;
    private BacklogItemStatus $status;
    private StoryPoints $storyPoints;
    private string $summary;
    private TenantId $tenantId;
    private BacklogItemType $type;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        BacklogItemId $aBacklogItemId,
        string $aSummary,
        string $aCategory,
        BacklogItemType $aType,
        BacklogItemStatus $aStatus,
        StoryPoints $aStoryPoints
    ) {
        parent::__construct();

        $this->setTenantId($aTenantId);
        $this->setProductId($aProductId);
        $this->setBacklogItemId($aBacklogItemId);
        $this->setSummary($aSummary);
        $this->setCategory($aCategory);
        $this->setType($aType);
        $this->setStatus($aStatus);
        $this->setStoryPoints($aStoryPoints);
    }

    public function backlogItemId(): BacklogItemId
    {
        return $this->backlogItemId;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function status(): BacklogItemStatus
    {
        return $this->status;
    }

    public function storyPoints(): StoryPoints
    {
        return $this->storyPoints;
    }

    public function summary(): string
    {
        return $this->summary;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function type(): BacklogItemType
    {
        return $this->type;
    }

    public function equals(object $anObject): bool
    {
        if (!$anObject instanceof self) {
            return false;
        }

        return $this->tenantId()->equals($anObject->tenantId()) &&
            $this->productId()->equals($anObject->productId()) &&
            $this->backlogItemId()->equals($anObject->backlogItemId());
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (34685 * 7)
            + $this->tenantId()->hashCode()
            + $this->productId()->hashCode()
            + $this->backlogItemId()->hashCode();

        return $hashCodeValue;
    }

    private function setBacklogItemId(BacklogItemId $aBacklogItemId): void
    {
        $this->assertArgumentNotNull($aBacklogItemId, 'The backlogItemId must be provided.');
        $this->backlogItemId = $aBacklogItemId;
    }

    private function setCategory(string $aCategory): void
    {
        $this->assertArgumentNotEmpty($aCategory, 'The category must be provided.');
        $this->assertArgumentLength($aCategory, 25, 'The category must be 25 characters or less.');
        $this->category = $aCategory;
    }

    private function setProductId(ProductId $aProductId): void
    {
        $this->assertArgumentNotNull($aProductId, 'The product id must be provided.');
        $this->productId = $aProductId;
    }

    private function setStatus(BacklogItemStatus $aStatus): void
    {
        $this->status = $aStatus;
    }

    private function setStoryPoints(StoryPoints $aStoryPoints): void
    {
        $this->storyPoints = $aStoryPoints;
    }

    private function setSummary(string $aSummary): void
    {
        $this->assertArgumentNotEmpty($aSummary, 'The summary must be provided.');
        $this->assertArgumentLength($aSummary, 100, 'The summary must be 100 characters or less.');
        $this->summary = $aSummary;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenant id must be provided.');
        $this->tenantId = $aTenantId;
    }

    private function setType(BacklogItemType $aType): void
    {
        $this->assertArgumentNotNull($aType, 'The backlog item type must be provided.');
        $this->type = $aType;
    }
}