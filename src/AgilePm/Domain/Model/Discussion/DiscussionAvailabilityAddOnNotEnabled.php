<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Discussion;


class DiscussionAvailabilityAddOnNotEnabled extends DiscussionAvailability
{
    public function isAddOnNotAvailable(): bool
    {
        return true;
    }
}