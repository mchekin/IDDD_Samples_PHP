<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Tenant;

use Webmozart\Assert\Assert;

final readonly class TenantId
{
    public function __construct(
        private string $id
    ) {
        $this->validateId($id);
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

    public function __toString(): string
    {
        return "TenantId [id={$this->id}]";
    }

    private function validateId(string $id): void
    {
        Assert::stringNotEmpty($id, 'The tenant identity is required.');
        Assert::maxLength($id, 36, 'The tenant identity must be 36 characters or less.');
    }
}