<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\Release;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItem;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use DateTime;

class Release extends Entity
{
    private bool $archived = false;
    private array $backlogItems = [];
    private DateTime $begins;
    private string $description;
    private DateTime $ends;
    private string $name;
    private ProductId $productId;
    private ReleaseId $releaseId;
    private TenantId $tenantId;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        ReleaseId $aReleaseId,
        string $aName,
        string $aDescription,
        DateTime $aBegins,
        DateTime $anEnds
    ) {
        parent::__construct();

        if ($anEnds < $aBegins) {
            throw new \InvalidArgumentException('Release must not end before it begins.');
        }

        $this->setBegins($aBegins);
        $this->setDescription($aDescription);
        $this->setEnds($anEnds);
        $this->setName($aName);
        $this->setProductId($aProductId);
        $this->setReleaseId($aReleaseId);
        $this->setTenantId($aTenantId);
    }

    public function allScheduledBacklogItems(): array
    {
        return $this->backlogItems();
    }

    public function archived(bool $anArchived): void
    {
        $this->setArchived($anArchived);

        // TODO: publish event / student assignment
    }

    public function begins(): DateTime
    {
        return $this->begins;
    }

    public function describeAs(string $aDescription): void
    {
        $this->setDescription($aDescription);

        // TODO: publish event / student assignment
    }

    public function description(): string
    {
        return $this->description;
    }

    public function ends(): DateTime
    {
        return $this->ends;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function nowBeginsOn(DateTime $aBegins): void
    {
        $this->setBegins($aBegins);

        // TODO: publish event / student assignment
    }

    public function nowEndsOn(DateTime $anEnds): void
    {
        $this->setEnds($anEnds);

        // TODO: publish event / student assignment
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function releaseId(): ReleaseId
    {
        return $this->releaseId;
    }

    public function rename(string $aName): void
    {
        $this->setName($aName);

        // TODO: publish event / student assignment
    }

    public function reorderFrom(BacklogItemId $anId, int $anOrderOfPriority): void
    {
        foreach ($this->backlogItems() as $scheduledBacklogItem) {
            $scheduledBacklogItem->reorderFrom($anId, $anOrderOfPriority);
        }
    }

    public function schedule(BacklogItem $aBacklogItem): void
    {
        $this->assertArgumentEquals($this->tenantId(), $aBacklogItem->tenantId(), 'Must have same tenants.');
        $this->assertArgumentEquals($this->productId(), $aBacklogItem->productId(), 'Must have same products.');

        $ordering = count($this->backlogItems()) + 1;

        $scheduledBacklogItem = new ScheduledBacklogItem(
            $this->tenantId(),
            $this->releaseId(),
            $aBacklogItem->backlogItemId(),
            $ordering
        );

        $this->backlogItems[] = $scheduledBacklogItem;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function unschedule(BacklogItem $aBacklogItem): void
    {
        $this->assertArgumentEquals($this->tenantId(), $aBacklogItem->tenantId(), 'Must have same tenants.');
        $this->assertArgumentEquals($this->productId(), $aBacklogItem->productId(), 'Must have same products.');

        $scheduledBacklogItem = new ScheduledBacklogItem(
            $this->tenantId(),
            $this->releaseId(),
            $aBacklogItem->backlogItemId()
        );

        $key = array_search($scheduledBacklogItem, $this->backlogItems, true);
        if ($key !== false) {
            unset($this->backlogItems[$key]);
            $this->backlogItems = array_values($this->backlogItems);
        }
    }

    public function equals(object $anObject): bool
    {
        $equalObjects = false;

        if ($anObject !== null && get_class($this) === get_class($anObject)) {
            $typedObject = $anObject;
            $equalObjects =
                $this->tenantId()->equals($typedObject->tenantId()) &&
                $this->productId()->equals($typedObject->productId()) &&
                $this->releaseId()->equals($typedObject->releaseId());
        }

        return $equalObjects;
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (84519 * 41)
            + $this->tenantId()->hashCode()
            + $this->productId()->hashCode()
            + $this->releaseId()->hashCode();

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return "Release [tenantId={$this->tenantId}, productId={$this->productId}"
                . ", releaseId={$this->releaseId}, archived={$this->archived}"
                . ", backlogItems=" . count($this->backlogItems) . ", begins={$this->begins->format('Y-m-d H:i:s')}"
                . ", description={$this->description}, ends={$this->ends->format('Y-m-d H:i:s')}"
                . ", name={$this->name}]";
    }

    private function setArchived(bool $anArchived): void
    {
        $this->archived = $anArchived;
    }

    private function backlogItems(): array
    {
        return $this->backlogItems;
    }

    private function setBacklogItems(array $aBacklogItems): void
    {
        $this->backlogItems = $aBacklogItems;
    }

    private function setBegins(DateTime $aBegins): void
    {
        $this->assertArgumentNotNull($aBegins, 'The begins must be provided.');

        $this->begins = $aBegins;
    }

    private function setDescription(string $aDescription): void
    {
        $this->assertArgumentLength($aDescription, 500, 'The description must be 500 characters or less.');

        $this->description = $aDescription;
    }

    private function setEnds(DateTime $anEnds): void
    {
        $this->assertArgumentNotNull($anEnds, 'The ends must be provided.');

        $this->ends = $anEnds;
    }

    private function setName(string $aName): void
    {
        $this->assertArgumentNotEmpty($aName, 'The name must be provided.');
        $this->assertArgumentLength($aName, 100, 'The name must be 100 characters or less.');

        $this->name = $aName;
    }

    private function setProductId(ProductId $aProductId): void
    {
        $this->assertArgumentNotNull($aProductId, 'The product id must be provided.');

        $this->productId = $aProductId;
    }

    private function setReleaseId(ReleaseId $aReleaseId): void
    {
        $this->assertArgumentNotNull($aReleaseId, 'The release id must be provided.');

        $this->releaseId = $aReleaseId;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenant id must be provided.');

        $this->tenantId = $aTenantId;
    }
}