<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Application;

use App\AgilePm\Application\Product\ProductApplicationService;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;
use DateTime;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

abstract class ProductApplicationCommonTest extends TestCase
{
    /** @var ProductRepository|MockInterface */
    protected $productRepository;
    
    /** @var ProductOwnerRepository|MockInterface */
    protected $productOwnerRepository;
    
    /** @var TimeConstrainedProcessTrackerRepository|MockInterface */
    protected $timeConstrainedProcessTrackerRepository;
    
    protected ProductApplicationService $productApplicationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->productOwnerRepository = Mockery::mock(ProductOwnerRepository::class);
        $this->timeConstrainedProcessTrackerRepository = Mockery::mock(TimeConstrainedProcessTrackerRepository::class);

        $this->productApplicationService = new ProductApplicationService(
            $this->productRepository,
            $this->productOwnerRepository,
            $this->timeConstrainedProcessTrackerRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function persistedProductForTest(): Product
    {
        $product = $this->productForTest();
        
        $this->productRepository->expects('save')
            ->with($product);
            
        return $product;
    }

    protected function persistedProductOwnerForTest(): ProductOwner
    {
        $tenantId = new TenantId('T-12345');
        $productOwnerId = new ProductOwnerId($tenantId, 'zoe');
        
        $productOwner = Mockery::mock(ProductOwner::class);
        $productOwner->shouldReceive('tenantId')->andReturn($tenantId);
        $productOwner->shouldReceive('productOwnerId')->andReturn($productOwnerId);
        $productOwner->shouldReceive('firstName')->andReturn('Zoe');
        $productOwner->shouldReceive('lastName')->andReturn('Doe');
        $productOwner->shouldReceive('emailAddress')->andReturn('zoe@saasovation.com');

        $this->productOwnerRepository->expects('save')
            ->with($productOwner);

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