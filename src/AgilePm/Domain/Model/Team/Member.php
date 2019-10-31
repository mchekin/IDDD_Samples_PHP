<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use Carbon\Carbon;

abstract class Member
{
    /**
     * @var TenantId
     */
    private $tenantId;
    /**
     * @var String
     */
    private $username;
    /**
     * @var String
     */
    private $firstName;
    /**
     * @var String
     */
    private $lastName;
    /**
     * @var String
     */
    private $emailAddress;
    /**
     * @var Carbon
     */
    private $initializedOn;

    public function __construct(
        TenantId $aTenantId,
        String $aUsername,
        String $aFirstName,
        String $aLastName,
        String $anEmailAddress,
        Carbon $anInitializedOn)
    {

        $this->setTenantId($aTenantId);
        $this->setUsername($aUsername);
        $this->setFirstName($aFirstName);
        $this->setLastName($aLastName);
        $this->setEmailAddress($anEmailAddress);
        $this->setInitializedOn($anInitializedOn);
    }

    public function getTenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    private function setTenantId(TenantId $tenantId): void
    {
        //TODO: implement validation

        $this->tenantId = $tenantId;
    }

    private function setUsername(String $username): void
    {
        //TODO: implement validation

        $this->username = $username;
    }

    private function setFirstName(String $firstName): void
    {
        //TODO: implement validation

        $this->firstName = $firstName;
    }

    private function setLastName(String $lastName): void
    {
        //TODO: implement validation

        $this->lastName = $lastName;
    }

    private function setEmailAddress(String $emailAddress): void
    {
        //TODO: implement validation

        $this->emailAddress = $emailAddress;
    }

    private function setInitializedOn(Carbon $initializedOn): void
    {
        //TODO: implement validation

        $this->initializedOn = $initializedOn;
    }
}