<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Team;

/**
 * @implements \IteratorAggregate<int, ProductOwner>
 */
class ProductOwnerCollection implements \IteratorAggregate, \Countable
{
    /** @var array<ProductOwner> */
    private array $productOwners;

    /**
     * @param array<ProductOwner> $productOwners
     */
    public function __construct(array $productOwners = [])
    {
        $this->productOwners = $productOwners;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->productOwners);
    }

    public function count(): int
    {
        return count($this->productOwners);
    }

    /**
     * @return array<ProductOwner>
     */
    public function toArray(): array
    {
        return $this->productOwners;
    }
}