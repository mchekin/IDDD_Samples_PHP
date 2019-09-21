<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;

interface ProductOwnerRepository
{
    public function allProductOwnersOfTenant(TenantId $aTenantId): ProductOwnerCollection;

    public function productOwnerOfIdentity(TenantId $aTenantId, string $aUsername): ProductOwner;

    public function remove(ProductOwner $aProductOwner): void;

    public function removeAll(ProductOwnerCollection $aProductOwnerCollection): void;

    public function save(ProductOwner $aProductOwner): void;

    public function saveAll(ProductOwnerCollection $aProductOwnerCollection): void;
}