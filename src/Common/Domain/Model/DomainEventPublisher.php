<?php declare(strict_types=1);

namespace App\Common\Domain\Model;

class DomainEventPublisher
{
    private static ?DomainEventPublisher $instance = null;
    private bool $publishing = false;
    private array $subscribers = [];

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function publish(object $aDomainEvent): void
    {
        if (!$this->isPublishing() && $this->hasSubscribers()) {
            try {
                $this->setPublishing(true);

                $eventType = get_class($aDomainEvent);

                foreach ($this->subscribers() as $subscriber) {
                    $subscribedToType = $subscriber->subscribedToEventType();

                    if ($eventType === $subscribedToType || $subscribedToType === DomainEvent::class) {
                        $subscriber->handleEvent($aDomainEvent);
                    }
                }
            } finally {
                $this->setPublishing(false);
            }
        }
    }

    public function publishAll(array $aDomainEvents): void
    {
        foreach ($aDomainEvents as $domainEvent) {
            $this->publish($domainEvent);
        }
    }

    public function reset(): void
    {
        if (!$this->isPublishing()) {
            $this->setSubscribers([]);
        }
    }

    public function subscribe(DomainEventSubscriber $aSubscriber): void
    {
        if (!$this->isPublishing()) {
            $this->ensureSubscribersList();
            $this->subscribers[] = $aSubscriber;
        }
    }

    private function __construct()
    {
        $this->setPublishing(false);
        $this->ensureSubscribersList();
    }

    private function ensureSubscribersList(): void
    {
        if (!$this->hasSubscribers()) {
            $this->setSubscribers([]);
        }
    }

    private function isPublishing(): bool
    {
        return $this->publishing;
    }

    private function setPublishing(bool $aFlag): void
    {
        $this->publishing = $aFlag;
    }

    private function hasSubscribers(): bool
    {
        return $this->subscribers !== null && count($this->subscribers) > 0;
    }

    private function subscribers(): array
    {
        return $this->subscribers;
    }

    private function setSubscribers(array $aSubscriberList): void
    {
        $this->subscribers = $aSubscriberList;
    }
}