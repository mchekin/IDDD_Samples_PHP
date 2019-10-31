<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use Carbon\Carbon;

class ProductOwner extends Member
{

    public function __construct(
        TenantId $aTenantId,
        String $aUsername,
        String $aFirstName,
        String $aLastName,
        String $anEmailAddress,
        Carbon $anInitializedOn)
    {
        parent::__construct($aTenantId, $aUsername, $aFirstName, $aLastName, $anEmailAddress, $anInitializedOn);
    }

    public function productOwnerId(): ProductOwnerId
    {
        return new ProductOwnerId($this->getTenantId(), $this->getUsername());
    }
}