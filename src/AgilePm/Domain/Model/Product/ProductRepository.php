<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Product;


use App\AgilePm\Domain\Model\Tenant\TenantId;

interface ProductRepository
{
    public function allProductsOfTenant(TenantId $aTenantId): ProductCollection;

    public function nextIdentity(): ProductId;

    public function productOfDiscussionInitiationId(TenantId $aTenantId, String $aDiscussionInitiationId): Product;

    public function productOfId(TenantId $aTenantId, ProductId $aProductId): Product;

    public function remove(Product $aProduct): void;

    public function removeAll(ProductCollection $aProductCollection): void;

    public function save(Product $aProduct): void;

    public function saveAll(ProductCollection $aProductCollection): void;
}