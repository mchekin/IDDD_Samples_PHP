<?php declare(strict_types=1);


namespace App\AgilePm\Domain\Model\Product\BacklogItem;


use App\Common\Domain\Model\AbstractId;

class BacklogItemId extends AbstractId
{
    public function __construct(string $anId)
    {
        parent::__construct($anId);
    }
}