<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Product;


use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;

class Product
{
    /**
     * @var TenantId
     */
    private $aTenantId;
    /**
     * @var ProductId
     */
    private $aProductId;
    /**
     * @var ProductOwnerId
     */
    private $aProductOwnerId;
    /**
     * @var string
     */
    private $aName;
    /**
     * @var string
     */
    private $aDescription;
    /**
     * @var DiscussionAvailability
     */
    private $aDiscussionAvailability;

    public function __construct(
        TenantId $aTenantId,
        ProductId $aProductId,
        ProductOwnerId $aProductOwnerId,
        string $aName,
        string $aDescription,
        DiscussionAvailability $aDiscussionAvailability
    )
    {
        $this->aTenantId = $aTenantId;
        $this->aProductId = $aProductId;
        $this->aProductOwnerId = $aProductOwnerId;
        $this->aName = $aName;
        $this->aDescription = $aDescription;
        $this->aDiscussionAvailability = $aDiscussionAvailability;
    }

    public function tenantId(): TenantId
    {
        return $this->aTenantId;
    }

    public function productId(): ProductId
    {
        return $this->aProductId;
    }

    public function productOwnerId(): ProductOwnerId
    {
        return $this->aProductOwnerId;
    }

    public function name(): string
    {
        return $this->aName;
    }

    public function description(): string
    {
        return $this->aDescription;
    }

    public function discussionAvailability(): DiscussionAvailability
    {
        return $this->aDiscussionAvailability;
    }
}