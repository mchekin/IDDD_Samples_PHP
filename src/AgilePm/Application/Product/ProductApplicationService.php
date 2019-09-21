<?php declare(strict_types=1);


namespace App\AgilePm\Application\Product;


use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;

class ProductApplicationService
{
    /**
     * @var ProductRepository
     */
    private $aProductRepository;

    /**
     * @var ProductOwnerRepository
     */
    private $aProductOwnerRepository;

    /**
     * @var TimeConstrainedProcessTrackerRepository
     */
    private $aProcessTrackerRepository;

    public function __construct(
        ProductRepository $aProductRepository,
        ProductOwnerRepository $aProductOwnerRepository,
        TimeConstrainedProcessTrackerRepository $aProcessTrackerRepository
    ) {
        $this->aProductRepository = $aProductRepository;
        $this->aProductOwnerRepository = $aProductOwnerRepository;
        $this->aProcessTrackerRepository = $aProcessTrackerRepository;
    }
}