<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\DomainEvent;
use DateTime;

class ProductDiscussionRequested implements DomainEvent
{
    private string $description;
    private int $eventVersion;
    private string $name;
    private DateTime $occurredOn;
    private ProductId $productId;
    private ProductOwnerId $productOwnerId;
    private bool $requestingDiscussion;
    private TenantId $tenantId;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        ProductOwnerId $aProductOwnerId,
        string $aName,
        string $aDescription,
        bool $aRequestingDiscussion
    ) {
        $this->description = $aDescription;
        $this->eventVersion = 1;
        $this->name = $aName;
        $this->occurredOn = new DateTime();
        $this->productId = $aProductId;
        $this->productOwnerId = $aProductOwnerId;
        $this->requestingDiscussion = $aRequestingDiscussion;
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

    public function description(): string
    {
        return $this->description;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function productOwnerId(): ProductOwnerId
    {
        return $this->productOwnerId;
    }

    public function isRequestingDiscussion(): bool
    {
        return $this->requestingDiscussion;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }
}