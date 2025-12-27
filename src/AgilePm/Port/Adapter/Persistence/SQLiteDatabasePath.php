<?php declare(strict_types=1);

namespace App\AgilePm\Port\Adapter\Persistence;

/**
 * Database paths for SQLite - matches Java's LevelDBDatabasePath
 */
class SQLiteDatabasePath
{
    public static function agilePMPath(): string
    {
        return self::testPath() . '/iddd_agilepm';
    }

    public static function agilePMTestPath(): string
    {
        return self::testPath() . '/iddd_agilepm_test';
    }

    private static function testPath(): string
    {
        return sys_get_temp_dir() . '/leveldb';
    }
}
