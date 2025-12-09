<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Team;

use App\AgilePm\Domain\Model\ValueObject;
use App\AgilePm\Domain\Model\Tenant\TenantId;

final class ProductOwnerId extends ValueObject
{
    private string $id;
    private TenantId $tenantId;

    public function __construct(TenantId $aTenantId, string $anId)
    {
        parent::__construct();
        $this->setId($anId);
        $this->setTenantId($aTenantId);
    }

    public static function fromProductOwnerId(ProductOwnerId $productOwnerId): self
    {
        return new self($productOwnerId->tenantId(), $productOwnerId->id());
    }

    public function id(): string
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function equals(ProductOwnerId $other): bool
    {
        return $this->tenantId->equals($other->tenantId) && $this->id === $other->id;
    }

    public function hashCode(): int
    {
        return (43685 * 83) + crc32($this->id);
    }

    public function __toString(): string
    {
        return "ProductOwnerId [tenantId={$this->tenantId}, id={$this->id}]";
    }

    private function setId(string $anId): void
    {
        $this->assertArgumentNotEmpty($anId, 'The id must be provided.');
        $this->assertArgumentLength($anId, 36, 'The id must be 36 characters or less.');
        $this->id = $anId;
    }

    private function setTenantId(TenantId $aTenantId): void
    {
        $this->assertArgumentNotNull($aTenantId, 'The tenantId must be provided.');
        $this->tenantId = $aTenantId;
    }
}
