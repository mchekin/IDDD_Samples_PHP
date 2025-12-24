<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Product\Release\ReleaseId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\DomainEvent;
use DateTime;

class ProductReleaseScheduled implements DomainEvent
{
    private DateTime $begins;
    private string $description;
    private DateTime $ends;
    private int $eventVersion;
    private string $name;
    private DateTime $occurredOn;
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
        $this->begins = $aBegins;
        $this->description = $aDescription;
        $this->ends = $anEnds;
        $this->eventVersion = 1;
        $this->name = $aName;
        $this->occurredOn = new DateTime();
        $this->productId = $aProductId;
        $this->releaseId = $aReleaseId;
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

    public function description(): string
    {
        return $this->description;
    }

    public function ends(): DateTime
    {
        return $this->ends;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function releaseId(): ReleaseId
    {
        return $this->releaseId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }
}