<?php declare(strict_types=1);


namespace Tests\Unit\AgilePm\Domain\Model\Team;

use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use PHPUnit\Framework\TestCase;

class ProductOwnerIdTest extends TestCase
{
    public function testSetId()
    {
        $anId = '062be292-2718-44a8-aae6-7612cebaa9b9';
        $aTenantId = new TenantId('503ad704-7140-4dda-b097-47c233a58c47');

        $productOwnerId = new ProductOwnerId($aTenantId, $anId);

        $this->assertSame($anId, $productOwnerId->id());
        $this->assertSame($aTenantId, $productOwnerId->tenantId());
    }
}