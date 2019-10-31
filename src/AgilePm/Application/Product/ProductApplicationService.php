<?php declare(strict_types=1);


namespace App\AgilePm\Application\Product;


use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;
use RuntimeException;

class ProductApplicationService
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductOwnerRepository
     */
    private $productOwnerRepository;

    /**
     * @var TimeConstrainedProcessTrackerRepository
     */
    private $processTrackerRepository;

    public function __construct(
        ProductRepository $aProductRepository,
        ProductOwnerRepository $aProductOwnerRepository,
        TimeConstrainedProcessTrackerRepository $aProcessTrackerRepository
    ) {
        $this->productRepository = $aProductRepository;
        $this->productOwnerRepository = $aProductOwnerRepository;
        $this->processTrackerRepository = $aProcessTrackerRepository;
    }

    public function newProduct(NewProductCommand $aCommand): string
    {
        return $this->newProductWith(
            $aCommand->getTenantId(),
            $aCommand->getProductOwnerId(),
            $aCommand->getName(),
            $aCommand->getDescription(),
            DiscussionAvailability::notRequested()
        );
    }

    private function newProductWith(
        string $aTenantId,
        string $aProductOwnerId,
        string $aName,
        string $aDescription,
        DiscussionAvailability $aDiscussionAvailability
    ) {
        $tenantId = new TenantId($aTenantId);
        $productId = null;

        // ApplicationServiceLifeCycle.begin();

        try {
            $productId = $this->productRepository->nextIdentity();

            $productOwner = $this->productOwnerRepository->productOwnerOfIdentity($tenantId, $aProductOwnerId);

            $product = new Product(
                $tenantId,
                $productId,
                $productOwner->productOwnerId(),
                $aName,
                $aDescription,
                $aDiscussionAvailability
            );

            $this->productRepository->save($product);

            //ApplicationServiceLifeCycle.success();

        } catch (RuntimeException $exception) {
            //ApplicationServiceLifeCycle.fail(e);
        }

        return $productId->id();
    }
}