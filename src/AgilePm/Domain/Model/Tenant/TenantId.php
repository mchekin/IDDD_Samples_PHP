<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Tenant;

use Webmozart\Assert\Assert;

class TenantId
{
    /** @var string */
    private $id;

    public function __construct(string $anId)
    {
        $this->setId($anId);
    }

    private function setId(string $anId): void
    {
        Assert::stringNotEmpty('The tenant identity is required.');
        Assert::uuid($anId, "The tenant identity $anId must be UUID");

        $this->id = $anId;
    }

    public function id(): string
    {
        return $this->id;
    }
}