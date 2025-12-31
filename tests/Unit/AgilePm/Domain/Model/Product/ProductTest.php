<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Product\Product;
use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCreate(): void
    {
        $aTenantId = new TenantId('062be292-2718-44a8-aae6-7612cebaa9b9');
        $aProductId = new ProductId('d75af329-03e2-4773-aba1-709abf096af6');
        $aProductOwnerId = new ProductOwnerId($aTenantId, '0ee27a2e-1697-4d68-9012-67bb665155c1');
        $aProductName = 'New Product';
        $aProductDescription = 'Description of a new product';
        $aDiscussionAvailability = DiscussionAvailability::NOT_REQUESTED;

        $product = new Product(
            $aTenantId,
            $aProductId,
            $aProductOwnerId,
            $aProductName,
            $aProductDescription,
            $aDiscussionAvailability
        );

        self::assertSame($aTenantId, $product->tenantId());
        self::assertSame($aProductId, $product->productId());
        self::assertSame($aProductOwnerId, $product->productOwnerId());
        self::assertSame($aProductName, $product->name());
        self::assertSame($aProductDescription, $product->description());
        self::assertSame($aDiscussionAvailability, $product->discussion()->availability());
    }
}