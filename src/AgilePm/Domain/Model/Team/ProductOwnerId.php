<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use Webmozart\Assert\Assert;

class ProductOwnerId
{
    /**
     * @var TenantId
     */
    private $aTenantId;
    /**
     * @var string
     */
    private $id;

    public function __construct(TenantId $aTenantId, string $anId)
    {
        $this->aTenantId = $aTenantId;

        $this->setId($anId);
    }

    public function tenantId(): TenantId
    {
        return $this->aTenantId;
    }

    public function id(): string
    {
        return $this->id;
    }

    private function setId(string $anId): void
    {
        Assert::stringNotEmpty($anId, 'The basic identity is required.');

        $this->id = $anId;
    }

}