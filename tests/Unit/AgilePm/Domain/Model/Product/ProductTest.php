<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCreate()
    {
        $aTenantId = '062be292-2718-44a8-aae6-7612cebaa9b9';
        $aProductId = 'd75af329-03e2-4773-aba1-709abf096af6';
        $aProductOwnerId = '0ee27a2e-1697-4d68-9012-67bb665155c1';
        $aProductName = 'New Product';
        $aProductDescription = 'Description of a new product';

        $tenantId = new TenantId($aTenantId);

        $product = new Product(
            $tenantId,
            new ProductId($aProductId),
            new ProductOwnerId($tenantId, $aProductOwnerId),
            $aProductName,
            $aProductDescription,
            DiscussionAvailability::notRequired()
        );
    }
}