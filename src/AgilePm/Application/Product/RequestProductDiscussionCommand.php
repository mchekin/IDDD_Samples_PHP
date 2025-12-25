<?php declare(strict_types=1);

namespace App\AgilePm\Application\Product;

class RequestProductDiscussionCommand
{
    private string $tenantId;
    private string $productId;

    public function __construct(string $tenantId = '', string $productId = '')
    {
        $this->tenantId = $tenantId;
        $this->productId = $productId;
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
}