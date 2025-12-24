<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\DomainEvent;
use DateTime;

class ProductDiscussionInitiated implements DomainEvent
{
    private int $eventVersion;
    private DateTime $occurredOn;
    private ProductDiscussion $productDiscussion;
    private ProductId $productId;
    private TenantId $tenantId;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        ProductDiscussion $aProductDiscussion
    ) {
        $this->eventVersion = 1;
        $this->occurredOn = new DateTime();
        $this->productDiscussion = $aProductDiscussion;
        $this->productId = $aProductId;
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

    public function productDiscussion(): ProductDiscussion
    {
        return $this->productDiscussion;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }
}