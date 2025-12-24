<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemType;
use App\AgilePm\Domain\Model\Product\BacklogItem\StoryPoints;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\DomainEvent;
use DateTime;

class ProductBacklogItemPlanned implements DomainEvent
{
    private BacklogItemId $backlogItemId;
    private string $category;
    private int $eventVersion;
    private DateTime $occurredOn;
    private ProductId $productId;
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
        StoryPoints $aStoryPoints
    ) {
        $this->backlogItemId = $aBacklogItemId;
        $this->category = $aCategory;
        $this->eventVersion = 1;
        $this->occurredOn = new DateTime();
        $this->productId = $aProductId;
        $this->storyPoints = $aStoryPoints;
        $this->summary = $aSummary;
        $this->tenantId = $aTenantId;
        $this->type = $aType;
    }

    public function backlogItemId(): BacklogItemId
    {
        return $this->backlogItemId;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function eventVersion(): int
    {
        return $this->eventVersion;
    }

    public function occurredOn(): DateTime
    {
        return $this->occurredOn;
    }

    public function productId(): ProductId
    {
        return $this->productId;
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
}