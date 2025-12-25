<?php declare(strict_types=1);

namespace App\Common\Domain\Model\Process;

use App\Common\AssertionConcern;
use DateTime;

class TimeConstrainedProcessTracker extends AssertionConcern
{
    private int $allowableDuration;
    private bool $completed = false;
    private int $concurrencyVersion = 0;
    private string $description;
    private ProcessId $processId;
    private bool $processInformedOfTimeout = false;
    private string $processTimedOutEventType;
    private int $retryCount = 0;
    private string $tenantId;
    private int $timeConstrainedProcessTrackerId;
    private int $timeoutOccursOn;
    private int $totalRetriesPermitted;

    public function __construct(
        string $aTenantId,
        ProcessId $aProcessId,
        string $aDescription,
        DateTime $anOriginalStartTime,
        int $anAllowableDuration,
        int $aTotalRetriesPermitted,
        string $aProcessTimedOutEventType
    ) {
        $this->setAllowableDuration($anAllowableDuration);
        $this->setDescription($aDescription);
        $this->setProcessId($aProcessId);
        $this->setProcessTimedOutEventType($aProcessTimedOutEventType);
        $this->setTenantId($aTenantId);
        $this->setTimeConstrainedProcessTrackerId(-1);
        $this->setTimeoutOccursOn($anOriginalStartTime->getTimestamp() * 1000 + $anAllowableDuration);
        $this->setTotalRetriesPermitted($aTotalRetriesPermitted);
    }

    public function allowableDuration(): int
    {
        return $this->allowableDuration;
    }

    public function completed(): void
    {
        $this->completed = true;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function processId(): ProcessId
    {
        return $this->processId;
    }

    public function tenantId(): string
    {
        return $this->tenantId;
    }

    private function setAllowableDuration(int $anAllowableDuration): void
    {
        $this->assertArgumentTrue($anAllowableDuration > 0, 'Allowable duration must be greater than zero.');
        $this->allowableDuration = $anAllowableDuration;
    }

    private function setDescription(string $aDescription): void
    {
        $this->assertArgumentNotEmpty($aDescription, 'Description must not be empty.');
        $this->description = $aDescription;
    }

    private function setProcessId(ProcessId $aProcessId): void
    {
        $this->assertArgumentNotNull($aProcessId, 'Process id must not be null.');
        $this->processId = $aProcessId;
    }

    private function setProcessTimedOutEventType(string $aProcessTimedOutEventType): void
    {
        $this->assertArgumentNotEmpty($aProcessTimedOutEventType, 'Process timed out event type must not be empty.');
        $this->processTimedOutEventType = $aProcessTimedOutEventType;
    }

    private function setTenantId(string $aTenantId): void
    {
        $this->assertArgumentNotEmpty($aTenantId, 'Tenant id must not be empty.');
        $this->tenantId = $aTenantId;
    }

    private function setTimeConstrainedProcessTrackerId(int $aTimeConstrainedProcessTrackerId): void
    {
        $this->timeConstrainedProcessTrackerId = $aTimeConstrainedProcessTrackerId;
    }

    private function setTimeoutOccursOn(int $aTimeoutOccursOn): void
    {
        $this->timeoutOccursOn = $aTimeoutOccursOn;
    }

    private function setTotalRetriesPermitted(int $aTotalRetriesPermitted): void
    {
        $this->assertArgumentTrue($aTotalRetriesPermitted >= 0, 'Total retries permitted must not be negative.');
        $this->totalRetriesPermitted = $aTotalRetriesPermitted;
    }
}