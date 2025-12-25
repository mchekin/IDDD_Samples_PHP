<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\Common\Domain\Model\Process\ProcessId;
use App\Common\Domain\Model\DomainEvent;
use DateTime;

class ProductDiscussionRequestTimedOut implements DomainEvent
{
    private string $tenantId;
    private ProcessId $processId;
    private int $totalRetriesPermitted;
    private int $retryCount;
    private DateTime $occurredOn;
    private int $eventVersion;

    public function __construct(
        string $aTenantId,
        ProcessId $aProcessId,
        int $aTotalRetriesPermitted,
        int $aRetryCount
    ) {
        $this->tenantId = $aTenantId;
        $this->processId = $aProcessId;
        $this->totalRetriesPermitted = $aTotalRetriesPermitted;
        $this->retryCount = $aRetryCount;
        $this->occurredOn = new DateTime();
        $this->eventVersion = 1;
    }

    public function tenantId(): string
    {
        return $this->tenantId;
    }

    public function processId(): ProcessId
    {
        return $this->processId;
    }

    public function totalRetriesPermitted(): int
    {
        return $this->totalRetriesPermitted;
    }

    public function retryCount(): int
    {
        return $this->retryCount;
    }

    public function occurredOn(): DateTime
    {
        return $this->occurredOn;
    }

    public function eventVersion(): int
    {
        return $this->eventVersion;
    }
}