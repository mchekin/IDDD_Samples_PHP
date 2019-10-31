<?php declare(strict_types=1);


namespace Tests\Unit\AgilePm\Application\Product;

use App\AgilePm\Application\Product\NewProductCommand;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class NewProductCommandTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testData()
    {
        $tenantId = Uuid::uuid4()->toString();
        $productOwnerId = Uuid::uuid4()->toString();
        $name = 'Amazing new product';
        $description = 'This product is just amazing!';

        $newProductCommand = new NewProductCommand(
            $tenantId,
            $productOwnerId,
            $name,
            $description
        );

        $this->assertSame($tenantId, $newProductCommand->getTenantId());
        $this->assertSame($productOwnerId, $newProductCommand->getProductOwnerId());
        $this->assertSame($name, $newProductCommand->getName());
        $this->assertSame($description, $newProductCommand->getDescription());
    }
}