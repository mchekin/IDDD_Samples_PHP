<?php declare(strict_types=1);

namespace App\Common\Domain\Model\Process;

use App\Common\Domain\Model\AbstractId;
use Ramsey\Uuid\Uuid;

final readonly class ProcessId extends AbstractId
{
    public static function existingProcessId(string $anId): self
    {
        return new self($anId);
    }

    public static function newProcessId(): self
    {
        return new self(strtolower(Uuid::uuid4()->toString()));
    }

    public function __construct(string $anId)
    {
        parent::__construct($anId);
    }
}
