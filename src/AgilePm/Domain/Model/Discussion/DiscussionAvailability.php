<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Discussion;

enum DiscussionAvailability: string
{
    case ADD_ON_NOT_ENABLED = 'add_on_not_enabled';
    case FAILED = 'failed';
    case NOT_REQUESTED = 'not_requested';
    case REQUESTED = 'requested';
    case READY = 'ready';


    public function isAddOnNotAvailable(): bool
    {
        return $this === self::ADD_ON_NOT_ENABLED;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    public function isNotRequested(): bool
    {
        return $this === self::NOT_REQUESTED;
    }

    public function isReady(): bool
    {
        return $this === self::READY;
    }

    public function isRequested(): bool
    {
        return $this === self::REQUESTED;
    }

    public function label(): string
    {
        return match ($this) {
            self::ADD_ON_NOT_ENABLED => 'Add-on Not Enabled',
            self::FAILED => 'Failed',
            self::NOT_REQUESTED => 'Not Requested',
            self::REQUESTED => 'Requested',
            self::READY => 'Ready',
        };
    }
}