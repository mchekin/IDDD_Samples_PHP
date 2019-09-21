<?php declare(strict_types=1);


namespace App\Common\Domain\Model\Process;


interface TimeConstrainedProcessTrackerRepository
{
    public function add(TimeConstrainedProcessTracker $aTimeConstrainedProcessTracker): void;

    public function allTimedOut(): TimeConstrainedProcessTrackerCollection;

    public function allTimedOutOf(String $aTenantId): TimeConstrainedProcessTrackerCollection;

    public function allTrackers(String $aTenantId): TimeConstrainedProcessTrackerCollection;

    public function save(TimeConstrainedProcessTracker $aTimeConstrainedProcessTracker): void;

    public function trackerOfProcessId(String $aTenantId, ProcessId $aProcessId): TimeConstrainedProcessTracker;
}