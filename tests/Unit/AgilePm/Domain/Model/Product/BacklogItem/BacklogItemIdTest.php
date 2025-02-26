<?php declare(strict_types=1);


namespace Tests\Unit\AgilePm\Domain\Model\Tenant;

use App\AgilePm\Domain\Model\Product\BacklogItem\BacklogItemId;
use PHPUnit\Framework\TestCase;

class BacklogItemIdTest extends TestCase
{
    public function testSetId()
    {
        $anId = '062be292-2718-44a8-aae6-7612cebaa9b9';

        $productId = new BacklogItemId($anId);

        $this->assertSame($anId, $productId->id());
    }
}