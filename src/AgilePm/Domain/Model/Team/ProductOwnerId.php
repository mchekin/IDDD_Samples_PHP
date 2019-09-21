<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Team;


use App\AgilePm\Domain\Model\Tenant\TenantId;
use App\Common\Domain\Model\AbstractId;

class ProductOwnerId extends AbstractId
{
    /**
     * @var TenantId
     */
    private $aTenantId;

    public function __construct(TenantId $aTenantId, string $anId)
    {
        $this->aTenantId = $aTenantId;

        parent::__construct($anId);
    }

    public function tenantId(): TenantId
    {
        return $this->aTenantId;
    }
}