<?php declare(strict_types=1);


namespace Tests\Unit\AgilePm\Application\Product;

use App\AgilePm\Application\Product\NewProductCommand;
use App\AgilePm\Application\Product\ProductApplicationService;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailabilityNotRequested;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;
use Exception;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ProductApplicationServiceTest extends TestCase
{
    /** @var ProductRepository|MockInterface */
    private $productRepository;

    /** @var ProductOwnerRepository|MockInterface */
    private $productOwnerRepository;

    /** @var TimeConstrainedProcessTrackerRepository|MockInterface */
    private $processTrackerRepository;

    /** @var NewProductCommand|MockInterface */
    private $newProductCommand;

    /** @var ProductId|MockInterface */
    private $productId;

    /** @var ProductOwner|MockInterface */
    private $productOwner;

    /** @var ProductOwnerId|MockInterface */
    private $productOwnerId;

    /** @var ProductApplicationService */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->productOwnerRepository = Mockery::mock(ProductOwnerRepository::class);
        $this->processTrackerRepository = Mockery::mock(TimeConstrainedProcessTrackerRepository::class);
        $this->newProductCommand = Mockery::mock(NewProductCommand::class);
        $this->productId = Mockery::mock(ProductId::class);
        $this->productOwner = Mockery::mock(ProductOwner::class);
        $this->productOwnerId = Mockery::mock(ProductOwnerId::class);

        $this->sut = new ProductApplicationService(
            $this->productRepository,
            $this->productOwnerRepository,
            $this->processTrackerRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testCreatesNewProduct()
    {
        $tenantId = Uuid::uuid4()->toString();
        $productOwnerId = Uuid::uuid4()->toString();
        $name = 'Self-heating socks';
        $description = 'A pair of self-heating socks.';
        $newProductId = Uuid::uuid4()->toString();

        $this->newProductCommand->expects('getTenantId')->andReturn($tenantId);
        $this->newProductCommand->expects('getProductOwnerId')->andReturn($productOwnerId);
        $this->newProductCommand->expects('getName')->andReturn($name);
        $this->newProductCommand->expects('getDescription')->andReturn($description);

        $this->productRepository->expects('nextIdentity')->andReturn($this->productId);

        $this->productOwnerRepository->expects('productOwnerOfIdentity')
            ->with(\Mockery::on(function (TenantId $argument) use ($tenantId) {
                return $tenantId === $argument->id();
            }), $productOwnerId)
            ->andReturn($this->productOwner);

        $this->productOwner->expects('productOwnerId')->andReturn($this->productOwnerId);

        $this->productRepository->expects('save')
            ->with(\Mockery::on(function (Product $argument) use ($tenantId, $name, $description) {
                return $this->productId === $argument->productId()
                    && $this->productOwnerId === $argument->productOwnerId()
                    && $tenantId === $argument->tenantId()->id()
                    && $name === $argument->name()
                    && $description === $argument->description()
                    && get_class($argument->discussionAvailability()) === DiscussionAvailabilityNotRequested::class;
            }))
            ->andReturn($this->productOwner);

        $this->productId->expects('id')->andReturn($newProductId);

        $this->assertSame($newProductId, $this->sut->newProduct($this->newProductCommand));
    }
}