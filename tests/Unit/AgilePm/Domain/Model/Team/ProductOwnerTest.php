<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Domain\Model\Team;

use App\AgilePm\Domain\Model\Product\ProductId;
use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ProductOwnerTest extends TestCase
{
    public function testCreate()
    {
        $aTenantId = '062be292-2718-44a8-aae6-7612cebaa9b9';
        $aUsername = 'john.smith';
        $aFirstName = 'John';
        $aLastName = 'Smith';
        $anEmailAddress = 'john.smith@email.com';

        $productOwner = new ProductOwner(
            new TenantId($aTenantId),
            $aUsername,
            $aFirstName,
            $aLastName,
            $anEmailAddress,
            Carbon::now()
        );
    }
}