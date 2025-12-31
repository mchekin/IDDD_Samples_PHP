<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product;

/**
 * @implements \IteratorAggregate<int, Product>
 */
class ProductCollection implements \IteratorAggregate, \Countable
{
    /** @var array<Product> */
    private array $products;

    /**
     * @param array<Product> $products
     */
    public function __construct(array $products = [])
    {
        $this->products = $products;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->products);
    }

    public function count(): int
    {
        return count($this->products);
    }

    /**
     * @return array<Product>
     */
    public function toArray(): array
    {
        return $this->products;
    }
}