<?php

declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Tenant;

use App\AgilePm\Domain\Model\ValueObject;

final class TenantId extends ValueObject
{
    private string $id;

    public function __construct(string $anId)
    {
        parent::__construct();
        $this->setId($anId);
    }

    public static function fromTenantId(TenantId $tenantId): self
    {
        return new self($tenantId->id());
    }

    public function id(): string
    {
        return $this->id;
    }

    public function equals(TenantId $other): bool
    {
        return $this->id === $other->id;
    }

    public function hashCode(): int
    {
        return (2785 * 5) + crc32($this->id);
    }

    public function __toString(): string
    {
        return "TenantId [id={$this->id}]";
    }

    private function setId(string $id): void
    {
        $this->assertArgumentNotEmpty($id, 'The tenant identity is required.');
        $this->assertArgumentLength($id, 36, 'The tenant identity must be 36 characters or less.');
    }
}
