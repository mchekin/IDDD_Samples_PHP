<?php declare(strict_types=1);

namespace App\AgilePm\Port\Adapter\Persistence;

use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductCollection;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteProvider;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteUnitOfWork;
use PDO;
use Ramsey\Uuid\Uuid;

/**
 * SQLite-based ProductRepository - matches Java's LevelDBProductRepository
 * Uses UnitOfWork pattern for transaction management and identity map
 */
class SQLiteProductRepository implements ProductRepository
{
    public function __construct(string $databasePath)
    {
        // Ensure database is initialized
        SQLiteProvider::instance()->databaseFrom($databasePath);
    }

    public function allProductsOfTenant(TenantId $aTenantId): ProductCollection
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $products = [];

        // Query all products for this tenant
        $stmt = $unitOfWork->database()->prepare(
            'SELECT key, data FROM entities WHERE class = ? AND key LIKE ?'
        );
        $stmt->execute([
            Product::class,
            $aTenantId->id() . ':%'
        ]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row) || !isset($row['data']) || !is_string($row['data'])) {
                continue;
            }
            /** @var Product $product */
            $product = $unitOfWork->deserializeFromJson($row['data'], Product::class);
            if ($product->tenantId()->equals($aTenantId)) {
                $products[] = $product;
            }
        }

        return new ProductCollection($products);
    }

    public function nextIdentity(): ProductId
    {
        return new ProductId(Uuid::uuid4()->toString());
    }

    public function productOfDiscussionInitiationId(TenantId $aTenantId, string $aDiscussionInitiationId): Product
    {
        $unitOfWork = SQLiteUnitOfWork::current();

        // Query all products for this tenant and filter by discussion initiation id
        $stmt = $unitOfWork->database()->prepare(
            'SELECT key, data FROM entities WHERE class = ? AND key LIKE ?'
        );
        $stmt->execute([
            Product::class,
            $aTenantId->id() . ':%'
        ]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row) || !isset($row['data']) || !is_string($row['data'])) {
                continue;
            }
            /** @var Product $product */
            $product = $unitOfWork->deserializeFromJson($row['data'], Product::class);
            if ($product->tenantId()->equals($aTenantId)
                && $product->discussionInitiationId() === $aDiscussionInitiationId) {
                return $product;
            }
        }

        throw new \RuntimeException('Product not found for discussion initiation id: ' . $aDiscussionInitiationId);
    }

    public function productOfId(TenantId $aTenantId, ProductId $aProductId): Product
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aTenantId, $aProductId);

        $product = $unitOfWork->read($key, Product::class);

        if ($product === null) {
            throw new \RuntimeException('Product not found: ' . $aProductId->id());
        }

        assert($product instanceof Product);
        return $product;
    }

    public function remove(Product $aProduct): void
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aProduct->tenantId(), $aProduct->productId());

        $unitOfWork->remove($key);
    }

    public function removeAll(ProductCollection $aProductCollection): void
    {
        foreach ($aProductCollection as $product) {
            $this->remove($product);
        }
    }

    public function save(Product $aProduct): void
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aProduct->tenantId(), $aProduct->productId());

        $unitOfWork->write($key, $aProduct);
    }

    public function saveAll(ProductCollection $aProductCollection): void
    {
        foreach ($aProductCollection as $product) {
            $this->save($product);
        }
    }

    private function keyFor(TenantId $aTenantId, ProductId $aProductId): string
    {
        return $aTenantId->id() . ':' . $aProductId->id();
    }
}
