<?php declare(strict_types=1);

namespace App\AgilePm\Application;

use RuntimeException;
use Throwable;

class ApplicationServiceLifeCycle
{
    public static function begin(bool $isListening = true): void
    {
        // TODO: Implement full lifecycle with UnitOfWork
    }

    public static function success(): void
    {
        // TODO: Implement commit logic
    }

    public static function fail(RuntimeException $anException = null): void
    {
        // TODO: Implement rollback logic
        if ($anException !== null) {
            throw $anException;
        }
    }
}