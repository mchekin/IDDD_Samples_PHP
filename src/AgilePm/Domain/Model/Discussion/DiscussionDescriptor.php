<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Discussion;

use App\AgilePm\Domain\Model\ValueObject;

class DiscussionDescriptor extends ValueObject
{
    public const UNDEFINED_ID = 'UNDEFINED';

    private string $id;

    public function __construct(string $anId)
    {
        parent::__construct();

        $this->setId($anId);
    }

    public static function fromDiscussionDescriptor(DiscussionDescriptor $aDiscussionDescriptor): self
    {
        return new self($aDiscussionDescriptor->id());
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isUndefined(): bool
    {
        return $this->id() === self::UNDEFINED_ID;
    }

    public function equals(DiscussionDescriptor $anObject): bool
    {
        return $this->id() === $anObject->id();
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (72881 * 101)
            + crc32($this->id());

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return "DiscussionDescriptor [id={$this->id}]";
    }

    private function setId(string $anId): void
    {
        $this->assertArgumentNotEmpty($anId, 'The discussion identity must be provided.');
        $this->assertArgumentLength($anId, 36, 'The discussion identity must be 36 characters or less.');

        $this->id = $anId;
    }
}