<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use Webmozart\Assert\Assert;

class ProductOwnerId
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var TenantId
     */
    private $aTenantId;

    public function __construct(TenantId $aTenantId, string $anId)
    {
        $this->aTenantId = $aTenantId;

        $this->setId($anId);
    }

    private function setId(string $anId): void
    {
        Assert::stringNotEmpty('The product owner identity is required.');
        Assert::uuid($anId, "The product owner identity $anId must be UUID");

        $this->id = $anId;
    }

    public function id():string
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->aTenantId;
    }
}