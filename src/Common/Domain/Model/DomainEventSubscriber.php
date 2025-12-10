<?php declare(strict_types=1);

namespace App\Common\Domain\Model;

interface DomainEventSubscriber
{
    public function handleEvent(object $aDomainEvent): void;

    public function subscribedToEventType(): string;
}