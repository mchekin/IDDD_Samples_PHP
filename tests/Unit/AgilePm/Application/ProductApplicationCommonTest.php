<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Application;

use App\AgilePm\Application\Product\ProductApplicationService;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\AgilePm\Port\Adapter\Persistence\SQLiteDatabasePath;
use App\AgilePm\Port\Adapter\Persistence\SQLiteProductOwnerRepository;
use App\AgilePm\Port\Adapter\Persistence\SQLiteProductRepository;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteProvider;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteTimeConstrainedProcessTrackerRepository;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteUnitOfWork;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use PDO;

abstract class ProductApplicationCommonTest extends TestCase
{
    protected SQLiteProductRepository $productRepository;
    protected SQLiteProductOwnerRepository $productOwnerRepository;
    protected SQLiteTimeConstrainedProcessTrackerRepository $timeConstrainedProcessTrackerRepository;
    protected ProductApplicationService $productApplicationService;
    protected PDO $database;

    protected function setUp(): void
    {
        parent::setUp();

        // Like Java's LevelDBProvider - get database and purge before test
        $databasePath = SQLiteDatabasePath::agilePMTestPath();

        // Close any existing connection first
        SQLiteProvider::instance()->close($databasePath);

        // Delete the database file to ensure clean state (avoid locks)
        if (file_exists($databasePath)) {
            @unlink($databasePath);
        }

        // Open fresh database
        $this->database = SQLiteProvider::instance()->databaseFrom($databasePath);

        // Start UnitOfWork for the test (like Java's ApplicationServiceLifeCycle)
        SQLiteUnitOfWork::start($this->database);

        // Create repositories with SQLite backend (like Java's LevelDB repositories)
        $this->productRepository = new SQLiteProductRepository($databasePath);
        $this->productOwnerRepository = new SQLiteProductOwnerRepository($databasePath);
        $this->timeConstrainedProcessTrackerRepository = new SQLiteTimeConstrainedProcessTrackerRepository($databasePath);

        $this->productApplicationService = new ProductApplicationService(
            $this->productRepository,
            $this->productOwnerRepository,
            $this->timeConstrainedProcessTrackerRepository
        );
    }

    protected function tearDown(): void
    {
        // Commit or rollback the UnitOfWork at the end of the test
        try {
            $unitOfWork = SQLiteUnitOfWork::current();
            try {
                $unitOfWork->commit();
            } catch (\Exception $e) {
                // If commit fails, rollback
                try {
                    $unitOfWork->rollback();
                } catch (\Exception $rollbackException) {
                    // Ignore rollback failures
                }
            }
        } catch (\RuntimeException $e) {
            // No active UnitOfWork, that's fine
        }

        // Close the database connection
        SQLiteProvider::instance()->close(SQLiteDatabasePath::agilePMTestPath());

        parent::tearDown();
    }

    protected function persistedProductForTest(): Product
    {
        $product = $this->productForTest();

        // Start UnitOfWork if not already started, and keep it active for the whole test
        try {
            SQLiteUnitOfWork::current();
        } catch (\RuntimeException $e) {
            SQLiteUnitOfWork::start($this->database);
        }

        $this->productRepository->save($product);

        return $product;
    }

    protected function persistedProductOwnerForTest(): ProductOwner
    {
        $tenantId = new TenantId('T-12345');
        $productOwnerId = new ProductOwnerId($tenantId, 'zoe');

        $productOwner = new ProductOwner(
            $tenantId,
            'zoe',
            'Zoe',
            'Doe',
            'zoe@saasovation.com',
            new DateTime('-30 days')
        );

        // Start UnitOfWork if not already started, and keep it active for the whole test
        try {
            SQLiteUnitOfWork::current();
        } catch (\RuntimeException $e) {
            SQLiteUnitOfWork::start($this->database);
        }

        $this->productOwnerRepository->save($productOwner);

        return $productOwner;
    }

    protected function productForTest(): Product
    {
        $tenantId = new TenantId('T12345');
        $productId = new ProductId(Uuid::uuid4()->toString());
        $productOwnerId = new ProductOwnerId($tenantId, 'zdoe');

        $product = new Product(
            $tenantId,
            $productId,
            $productOwnerId,
            'My Product',
            'This is the description of my product.',
            DiscussionAvailability::NOT_REQUESTED
        );

        return $product;
    }
}
