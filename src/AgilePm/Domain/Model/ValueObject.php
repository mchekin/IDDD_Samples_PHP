<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model;

use App\Common\AssertionConcern;

abstract class ValueObject extends AssertionConcern
{
    public function __construct()
    {
        parent::__construct();
    }
}