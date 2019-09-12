<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Domain\Model\Team;

use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ProductOwnerTest extends TestCase
{
    public function testCreate()
    {
        $aUsername = 'john.smith';
        $aFirstName = 'John';
        $aLastName = 'Smith';
        $anEmailAddress = 'john.smith@email.com';
        $anInitializedOn = Carbon::now();
        $aTenantId = new TenantId('062be292-2718-44a8-aae6-7612cebaa9b9');

        $productOwner = new ProductOwner(
            $aTenantId,
            $aUsername,
            $aFirstName,
            $aLastName,
            $anEmailAddress,
            $anInitializedOn
        );

        $this->assertSame($aTenantId, $productOwner->tenantId());
        $this->assertSame($aUsername, $productOwner->username());
        $this->assertSame($aFirstName, $productOwner->firstName());
        $this->assertSame($aLastName, $productOwner->lastName());
        $this->assertSame($anEmailAddress, $productOwner->emailAddress());
        $this->assertSame($anInitializedOn, $productOwner->initializedOn());
    }
}