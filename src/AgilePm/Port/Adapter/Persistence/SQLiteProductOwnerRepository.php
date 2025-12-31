<?php declare(strict_types=1);

namespace App\AgilePm\Port\Adapter\Persistence;

use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerCollection;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteProvider;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteUnitOfWork;
use PDO;

/**
 * SQLite-based ProductOwnerRepository - matches Java's LevelDBProductOwnerRepository
 * Uses UnitOfWork pattern for transaction management and identity map
 */
class SQLiteProductOwnerRepository implements ProductOwnerRepository
{
    public function __construct(string $databasePath)
    {
        // Ensure database is initialized
        SQLiteProvider::instance()->databaseFrom($databasePath);
    }

    public function allProductOwnersOfTenant(TenantId $aTenantId): ProductOwnerCollection
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $productOwners = [];

        // Query all product owners for this tenant
        $stmt = $unitOfWork->database()->prepare(
            'SELECT key, data FROM entities WHERE class = ? AND key LIKE ?'
        );
        $stmt->execute([
            ProductOwner::class,
            $aTenantId->id() . ':%'
        ]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row) || !isset($row['data']) || !is_string($row['data'])) {
                continue;
            }
            /** @var ProductOwner $productOwner */
            $productOwner = $unitOfWork->deserializeFromJson($row['data'], ProductOwner::class);
            if ($productOwner->tenantId()->equals($aTenantId)) {
                $productOwners[] = $productOwner;
            }
        }

        return new ProductOwnerCollection($productOwners);
    }

    public function productOwnerOfIdentity(TenantId $aTenantId, string $aUsername): ProductOwner
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aTenantId, $aUsername);

        $productOwner = $unitOfWork->read($key, ProductOwner::class);

        if ($productOwner === null) {
            throw new \RuntimeException('ProductOwner not found: ' . $aUsername);
        }

        assert($productOwner instanceof ProductOwner);
        return $productOwner;
    }

    public function remove(ProductOwner $aProductOwner): void
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aProductOwner->tenantId(), $aProductOwner->productOwnerId()->id());

        $unitOfWork->remove($key);
    }

    public function removeAll(ProductOwnerCollection $aProductOwnerCollection): void
    {
        foreach ($aProductOwnerCollection as $productOwner) {
            $this->remove($productOwner);
        }
    }

    public function save(ProductOwner $aProductOwner): void
    {
        $unitOfWork = SQLiteUnitOfWork::current();
        $key = $this->keyFor($aProductOwner->tenantId(), $aProductOwner->productOwnerId()->id());

        $unitOfWork->write($key, $aProductOwner);
    }

    public function saveAll(ProductOwnerCollection $aProductOwnerCollection): void
    {
        foreach ($aProductOwnerCollection as $productOwner) {
            $this->save($productOwner);
        }
    }

    private function keyFor(TenantId $aTenantId, string $aUsername): string
    {
        return $aTenantId->id() . ':' . $aUsername;
    }
}
