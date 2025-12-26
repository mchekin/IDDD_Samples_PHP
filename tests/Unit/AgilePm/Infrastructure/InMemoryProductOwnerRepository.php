<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Infrastructure;

use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerCollection;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;

class InMemoryProductOwnerRepository implements ProductOwnerRepository
{
    private array $productOwners = [];

    public function allProductOwnersOfTenant(TenantId $aTenantId): ProductOwnerCollection
    {
        $productOwners = array_filter($this->productOwners, function (ProductOwner $productOwner) use ($aTenantId) {
            return $productOwner->tenantId()->equals($aTenantId);
        });

        return new ProductOwnerCollection($productOwners);
    }

    public function productOwnerOfIdentity(TenantId $aTenantId, string $aUsername): ProductOwner
    {
        $key = $this->keyFor($aTenantId, $aUsername);
        $productOwner = $this->productOwners[$key] ?? null;

        if ($productOwner === null) {
            throw new \RuntimeException('ProductOwner not found: ' . $aUsername);
        }

        return $productOwner;
    }

    public function remove(ProductOwner $aProductOwner): void
    {
        $key = $this->keyFor($aProductOwner->tenantId(), $aProductOwner->productOwnerId()->id());
        unset($this->productOwners[$key]);
    }

    public function removeAll(ProductOwnerCollection $aProductOwnerCollection): void
    {
        foreach ($aProductOwnerCollection as $productOwner) {
            $this->remove($productOwner);
        }
    }

    public function save(ProductOwner $aProductOwner): void
    {
        $key = $this->keyFor($aProductOwner->tenantId(), $aProductOwner->productOwnerId()->id());
        $this->productOwners[$key] = $aProductOwner;
    }

    public function saveAll(ProductOwnerCollection $aProductOwnerCollection): void
    {
        foreach ($aProductOwnerCollection as $productOwner) {
            $this->save($productOwner);
        }
    }

    public function clear(): void
    {
        $this->productOwners = [];
    }

    private function keyFor(TenantId $aTenantId, string $aUsername): string
    {
        return $aTenantId->id() . ':' . $aUsername;
    }
}
