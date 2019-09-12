<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Discussion;


class DiscussionAvailabilityRequested extends DiscussionAvailability
{
    public function isRequested(): bool
    {
        return true;
    }
}