<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Infrastructure;

use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductCollection;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use Ramsey\Uuid\Uuid;

class InMemoryProductRepository implements ProductRepository
{
    private array $products = [];

    public function allProductsOfTenant(TenantId $aTenantId): ProductCollection
    {
        $products = array_filter($this->products, function (Product $product) use ($aTenantId) {
            return $product->tenantId()->equals($aTenantId);
        });

        return new ProductCollection($products);
    }

    public function nextIdentity(): ProductId
    {
        return new ProductId(Uuid::uuid4()->toString());
    }

    public function productOfDiscussionInitiationId(TenantId $aTenantId, string $aDiscussionInitiationId): Product
    {
        foreach ($this->products as $product) {
            if ($product->tenantId()->equals($aTenantId)
                && $product->discussionInitiationId() === $aDiscussionInitiationId) {
                return $product;
            }
        }

        throw new \RuntimeException('Product not found for discussion initiation id: ' . $aDiscussionInitiationId);
    }

    public function productOfId(TenantId $aTenantId, ProductId $aProductId): Product
    {
        $key = $this->keyFor($aTenantId, $aProductId);
        $product = $this->products[$key] ?? null;

        if ($product === null) {
            throw new \RuntimeException('Product not found: ' . $aProductId->id());
        }

        return $product;
    }

    public function remove(Product $aProduct): void
    {
        $key = $this->keyFor($aProduct->tenantId(), $aProduct->productId());
        unset($this->products[$key]);
    }

    public function removeAll(ProductCollection $aProductCollection): void
    {
        foreach ($aProductCollection as $product) {
            $this->remove($product);
        }
    }

    public function save(Product $aProduct): void
    {
        $key = $this->keyFor($aProduct->tenantId(), $aProduct->productId());
        $this->products[$key] = $aProduct;
    }

    public function saveAll(ProductCollection $aProductCollection): void
    {
        foreach ($aProductCollection as $product) {
            $this->save($product);
        }
    }

    public function clear(): void
    {
        $this->products = [];
    }

    private function keyFor(TenantId $aTenantId, ProductId $aProductId): string
    {
        return $aTenantId->id() . ':' . $aProductId->id();
    }
}
