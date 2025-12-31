<?php

declare(strict_types=1);

namespace App\AgilePm\Application\Product;


use App\AgilePm\Application\ApplicationServiceLifeCycle;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Discussion\DiscussionDescriptor;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductDiscussionRequestTimedOut;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Product\ProductRepository;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerRepository;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\Process\ProcessId;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTracker;
use App\Common\Domain\Model\Process\TimeConstrainedProcessTrackerRepository;
use DateTime;
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

    public function initiateDiscussion(InitiateDiscussionCommand $aCommand): void
    {
        ApplicationServiceLifeCycle::begin();

        try {
            $product = $this->productRepository()
                ->productOfId(
                    new TenantId($aCommand->getTenantId()),
                    new ProductId($aCommand->getProductId())
                );

            $product->initiateDiscussion(new DiscussionDescriptor($aCommand->getDiscussionId()));

            $this->productRepository()->save($product);

            $discussionInitiationId = $product->discussionInitiationId();
            if ($discussionInitiationId === null) {
                throw new \InvalidArgumentException('Discussion initiation ID is required');
            }

            $processId = ProcessId::existingProcessId($discussionInitiationId);

            $tracker = $this->processTrackerRepository()
                ->trackerOfProcessId($aCommand->getTenantId(), $processId);

            $tracker->completed();

            $this->processTrackerRepository()->save($tracker);

            ApplicationServiceLifeCycle::success();
        } catch (RuntimeException $e) {
            ApplicationServiceLifeCycle::fail($e);
        }
    }

    public function newProduct(NewProductCommand $aCommand): string
    {
        return $this->newProductWith(
            $aCommand->getTenantId(),
            $aCommand->getProductOwnerId(),
            $aCommand->getName(),
            $aCommand->getDescription(),
            DiscussionAvailability::NOT_REQUESTED
        );
    }

    public function newProductWithDiscussion(NewProductCommand $aCommand): string
    {
        return $this->newProductWith(
            $aCommand->getTenantId(),
            $aCommand->getProductOwnerId(),
            $aCommand->getName(),
            $aCommand->getDescription(),
            $this->requestDiscussionIfAvailable()
        );
    }

    public function requestProductDiscussion(RequestProductDiscussionCommand $aCommand): void
    {
        ApplicationServiceLifeCycle::begin();

        try {
            $product = $this->productRepository()
                ->productOfId(
                    new TenantId($aCommand->getTenantId()),
                    new ProductId($aCommand->getProductId())
                );

            $product->requestDiscussion($this->requestDiscussionIfAvailable());

            $this->productRepository()->save($product);

            ApplicationServiceLifeCycle::success();
        } catch (RuntimeException $e) {
            ApplicationServiceLifeCycle::fail($e);
        }
    }

    public function retryProductDiscussionRequest(RetryProductDiscussionRequestCommand $aCommand): void
    {
        ApplicationServiceLifeCycle::begin();

        try {
            $processId = ProcessId::existingProcessId($aCommand->getProcessId());
            $tenantId = new TenantId($aCommand->getTenantId());

            $product = $this->productRepository()
                ->productOfDiscussionInitiationId(
                    $tenantId,
                    $processId->id()
                );

            $product->requestDiscussion($this->requestDiscussionIfAvailable());

            $this->productRepository()->save($product);

            ApplicationServiceLifeCycle::success();
        } catch (RuntimeException $e) {
            ApplicationServiceLifeCycle::fail($e);
        }
    }

    public function startDiscussionInitiation(StartDiscussionInitiationCommand $aCommand): void
    {
        ApplicationServiceLifeCycle::begin();

        try {
            $product = $this->productRepository()
                ->productOfId(
                    new TenantId($aCommand->getTenantId()),
                    new ProductId($aCommand->getProductId())
                );

            $tracker = $this->processTrackerOfProduct($product);

            $this->processTrackerRepository()->save($tracker);

            $product->startDiscussionInitiation($tracker->processId()->id());

            $this->productRepository()->save($product);

            ApplicationServiceLifeCycle::success();
        } catch (RuntimeException $e) {
            ApplicationServiceLifeCycle::fail($e);
        }
    }

    public function timeOutProductDiscussionRequest(TimeOutProductDiscussionRequestCommand $aCommand): void
    {
        ApplicationServiceLifeCycle::begin();

        try {
            $processId = ProcessId::existingProcessId($aCommand->getProcessId());
            $tenantId = new TenantId($aCommand->getTenantId());

            $product = $this->productRepository()
                ->productOfDiscussionInitiationId(
                    $tenantId,
                    $processId->id()
                );

            $this->sendEmailForTimedOutProcess($product);

            $product->failDiscussionInitiation();

            $this->productRepository()->save($product);

            ApplicationServiceLifeCycle::success();
        } catch (RuntimeException $e) {
            ApplicationServiceLifeCycle::fail($e);
        }
    }

    private function newProductWith(
        string $aTenantId,
        string $aProductOwnerId,
        string $aName,
        string $aDescription,
        DiscussionAvailability $aDiscussionAvailability
    ): string {
        $tenantId = new TenantId($aTenantId);

        ApplicationServiceLifeCycle::begin();

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

            return $productId->id();
        } catch (RuntimeException $exception) {
            //ApplicationServiceLifeCycle.fail(e);

            throw $exception;
        }
    }

    private function sendEmailForTimedOutProcess(Product $aProduct): void
    {
        // TODO: Implement
    }

    private function requestDiscussionIfAvailable(): DiscussionAvailability
    {
        $availability = DiscussionAvailability::ADD_ON_NOT_ENABLED;
        $enabled = true; // TODO: determine add-on enabled

        /** @phpstan-ignore-next-line if.alwaysTrue (TODO: implement add-on detection) */
        if ($enabled) {
            $availability = DiscussionAvailability::REQUESTED;
        }

        return $availability;
    }

    private function processTrackerRepository(): TimeConstrainedProcessTrackerRepository
    {
        return $this->processTrackerRepository;
    }

    /** @phpstan-ignore-next-line method.unused (kept for future use) */
    private function productOwnerRepository(): ProductOwnerRepository
    {
        return $this->productOwnerRepository;
    }

    private function productRepository(): ProductRepository
    {
        return $this->productRepository;
    }

    /** @phpstan-ignore-next-line method.unused (kept for future use) */
    private function requestProductDiscussionFor(Product $aProduct): void
    {
        $aProduct->requestDiscussion($this->requestDiscussionIfAvailable());

        $this->productRepository()->save($aProduct);
    }

    private function processTrackerOfProduct(Product $aProduct): TimeConstrainedProcessTracker
    {
        $tracker = null;

        if ($aProduct->discussionInitiationId() !== null) {
            $processId = ProcessId::existingProcessId($aProduct->discussionInitiationId());

            $tracker = $this->processTrackerRepository()
                ->trackerOfProcessId($aProduct->tenantId()->id(), $processId);
        } else {
            $timedOutEventName = ProductDiscussionRequestTimedOut::class;

            $tracker = new TimeConstrainedProcessTracker(
                $aProduct->tenantId()->id(),
                ProcessId::newProcessId(),
                'Create discussion for product: ' . $aProduct->name(),
                new DateTime(),
                5 * 60 * 1000, // retries every 5 minutes
                3, // 3 total retries
                $timedOutEventName
            );
        }

        return $tracker;
    }
}
