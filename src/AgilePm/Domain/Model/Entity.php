<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model;

use App\Common\AssertionConcern;

abstract class Entity extends AssertionConcern
{
    private int $concurrencyVersion;

    public function __construct()
    {
        parent::__construct();

        $this->setConcurrencyVersion(0);
    }

    public function concurrencyVersion(): int
    {
        return $this->concurrencyVersion;
    }

    private function setConcurrencyVersion(int $aConcurrencyVersion): void
    {
        $this->concurrencyVersion = $aConcurrencyVersion;
    }
}