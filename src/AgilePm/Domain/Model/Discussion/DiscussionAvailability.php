<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Discussion;


abstract class DiscussionAvailability
{
    public static function addOnNotEnabled(): DiscussionAvailability
    {
        return new DiscussionAvailabilityAddOnNotEnabled();
    }

    public static function failed(): DiscussionAvailability
    {
        return new DiscussionAvailabilityFailed();
    }

    public static function notRequested(): DiscussionAvailability
    {
        return new DiscussionAvailabilityNotRequested();
    }

    public static function requested(): DiscussionAvailability
    {
        return new DiscussionAvailabilityRequested();
    }

    public static function ready(): DiscussionAvailability
    {
        return new DiscussionAvailabilityReady();
    }

    public function isAddOnNotAvailable(): bool
    {
        return false;
    }

    public function isFailed(): bool
    {
        return false;
    }

    public function isNotRequested(): bool
    {
        return false;
    }

    public function isReady(): bool
    {
        return false;
    }

    public function isRequested(): bool
    {
        return false;
    }
}