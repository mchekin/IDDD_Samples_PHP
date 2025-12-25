<?php declare(strict_types=1);

namespace App\AgilePm\Application\Product;

class InitiateDiscussionCommand
{
    private string $tenantId;
    private string $productId;
    private string $discussionId;

    public function __construct(string $tenantId = '', string $productId = '', string $discussionId = '')
    {
        $this->tenantId = $tenantId;
        $this->productId = $productId;
        $this->discussionId = $discussionId;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function setTenantId(string $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getDiscussionId(): string
    {
        return $this->discussionId;
    }

    public function setDiscussionId(string $discussionId): void
    {
        $this->discussionId = $discussionId;
    }
}