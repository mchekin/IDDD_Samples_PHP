<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\BacklogItem;

enum BacklogItemStatus: string
{
    case PLANNED = 'planned';
    case SCHEDULED = 'scheduled';
    case COMMITTED = 'committed';
    case DONE = 'done';
    case REMOVED = 'removed';

    public function isCommitted(): bool
    {
        return $this === self::COMMITTED;
    }

    public function isDone(): bool
    {
        return $this === self::DONE;
    }

    public function isPlanned(): bool
    {
        return $this === self::PLANNED;
    }

    public function isRemoved(): bool
    {
        return $this === self::REMOVED;
    }

    public function isScheduled(): bool
    {
        return $this === self::SCHEDULED;
    }

    public function regress(): self
    {
        if ($this->isPlanned()) {
            return self::PLANNED;
        } elseif ($this->isScheduled()) {
            return self::PLANNED;
        } elseif ($this->isCommitted()) {
            return self::SCHEDULED;
        } elseif ($this->isDone()) {
            return self::COMMITTED;
        } elseif ($this->isRemoved()) {
            return self::PLANNED;
        }

        return self::PLANNED;
    }
}