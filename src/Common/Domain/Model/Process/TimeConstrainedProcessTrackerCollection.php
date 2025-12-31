<?php declare(strict_types=1);

namespace App\Common\Domain\Model\Process;

/**
 * @implements \IteratorAggregate<int, TimeConstrainedProcessTracker>
 */
class TimeConstrainedProcessTrackerCollection implements \IteratorAggregate, \Countable
{
    /** @var array<TimeConstrainedProcessTracker> */
    private array $trackers;

    /**
     * @param array<TimeConstrainedProcessTracker> $trackers
     */
    public function __construct(array $trackers = [])
    {
        $this->trackers = $trackers;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->trackers);
    }

    public function count(): int
    {
        return count($this->trackers);
    }

    /**
     * @return array<TimeConstrainedProcessTracker>
     */
    public function toArray(): array
    {
        return $this->trackers;
    }
}