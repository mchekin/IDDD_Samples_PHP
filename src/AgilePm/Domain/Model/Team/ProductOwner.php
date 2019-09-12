<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use Carbon\Carbon;

class ProductOwner
{
    public function __construct(
        TenantId $aTenantId,
        String $aUsername,
        String $aFirstName,
        String $aLastName,
        String $anEmailAddress,
        Carbon $anInitializedOn)
    {

    }
}