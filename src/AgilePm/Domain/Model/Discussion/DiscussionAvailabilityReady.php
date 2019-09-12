<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Discussion;


class DiscussionAvailabilityReady extends DiscussionAvailability
{
    public function isReady(): bool
    {
        return true;
    }
}