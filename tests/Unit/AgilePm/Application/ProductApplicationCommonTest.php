<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Application;

use App\AgilePm\Application\Product\ProductApplicationService;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\AgilePm\Infrastructure\InMemoryProductOwnerRepository;
use Tests\Unit\AgilePm\Infrastructure\InMemoryProductRepository;
use Tests\Unit\AgilePm\Infrastructure\InMemoryTimeConstrainedProcessTrackerRepository;

abstract class ProductApplicationCommonTest extends TestCase
{
    protected InMemoryProductRepository $productRepository;
    protected InMemoryProductOwnerRepository $productOwnerRepository;
    protected InMemoryTimeConstrainedProcessTrackerRepository $timeConstrainedProcessTrackerRepository;
    protected ProductApplicationService $productApplicationService;

    protected function setUp(): void
    {
        parent::setUp();

        // Like Java's LevelDBProvider.purge() - start with clean repositories
        $this->productRepository = new InMemoryProductRepository();
        $this->productOwnerRepository = new InMemoryProductOwnerRepository();
        $this->timeConstrainedProcessTrackerRepository = new InMemoryTimeConstrainedProcessTrackerRepository();

        $this->productApplicationService = new ProductApplicationService(
            $this->productRepository,
            $this->productOwnerRepository,
            $this->timeConstrainedProcessTrackerRepository
        );
    }

    protected function tearDown(): void
    {
        // Like Java's LevelDBProvider.purge() - clean up after test
        $this->productRepository->clear();
        $this->productOwnerRepository->clear();
        $this->timeConstrainedProcessTrackerRepository->clear();

        parent::tearDown();
    }

    protected function persistedProductForTest(): Product
    {
        $product = $this->productForTest();

        // Actually save like Java's LevelDBUnitOfWork pattern
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

        // Actually save like Java
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
