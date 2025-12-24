<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\Release;

use App\AgilePm\Domain\Model\ValueObject;

class ReleaseId extends ValueObject
{
    private string $id;

    public function __construct(string $anId)
    {
        parent::__construct();

        $this->setId($anId);
    }

    public static function fromReleaseId(ReleaseId $aReleaseId): self
    {
        return new self($aReleaseId->id());
    }

    public function id(): string
    {
        return $this->id;
    }

    public function equals(ReleaseId $anObject): bool
    {
        return $this->id() === $anObject->id();
    }

    public function hashCode(): int
    {
        $hashCodeValue =
            + (38313 * 43)
            + crc32($this->id());

        return $hashCodeValue;
    }

    public function __toString(): string
    {
        return "ReleaseId [id={$this->id}]";
    }

    private function setId(string $anId): void
    {
        $this->assertArgumentNotEmpty($anId, 'The id must be provided.');
        $this->assertArgumentLength($anId, 36, 'The id must be 36 characters or less.');

        $this->id = $anId;
    }
}