<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

use App\AgilePm\Domain\Model\ValueObject;
use App\AgilePm\Domain\Model\Discussion\DiscussionAvailability;
use App\AgilePm\Domain\Model\Discussion\DiscussionDescriptor;

final class ProductDiscussion extends ValueObject
{
    private DiscussionAvailability $availability;
    private DiscussionDescriptor $descriptor;

    public static function fromAvailability(DiscussionAvailability $anAvailability): self
    {
        if ($anAvailability->isReady()) {
            throw new \InvalidArgumentException('Cannot be created ready.');
        }

        $descriptor = new DiscussionDescriptor(DiscussionDescriptor::UNDEFINED_ID);

        return new self($descriptor, $anAvailability);
    }

    public function __construct(
        DiscussionDescriptor $aDescriptor,
        DiscussionAvailability $anAvailability
    ) {
        parent::__construct();

        $this->setAvailability($anAvailability);
        $this->setDescriptor($aDescriptor);
    }

    public static function fromProductDiscussion(ProductDiscussion $aProductDiscussion): self
    {
        return new self($aProductDiscussion->descriptor(), $aProductDiscussion->availability());
    }

    public function availability(): DiscussionAvailability
    {
        return $this->availability;
    }

    public function descriptor(): DiscussionDescriptor
    {
        return $this->descriptor;
    }

    public function nowReady(DiscussionDescriptor $aDescriptor): self
    {
        if ($aDescriptor === null || $aDescriptor->isUndefined()) {
            throw new \InvalidArgumentException('The discussion descriptor must be defined.');
        }
        if (!$this->availability()->isRequested()) {
            throw new \InvalidArgumentException('The discussion must be requested first.');
        }

        return new self($aDescriptor, DiscussionAvailability::READY);
    }

    private function setAvailability(DiscussionAvailability $anAvailability): void
    {
        $this->assertArgumentNotNull($anAvailability, 'The availability must be provided.');

        $this->availability = $anAvailability;
    }

    private function setDescriptor(DiscussionDescriptor $aDescriptor): void
    {
        $this->assertArgumentNotNull($aDescriptor, 'The descriptor must be provided.');

        $this->descriptor = $aDescriptor;
    }
}