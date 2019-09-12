<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Discussion;


class DiscussionAvailabilityNotRequested extends DiscussionAvailability
{
    public function isNotRequested(): bool
    {
        return true;
    }
}