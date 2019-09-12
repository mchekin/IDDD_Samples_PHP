<?php declare(strict_types=1);


namespace Tests\Unit\AgilePm\Domain\Model\Tenant;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use PHPUnit\Framework\TestCase;

class TenantIdTest extends TestCase
{
    public function testSetId()
    {
        $anId = '062be292-2718-44a8-aae6-7612cebaa9b9';

        $tenantId = new TenantId($anId);

        $this->assertEquals($anId, $tenantId->id());
    }
}