<?php

declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Team;

use App\AgilePm\Domain\Model\Entity;
use App\AgilePm\Domain\Model\Tenant\TenantId;
use DateTime;

class ProductOwner extends Entity
{
    private string $emailAddress;
    private bool $enabled = true;
    private string $firstName;
    private string $lastName;
    private TenantId $tenantId;
    private string $username;

    public function __construct(
        TenantId $aTenantId,
        string $aUsername,
        string $aFirstName,
        string $aLastName,
        string $anEmailAddress,
        DateTime $anInitializedOn
    ) {
        parent::__construct();

        $this->setEmailAddress($anEmailAddress);
        $this->setFirstName($aFirstName);
        $this->setLastName($aLastName);
        $this->setTenantId($aTenantId);
        $this->setUsername($aUsername);
    }

    public function productOwnerId(): ProductOwnerId
    {
        return new ProductOwnerId($this->tenantId(), $this->username());
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function equals(object $anObject): bool
    {
        $equalObjects = false;

        if ($anObject !== null && get_class($this) === get_class($anObject)) {
            $typedObject = $anObject;
            $equalObjects =
                $this->tenantId()->equals($typedObject->tenantId()) &&
                $this->username() === $typedObject->username();
        }

        return $equalObjects;
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (71121 * 79)
                + $this->tenantId()->hashCode()
                + crc32($this->username());

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return "ProductOwner [productOwnerId()={$this->productOwnerId()}, emailAddress()={$this->emailAddress()}, isEnabled()="
            . ($this->isEnabled() ? 'true' : 'false') . ", firstName()={$this->firstName()}, lastName()={$this->lastName()}, tenantId()={$this->tenantId()}"
            . ", username()={$this->username()}]";
    }

    private function setEmailAddress(string $anEmailAddress): void
    {
        if ($anEmailAddress !== null) {
            $this->assertArgumentLength($anEmailAddress, 100, 'Email address must be 100 characters or less.');
        }

        $this->emailAddress = $anEmailAddress;
    }

    private function setFirstName(string $aFirstName): void
    {
        if ($aFirstName !== null) {
            $this->assertArgumentLength($aFirstName, 50, 'First name must be 50 characters or less.');
        }

        $this->firstName = $aFirstName;
    }

    private function setLastName(string $aLastName): void
    {
        if ($aLastName !== null) {
            $this->assertArgumentLength($aLastName, 50, 'Last name must be 50 characters or less.');
        }

        $this->lastName = $aLastName;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenant id must be provided.');

        $this->tenantId = $aTenantId;
    }

    private function setUsername(string $aUsername): void
    {
        $this->assertArgumentNotEmpty($aUsername, 'The username must be provided.');
        $this->assertArgumentLength($aUsername, 250, 'The username must be 250 characters or less.');

        $this->username = $aUsername;
    }
}
