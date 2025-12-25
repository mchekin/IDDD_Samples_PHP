<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Application\Product;

use App\AgilePm\Application\Product\InitiateDiscussionCommand;
use App\AgilePm\Application\Product\NewProductCommand;
use App\AgilePm\Application\Product\RequestProductDiscussionCommand;
use App\AgilePm\Application\Product\RetryProductDiscussionRequestCommand;
use App\AgilePm\Application\Product\StartDiscussionInitiationCommand;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use Exception;
use Mockery;
use Ramsey\Uuid\Uuid;
use Tests\Unit\AgilePm\Application\ProductApplicationCommonTest;

class ProductApplicationServiceTest extends ProductApplicationCommonTest
{

    public function testDiscussionProcess(): void
    {
        $product = $this->persistedProductForTest();

        $this->productApplicationService->requestProductDiscussion(
            new RequestProductDiscussionCommand(
                $product->tenantId()->id(),
                $product->productId()->id()
            )
        );

        $this->productApplicationService->startDiscussionInitiation(
            new StartDiscussionInitiationCommand(
                $product->tenantId()->id(),
                $product->productId()->id()
            )
        );

        $productWithStartedDiscussionInitiation = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertNotNull($productWithStartedDiscussionInitiation->discussionInitiationId());

        $discussionId = strtoupper(Uuid::uuid4()->toString());

        $command = new InitiateDiscussionCommand(
            $product->tenantId()->id(),
            $product->productId()->id(),
            $discussionId
        );

        $this->productApplicationService->initiateDiscussion($command);

        $productWithInitiatedDiscussion = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertEquals($discussionId, $productWithInitiatedDiscussion->discussion()->descriptor()->id());
    }

    public function testNewProduct(): void
    {
        $productOwner = $this->persistedProductOwnerForTest();

        $newProductId = $this->productApplicationService->newProduct(
            new NewProductCommand(
                'T-12345',
                $productOwner->productOwnerId()->id(),
                'My Product',
                'The description of My Product.'
            )
        );

        $newProduct = $this->productRepository
            ->productOfId(
                $productOwner->tenantId(),
                new ProductId($newProductId)
            );

        $this->assertNotNull($newProduct);
        $this->assertEquals('My Product', $newProduct->name());
        $this->assertEquals('The description of My Product.', $newProduct->description());
    }

    public function testNewProductWithDiscussion(): void
    {
        $productOwner = $this->persistedProductOwnerForTest();

        $newProductId = $this->productApplicationService->newProductWithDiscussion(
            new NewProductCommand(
                'T-12345',
                $productOwner->productOwnerId()->id(),
                'My Product',
                'The description of My Product.'
            )
        );

        $newProduct = $this->productRepository
            ->productOfId(
                $productOwner->tenantId(),
                new ProductId($newProductId)
            );

        $this->assertNotNull($newProduct);
        $this->assertEquals('My Product', $newProduct->name());
        $this->assertEquals('The description of My Product.', $newProduct->description());
        $this->assertEquals(DiscussionAvailability::REQUESTED, $newProduct->discussion()->availability());
    }

    public function testRequestProductDiscussion(): void
    {
        $product = $this->persistedProductForTest();

        $this->productApplicationService->requestProductDiscussion(
            new RequestProductDiscussionCommand(
                $product->tenantId()->id(),
                $product->productId()->id()
            )
        );

        $productWithRequestedDiscussion = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertEquals(DiscussionAvailability::REQUESTED, $productWithRequestedDiscussion->discussion()->availability());
    }

    public function testRetryProductDiscussionRequest(): void
    {
        $product = $this->persistedProductForTest();

        $this->productApplicationService->requestProductDiscussion(
            new RequestProductDiscussionCommand(
                $product->tenantId()->id(),
                $product->productId()->id()
            )
        );

        $productWithRequestedDiscussion = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertEquals(DiscussionAvailability::REQUESTED, $productWithRequestedDiscussion->discussion()->availability());

        $this->productApplicationService->startDiscussionInitiation(
            new StartDiscussionInitiationCommand(
                $product->tenantId()->id(),
                $product->productId()->id()
            )
        );

        $productWithDiscussionInitiation = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertNotNull($productWithDiscussionInitiation->discussionInitiationId());

        $this->productApplicationService->retryProductDiscussionRequest(
            new RetryProductDiscussionRequestCommand(
                $product->tenantId()->id(),
                $productWithDiscussionInitiation->discussionInitiationId()
            )
        );

        $productWithRetriedRequestedDiscussion = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertEquals(DiscussionAvailability::REQUESTED, $productWithRetriedRequestedDiscussion->discussion()->availability());
        $this->assertEquals($productWithDiscussionInitiation->discussionInitiationId(), $productWithRetriedRequestedDiscussion->discussionInitiationId());
    }

    public function testStartDiscussionInitiation(): void
    {
        $product = $this->persistedProductForTest();

        $this->productApplicationService->requestProductDiscussion(
            new RequestProductDiscussionCommand(
                $product->tenantId()->id(),
                $product->productId()->id()
            )
        );

        $productWithRequestedDiscussion = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertEquals(DiscussionAvailability::REQUESTED, $productWithRequestedDiscussion->discussion()->availability());
        $this->assertNull($productWithRequestedDiscussion->discussionInitiationId());

        $this->productApplicationService->startDiscussionInitiation(
            new StartDiscussionInitiationCommand(
                $product->tenantId()->id(),
                $product->productId()->id()
            )
        );

        $productWithDiscussionInitiation = $this->productRepository
            ->productOfId(
                $product->tenantId(),
                $product->productId()
            );

        $this->assertNotNull($productWithDiscussionInitiation->discussionInitiationId());
    }

    public function testTimeOutProductDiscussionRequest(): void
    {
        // TODO: student assignment
    }
}