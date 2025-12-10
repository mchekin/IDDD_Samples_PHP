<?php declare(strict_types=1);

namespace App\Common\Domain\Model;

use DateTime;

interface DomainEvent
{
    public function eventVersion(): int;

    public function occurredOn(): DateTime;
}