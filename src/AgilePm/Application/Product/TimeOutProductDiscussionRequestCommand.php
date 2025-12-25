<?php

declare(strict_types=1);

namespace App\AgilePm\Application\Product;

use DateTime;

class TimeOutProductDiscussionRequestCommand
{
    private string $tenantId;
    private string $processId;
    private DateTime $timedOutDate;

    public function __construct(string $tenantId = '', string $processId = '', ?DateTime $timedOutDate = null)
    {
        $this->tenantId = $tenantId;
        $this->processId = $processId;
        $this->timedOutDate = $timedOutDate ?? new DateTime();
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function setTenantId(string $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function getProcessId(): string
    {
        return $this->processId;
    }

    public function setProcessId(string $processId): void
    {
        $this->processId = $processId;
    }

    public function getTimedOutDate(): DateTime
    {
        return $this->timedOutDate;
    }

    public function setTimedOutDate(DateTime $timedOutDate): void
    {
        $this->timedOutDate = $timedOutDate;
    }
}
