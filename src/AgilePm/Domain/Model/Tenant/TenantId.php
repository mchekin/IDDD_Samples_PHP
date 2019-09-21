<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Tenant;

use App\Common\Domain\Model\AbstractId;

class TenantId extends AbstractId
{
    public function __construct(string $anId)
    {
        parent::__construct($anId);
    }
}