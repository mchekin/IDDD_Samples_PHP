<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Discussion\DiscussionDescriptor;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItem;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemStatus;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemType;
use App\AgilePm\Domain\Model\Product\BacklogItem\StoryPoints;
use App\AgilePm\Domain\Model\Product\Release\Release;
use App\AgilePm\Domain\Model\Product\Release\ReleaseId;
use App\AgilePm\Domain\Model\Product\Sprint\Sprint;
use App\AgilePm\Domain\Model\Product\Sprint\SprintId;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\DomainEventPublisher;
use DateTime;

class Product extends Entity
{
    private array $backlogItems = [];
    private string $description;
    private ProductDiscussion $discussion;
    private ?string $discussionInitiationId = null;
    private string $name;
    private ProductId $productId;
    private ProductOwnerId $productOwnerId;
    private TenantId $tenantId;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        ProductOwnerId $aProductOwnerId,
        string $aName,
        string $aDescription,
        DiscussionAvailability $aDiscussionAvailability
    ) {
        parent::__construct();

        $this->setTenantId($aTenantId);
        $this->setDescription($aDescription);
        $this->setDiscussion(ProductDiscussion::fromAvailability($aDiscussionAvailability));
        $this->setDiscussionInitiationId(null);
        $this->setName($aName);
        $this->setProductId($aProductId);
        $this->setProductOwnerId($aProductOwnerId);

        DomainEventPublisher::instance()->publish(new ProductCreated(
            $this->tenantId(),
            $this->productId(),
            $this->productOwnerId(),
            $this->name(),
            $this->description(),
            $this->discussion()->availability()->isRequested()
        ));
    }

    public function allBacklogItems(): array
    {
        return $this->backlogItems();
    }

    public function changeProductOwner(ProductOwner $aProductOwner): void
    {
        if (!$this->productOwnerId()->equals($aProductOwner->productOwnerId())) {
            $this->setProductOwnerId($aProductOwner->productOwnerId());

            // TODO: publish event
        }
    }

    public function description(): string
    {
        return $this->description;
    }

    public function discussion(): ProductDiscussion
    {
        return $this->discussion;
    }

    public function discussionInitiationId(): ?string
    {
        return $this->discussionInitiationId;
    }

    public function failDiscussionInitiation(): void
    {
        if (!$this->discussion()->availability()->isReady()) {
            $this->setDiscussionInitiationId(null);
            $this->setDiscussion(
                ProductDiscussion::fromAvailability(DiscussionAvailability::FAILED)
            );
        }
    }

    public function initiateDiscussion(DiscussionDescriptor $aDescriptor): void
    {
        if ($aDescriptor === null) {
            throw new \InvalidArgumentException('The descriptor must not be null.');
        }

        if ($this->discussion()->availability()->isRequested()) {
            $this->setDiscussion($this->discussion()->nowReady($aDescriptor));

            DomainEventPublisher::instance()->publish(new ProductDiscussionInitiated(
                $this->tenantId(),
                $this->productId(),
                $this->discussion()
            ));
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function planBacklogItem(
        BacklogItemId $aNewBacklogItemId,
        string $aSummary,
        string $aCategory,
        BacklogItemType $aType,
        StoryPoints $aStoryPoints
    ): BacklogItem {
        $backlogItem = new BacklogItem(
            $this->tenantId(),
            $this->productId(),
            $aNewBacklogItemId,
            $aSummary,
            $aCategory,
            $aType,
            BacklogItemStatus::PLANNED,
            $aStoryPoints
        );

        DomainEventPublisher::instance()->publish(new ProductBacklogItemPlanned(
            $backlogItem->tenantId(),
            $backlogItem->productId(),
            $backlogItem->backlogItemId(),
            $backlogItem->summary(),
            $backlogItem->category(),
            $backlogItem->type(),
            $backlogItem->storyPoints()
        ));

        return $backlogItem;
    }

    public function plannedProductBacklogItem(BacklogItem $aBacklogItem): void
    {
        $this->assertArgumentEquals(
            $this->tenantId(),
            $aBacklogItem->tenantId(),
            'The product and backlog item must have same tenant.'
        );
        $this->assertArgumentEquals(
            $this->productId(),
            $aBacklogItem->productId(),
            'The backlog item must belong to product.'
        );

        $ordering = count($this->backlogItems()) + 1;

        $productBacklogItem = new ProductBacklogItem(
            $this->tenantId(),
            $this->productId(),
            $aBacklogItem->backlogItemId(),
            $ordering
        );

        $this->backlogItems[] = $productBacklogItem;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function productOwnerId(): ProductOwnerId
    {
        return $this->productOwnerId;
    }

    public function reorderFrom(BacklogItemId $anId, int $anOrdering): void
    {
        foreach ($this->backlogItems() as $productBacklogItem) {
            $productBacklogItem->reorderFrom($anId, $anOrdering);
        }
    }

    public function requestDiscussion(DiscussionAvailability $aDiscussionAvailability): void
    {
        if (!$this->discussion()->availability()->isReady()) {
            $this->setDiscussion(
                ProductDiscussion::fromAvailability($aDiscussionAvailability)
            );

            DomainEventPublisher::instance()->publish(new ProductDiscussionRequested(
                $this->tenantId(),
                $this->productId(),
                $this->productOwnerId(),
                $this->name(),
                $this->description(),
                $this->discussion()->availability()->isRequested()
            ));
        }
    }

    public function scheduleRelease(
        ReleaseId $aNewReleaseId,
        string $aName,
        string $aDescription,
        DateTime $aBegins,
        DateTime $anEnds
    ): Release {
        $release = new Release(
            $this->tenantId(),
            $this->productId(),
            $aNewReleaseId,
            $aName,
            $aDescription,
            $aBegins,
            $anEnds
        );

        DomainEventPublisher::instance()->publish(new ProductReleaseScheduled(
            $release->tenantId(),
            $release->productId(),
            $release->releaseId(),
            $release->name(),
            $release->description(),
            $release->begins(),
            $release->ends()
        ));

        return $release;
    }

    public function scheduleSprint(
        SprintId $aNewSprintId,
        string $aName,
        string $aGoals,
        DateTime $aBegins,
        DateTime $anEnds
    ): Sprint {
        $sprint = new Sprint(
            $this->tenantId(),
            $this->productId(),
            $aNewSprintId,
            $aName,
            $aGoals,
            $aBegins,
            $anEnds
        );

        DomainEventPublisher::instance()->publish(new ProductSprintScheduled(
            $sprint->tenantId(),
            $sprint->productId(),
            $sprint->sprintId(),
            $sprint->name(),
            $sprint->goals(),
            $sprint->begins(),
            $sprint->ends()
        ));

        return $sprint;
    }

    public function startDiscussionInitiation(string $aDiscussionInitiationId): void
    {
        if (!$this->discussion()->availability()->isReady()) {
            $this->setDiscussionInitiationId($aDiscussionInitiationId);
        }
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
                $this->productId()->equals($typedObject->productId());
        }

        return $equalObjects;
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (2335 * 3)
            + $this->tenantId()->hashCode()
            + $this->productId()->hashCode();

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return "Product [tenantId={$this->tenantId}, productId={$this->productId}"
                . ", backlogItems=" . count($this->backlogItems) . ", description="
                . $this->description . ", discussion={$this->discussion}"
                . ", discussionInitiationId={$this->discussionInitiationId}"
                . ", name={$this->name}, productOwnerId={$this->productOwnerId}]";
    }

    private function backlogItems(): array
    {
        return $this->backlogItems;
    }

    private function setBacklogItems(array $backlogItems): void
    {
        $this->backlogItems = $backlogItems;
    }

    private function setDescription(string $aDescription): void
    {
        $this->assertArgumentNotEmpty($aDescription, 'The description must be provided.');
        $this->assertArgumentLength($aDescription, 500, 'Description must be 500 characters or less.');

        $this->description = $aDescription;
    }

    private function setDiscussion(ProductDiscussion $aDiscussion): void
    {
        $this->assertArgumentNotNull($aDiscussion, 'The discussion is required even if it is unused.');

        $this->discussion = $aDiscussion;
    }

    private function setDiscussionInitiationId(?string $aDiscussionInitiationId): void
    {
        if ($aDiscussionInitiationId !== null) {
            $this->assertArgumentLength(
                $aDiscussionInitiationId,
                100,
                'Discussion initiation identity must be 100 characters or less.'
            );
        }

        $this->discussionInitiationId = $aDiscussionInitiationId;
    }

    private function setName(string $aName): void
    {
        $this->assertArgumentNotEmpty($aName, 'The name must be provided.');
        $this->assertArgumentLength($aName, 100, 'The name must be 100 characters or less.');

        $this->name = $aName;
    }

    private function setProductId(ProductId $aProductId): void
    {
        $this->assertArgumentNotNull($aProductId, 'The productId must be provided.');

        $this->productId = $aProductId;
    }

    private function setProductOwnerId(ProductOwnerId $aProductOwnerId): void
    {
        $this->assertArgumentNotNull($aProductOwnerId, 'The productOwnerId must be provided.');
        $this->assertArgumentEquals(
            $this->tenantId(),
            $aProductOwnerId->tenantId(),
            'The productOwner must have the same tenant.'
        );

        $this->productOwnerId = $aProductOwnerId;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenantId must be provided.');

        $this->tenantId = $aTenantId;
    }

    private function __construct()
    {
        parent::__construct();

        $this->setBacklogItems([]);
    }
}