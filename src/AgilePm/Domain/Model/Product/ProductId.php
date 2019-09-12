<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Product;


use Webmozart\Assert\Assert;

class ProductId
{
    /** @var string */
    private $id;

    public function __construct(string $anId)
    {
        $this->setId($anId);
    }

    private function setId(string $anId): void
    {
        Assert::stringNotEmpty('The product identity is required.');
        Assert::uuid($anId, "The product identity $anId must be UUID");

        $this->id = $anId;
    }

    public function id():string
    {
        return $this->id;
    }
}