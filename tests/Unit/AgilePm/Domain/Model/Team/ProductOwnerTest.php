<?php declare(strict_types=1);

namespace Tests\Unit\AgilePm\Domain\Model\Team;

use App\AgilePm\Domain\Model\Team\ProductOwner;
use App\AgilePm\Domain\Model\Team\ProductOwnerId;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use Carbon\Carbon;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ProductOwnerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreate(): void
    {
        $aUsername = 'john.smith';
        $aFirstName = 'John';
        $aLastName = 'Smith';
        $anEmailAddress = 'john.smith@email.com';
        $anInitializedOn = Carbon::now();
        $aTenantId = new TenantId(Uuid::uuid4()->toString());

        $productOwner = new ProductOwner(
            $aTenantId,
            $aUsername,
            $aFirstName,
            $aLastName,
            $anEmailAddress,
            $anInitializedOn
        );

        self::assertSame($aTenantId, $productOwner->tenantId());
        self::assertSame($aUsername, $productOwner->username());
        self::assertEquals(new ProductOwnerId($aTenantId, $aUsername), $productOwner->productOwnerId());
    }
}