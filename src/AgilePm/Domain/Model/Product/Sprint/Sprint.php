<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\Sprint;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItem;
use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use DateTime;

class Sprint extends Entity
{
    private array $backlogItems = [];
    private DateTime $begins;
    private DateTime $ends;
    private string $goals;
    private string $name;
    private ProductId $productId;
    private ?string $retrospective = null;
    private SprintId $sprintId;
    private TenantId $tenantId;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        SprintId $aSprintId,
        string $aName,
        string $aGoals,
        DateTime $aBegins,
        DateTime $anEnds
    ) {
        parent::__construct();

        if ($anEnds < $aBegins) {
            throw new \InvalidArgumentException('Sprint must not end before it begins.');
        }

        $this->setBegins($aBegins);
        $this->setEnds($anEnds);
        $this->setGoals($aGoals);
        $this->setName($aName);
        $this->setProductId($aProductId);
        $this->setSprintId($aSprintId);
        $this->setTenantId($aTenantId);
    }

    public function adjustGoals(string $aGoals): void
    {
        $this->setGoals($aGoals);

        // TODO: publish event / student assignment
    }

    public function allCommittedBacklogItems(): array
    {
        return $this->backlogItems();
    }

    public function begins(): DateTime
    {
        return $this->begins;
    }

    public function captureRetrospectiveMeetingResults(string $aRetrospective): void
    {
        $this->setRetrospective($aRetrospective);

        // TODO: publish event / student assignment
    }

    public function commit(BacklogItem $aBacklogItem): void
    {
        $this->assertArgumentEquals($this->tenantId(), $aBacklogItem->tenantId(), 'Must have same tenants.');
        $this->assertArgumentEquals($this->productId(), $aBacklogItem->productId(), 'Must have same products.');

        $ordering = count($this->backlogItems()) + 1;

        $committedBacklogItem = new CommittedBacklogItem(
            $this->tenantId(),
            $this->sprintId(),
            $aBacklogItem->backlogItemId(),
            $ordering
        );

        $this->backlogItems[] = $committedBacklogItem;
    }

    public function ends(): DateTime
    {
        return $this->ends;
    }

    public function goals(): string
    {
        return $this->goals;
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

    public function rename(string $aName): void
    {
        $this->setName($aName);

        // TODO: publish event / student assignment
    }

    public function reorderFrom(BacklogItemId $anId, int $anOrderOfPriority): void
    {
        foreach ($this->backlogItems() as $committedBacklogItem) {
            $committedBacklogItem->reorderFrom($anId, $anOrderOfPriority);
        }
    }

    public function retrospective(): ?string
    {
        return $this->retrospective;
    }

    public function sprintId(): SprintId
    {
        return $this->sprintId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function uncommit(BacklogItem $aBacklogItem): void
    {
        $cbi = new CommittedBacklogItem(
            $this->tenantId(),
            $this->sprintId(),
            $aBacklogItem->backlogItemId()
        );

        $key = array_search($cbi, $this->backlogItems, true);
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
                $this->sprintId()->equals($typedObject->sprintId());
        }

        return $equalObjects;
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (11873 * 53)
            + $this->tenantId()->hashCode()
            + $this->productId()->hashCode()
            + $this->sprintId()->hashCode();

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return "Sprint [tenantId={$this->tenantId}, productId={$this->productId}"
                . ", sprintId={$this->sprintId}, backlogItems=" . count($this->backlogItems)
                . ", begins={$this->begins->format('Y-m-d H:i:s')}, ends={$this->ends->format('Y-m-d H:i:s')}"
                . ", goals={$this->goals}, name={$this->name}"
                . ", retrospective={$this->retrospective}]";
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

    private function setEnds(DateTime $anEnds): void
    {
        $this->assertArgumentNotNull($anEnds, 'The ends must be provided.');

        $this->ends = $anEnds;
    }

    private function setGoals(string $aGoals): void
    {
        if ($aGoals !== null) {
            $this->assertArgumentLength($aGoals, 500, 'The goals must be 500 characters or less.');
        }

        $this->goals = $aGoals;
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

    private function setRetrospective(?string $aRetrospective): void
    {
        if ($aRetrospective !== null) {
            $this->assertArgumentLength($aRetrospective, 5000, 'The goals must be 5000 characters or less.');
        }

        $this->retrospective = $aRetrospective;
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