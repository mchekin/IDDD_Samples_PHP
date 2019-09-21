<?php declare(strict_types=1);


namespace Tests\Unit\AgilePm\Application;

use App\AgilePm\Application\Product\ProductApplicationService;
use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductApplicationServiceTest extends TestCase
{
    /** @var ProductApplicationService */
    private $sut;

    /** @var MockObject */
    private $aProductRepository;

    /** @var MockObject */
    private $aProductOwnerRepository;

    /** @var MockObject */
    private $aProcessTrackerRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aProductRepository = $this->createMock(ProductRepository::class);
        $this->aProductOwnerRepository = $this->createMock(ProductOwnerRepository::class);
        $this->aProcessTrackerRepository = $this->createMock(TimeConstrainedProcessTrackerRepository::class);

        $this->sut = new ProductApplicationService(
            $this->aProductRepository,
            $this->aProductOwnerRepository,
            $this->aProcessTrackerRepository
        );
    }

    public function test()
    {
        $this->assertTrue(true);
    }
}