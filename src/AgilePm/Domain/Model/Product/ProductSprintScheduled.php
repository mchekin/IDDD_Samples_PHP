<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Product\Sprint\SprintId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\DomainEvent;
use DateTime;

class ProductSprintScheduled implements DomainEvent
{
    private DateTime $begins;
    private DateTime $ends;
    private int $eventVersion;
    private string $goals;
    private string $name;
    private DateTime $occurredOn;
    private ProductId $productId;
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
        $this->begins = $aBegins;
        $this->ends = $anEnds;
        $this->eventVersion = 1;
        $this->goals = $aGoals;
        $this->name = $aName;
        $this->occurredOn = new DateTime();
        $this->productId = $aProductId;
        $this->sprintId = $aSprintId;
        $this->tenantId = $aTenantId;
    }

    public function eventVersion(): int
    {
        return $this->eventVersion;
    }

    public function occurredOn(): DateTime
    {
        return $this->occurredOn;
    }

    public function begins(): DateTime
    {
        return $this->begins;
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

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function sprintId(): SprintId
    {
        return $this->sprintId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }
}