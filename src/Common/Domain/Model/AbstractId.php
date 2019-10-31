<?php declare(strict_types=1);


namespace App\Common\Domain\Model;


use Webmozart\Assert\Assert;

abstract class AbstractId
{
    /** @var string */
    private $id;

    public function __construct(string $anId)
    {
        $this->setId($anId);
    }

    private function setId(string $anId): void
    {
        Assert::stringNotEmpty($anId, 'The basic identity is required.');
        Assert::uuid($anId, "The basic identity $anId must be UUID");

        $this->id = $anId;
    }

    public function id():string
    {
        return $this->id;
    }
}