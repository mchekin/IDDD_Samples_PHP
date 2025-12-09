<?php

declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\ValueObject;

final class ProductId extends ValueObject
{
    private string $id;

    public function __construct(string $anId)
    {
        parent::__construct();
        $this->setId($anId);
    }

    public static function fromProductId(ProductId $productId): self
    {
        return new self($productId->id());
    }

    public function id(): string
    {
        return $this->id;
    }

    public function equals(ProductId $other): bool
    {
        return $this->id === $other->id;
    }

    public function hashCode(): int
    {
        return (57853 * 31) + crc32($this->id);
    }

    public function __toString(): string
    {
        return "ProductId [id={$this->id}]";
    }

    private function setId(string $id): void
    {
        $this->assertArgumentNotEmpty($id, 'The id must be provided.');
        $this->assertArgumentLength($id, 36, 'The id must be 36 characters or less.');
    }
}
