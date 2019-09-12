<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use Carbon\Carbon;

class ProductOwner
{
    /**
     * @var TenantId
     */
    private $aTenantId;
    /**
     * @var String
     */
    private $aUsername;
    /**
     * @var String
     */
    private $aFirstName;
    /**
     * @var String
     */
    private $aLastName;
    /**
     * @var String
     */
    private $anEmailAddress;
    /**
     * @var Carbon
     */
    private $anInitializedOn;

    public function __construct(
        TenantId $aTenantId,
        String $aUsername,
        String $aFirstName,
        String $aLastName,
        String $anEmailAddress,
        Carbon $anInitializedOn)
    {

        $this->aTenantId = $aTenantId;
        $this->aUsername = $aUsername;
        $this->aFirstName = $aFirstName;
        $this->aLastName = $aLastName;
        $this->anEmailAddress = $anEmailAddress;
        $this->anInitializedOn = $anInitializedOn;
    }

    public function tenantId(): TenantId
    {
        return $this->aTenantId;
    }

    public function username(): String
    {
        return $this->aUsername;
    }

    public function firstName(): String
    {
        return $this->aFirstName;
    }

    public function lastName(): String
    {
        return $this->aLastName;
    }

    public function emailAddress(): String
    {
        return $this->anEmailAddress;
    }

    public function initializedOn(): Carbon
    {
        return $this->anInitializedOn;
    }
}