<?php declare(strict_types=1);

namespace App\Common\Domain\Model;

use Webmozart\Assert\Assert;

abstract readonly class AbstractId
{
    public function __construct(
        private string $id
    ) {
        Assert::stringNotEmpty($id, 'The basic identity is required.');
        Assert::uuid($id, "The basic identity {$id} must be UUID");
    }

    public function id(): string
    {
        return $this->id;
    }

    public function equals(AbstractId $other): bool
    {
        return $this::class === $other::class && $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}