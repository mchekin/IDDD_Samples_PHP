<?php

declare(strict_types=1);

namespace App\AgilePm\Application;

use App\AgilePm\Port\Adapter\Persistence\SQLiteDatabasePath;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteProvider;
use App\Common\Port\Adapter\Persistence\SQLite\SQLiteUnitOfWork;
use RuntimeException;

/**
 * Application Service Lifecycle - matches Java's ApplicationServiceLifeCycle
 * Manages UnitOfWork lifecycle for application services
 */
class ApplicationServiceLifeCycle
{
    private static bool $ownsUnitOfWork = false;

    public static function begin(bool $isListening = true): void
    {
        // Check if UnitOfWork is already started (e.g., in tests)
        try {
            SQLiteUnitOfWork::current();
            // Already started, we don't own it
            self::$ownsUnitOfWork = false;
            return;
        } catch (\RuntimeException $e) {
            // Not started, so start it and mark that we own it
            self::$ownsUnitOfWork = true;
        }

        // Start UnitOfWork like Java's LevelDBUnitOfWork pattern
        $databasePath = SQLiteDatabasePath::agilePMPath();
        $database = SQLiteProvider::instance()->databaseFrom($databasePath);
        SQLiteUnitOfWork::start($database);
    }

    public static function success(): void
    {
        // Only commit if we own the UnitOfWork
        if (!self::$ownsUnitOfWork) {
            return;
        }

        // Commit the current UnitOfWork like Java
        try {
            SQLiteUnitOfWork::current()->commit();
        } catch (\Exception $e) {
            // If commit fails, attempt rollback
            try {
                SQLiteUnitOfWork::current()->rollback();
            } catch (\Exception $rollbackException) {
                // Ignore rollback exceptions
            }
            throw $e;
        } finally {
            self::$ownsUnitOfWork = false;
        }
    }

    public static function fail(?RuntimeException $anException = null): void
    {
        // Only rollback if we own the UnitOfWork
        if (!self::$ownsUnitOfWork) {
            if ($anException !== null) {
                throw $anException;
            }
            return;
        }

        // Rollback the current UnitOfWork like Java
        try {
            SQLiteUnitOfWork::current()->rollback();
        } catch (\Exception $e) {
            // Ignore rollback exceptions if there's no active UnitOfWork
        } finally {
            self::$ownsUnitOfWork = false;
        }

        if ($anException !== null) {
            throw $anException;
        }
    }
}
