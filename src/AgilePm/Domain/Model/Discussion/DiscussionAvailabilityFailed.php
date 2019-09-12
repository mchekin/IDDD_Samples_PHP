<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Discussion;


class DiscussionAvailabilityFailed extends DiscussionAvailability
{
    public function isFailed(): bool
    {
        return true;
    }
}