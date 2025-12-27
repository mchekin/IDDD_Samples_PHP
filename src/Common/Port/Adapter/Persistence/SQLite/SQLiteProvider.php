<?php declare(strict_types=1);

namespace App\Common\Port\Adapter\Persistence\SQLite;

use PDO;

/**
 * SQLite database provider - matches Java's LevelDBProvider
 * Manages SQLite database connections (embedded database, no server needed)
 */
class SQLiteProvider
{
    private static ?self $instance = null;
    private array $databases = [];

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function databaseFrom(string $aPath): PDO
    {
        if (!isset($this->databases[$aPath])) {
            $this->databases[$aPath] = $this->openDatabase($aPath);
        }

        return $this->databases[$aPath];
    }

    public function purge(PDO $aDatabase): void
    {
        // Like Java's purge() - delete all data
        $aDatabase->exec('DELETE FROM entities');
    }

    public function close(string $aPath): void
    {
        if (isset($this->databases[$aPath])) {
            unset($this->databases[$aPath]);
        }
    }

    public function closeAll(): void
    {
        $this->databases = [];
    }

    private function __construct()
    {
        // Private constructor for singleton
    }

    private function openDatabase(string $aPath): PDO
    {
        // Create directory if it doesn't exist
        $directory = dirname($aPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Open SQLite database (embedded, like LevelDB)
        $pdo = new PDO('sqlite:' . $aPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create tables if they don't exist
        $this->initializeSchema($pdo);

        return $pdo;
    }

    private function initializeSchema(PDO $pdo): void
    {
        // Simple key-value store schema
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS entities (
                key TEXT PRIMARY KEY,
                class TEXT NOT NULL,
                data TEXT NOT NULL
            )
        ');

        // Create indexes
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_class ON entities(class)');
    }
}
