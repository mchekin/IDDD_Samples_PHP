<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Product\BacklogItem;


use Webmozart\Assert\Assert;

class BacklogItemId
{
    /** @var string */
    private $id;

    public function __construct(string $anId)
    {
        $this->setId($anId);
    }

    private function setId(string $anId): void
    {
        Assert::stringNotEmpty('The backlog item identity is required.');
        Assert::uuid($anId, "The backlog item identity $anId must be UUID");

        $this->id = $anId;
    }

    public function id():string
    {
        return $this->id;
    }
}