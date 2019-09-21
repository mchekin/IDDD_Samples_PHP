<?php declare(strict_types=1);


namespace App\Common\Domain\Model\Process;

use App\Common\Domain\Model\AbstractId;

class ProcessId extends AbstractId
{
    public function __construct(string $anId)
    {
        parent::__construct($anId);
    }
}