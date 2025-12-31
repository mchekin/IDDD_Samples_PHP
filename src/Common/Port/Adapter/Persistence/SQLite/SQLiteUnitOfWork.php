<?php declare(strict_types=1);

namespace App\Common\Port\Adapter\Persistence\SQLite;

use PDO;

/**
 * Unit of Work pattern for SQLite - matches Java's LevelDBUnitOfWork
 * Provides transaction management and identity map
 */
class SQLiteUnitOfWork
{
    private static ?self $current = null;

    private PDO $database;
    private bool $isWritable;
    private array $identityMap = [];
    private bool $inTransaction = false;

    public static function current(): self
    {
        if (self::$current === null) {
            throw new \RuntimeException('No unit of work has been started.');
        }

        return self::$current;
    }

    public static function readOnly(PDO $aDatabase): self
    {
        if (self::$current === null) {
            self::$current = new self($aDatabase, false);
        }

        return self::$current;
    }

    public static function start(PDO $aDatabase): self
    {
        if (self::$current === null) {
            self::$current = new self($aDatabase, true);
        }

        return self::$current;
    }

    public function commit(): void
    {
        if ($this->inTransaction) {
            $this->database->commit();
            $this->inTransaction = false;
        }

        $this->close();
    }

    public function rollback(): void
    {
        if ($this->inTransaction) {
            $this->database->rollBack();
            $this->inTransaction = false;
        }

        $this->close();
    }

    public function write(string $key, object $value): void
    {
        $this->ensureTransaction();

        $serialized = json_encode($this->serialize($value), JSON_THROW_ON_ERROR);
        $className = get_class($value);

        $stmt = $this->database->prepare(
            'INSERT OR REPLACE INTO entities (key, class, data) VALUES (?, ?, ?)'
        );
        $stmt->execute([$key, $className, $serialized]);

        // Add to identity map
        $this->identityMap[$key] = $value;
    }

    /**
     * @param class-string $className
     */
    public function read(string $key, string $className): ?object
    {
        // Check identity map first
        if (isset($this->identityMap[$key])) {
            return $this->identityMap[$key];
        }

        $stmt = $this->database->prepare(
            'SELECT data FROM entities WHERE key = ? AND class = ?'
        );
        $stmt->execute([$key, $className]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        if (!is_array($row) || !isset($row['data']) || !is_string($row['data'])) {
            return null;
        }

        $data = json_decode($row['data'], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \RuntimeException('Failed to decode JSON data for key: ' . $key);
        }

        $object = $this->deserialize($data, $className);

        // Add to identity map
        $this->identityMap[$key] = $object;

        return $object;
    }

    public function remove(string $key): void
    {
        $this->ensureTransaction();

        $stmt = $this->database->prepare('DELETE FROM entities WHERE key = ?');
        $stmt->execute([$key]);

        unset($this->identityMap[$key]);
    }

    /**
     * Deserialize JSON data to an object
     * Public method for repositories to use when querying directly
     * @param class-string $className
     */
    public function deserializeFromJson(string $json, string $className): object
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \RuntimeException('Failed to decode JSON data');
        }
        return $this->deserialize($data, $className);
    }

    public function database(): PDO
    {
        return $this->database;
    }

    private function __construct(PDO $aDatabase, bool $isWritable)
    {
        $this->database = $aDatabase;
        $this->isWritable = $isWritable;

        if ($isWritable) {
            $this->database->beginTransaction();
            $this->inTransaction = true;
        }
    }

    private function ensureTransaction(): void
    {
        if (!$this->isWritable) {
            throw new \RuntimeException('Cannot write in read-only unit of work.');
        }

        if (!$this->inTransaction) {
            $this->database->beginTransaction();
            $this->inTransaction = true;
        }
    }

    private function close(): void
    {
        self::$current = null;
        $this->identityMap = [];
    }

    private function serialize(object $object): array
    {
        $reflection = new \ReflectionClass($object);
        $data = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            if ($property->isInitialized($object)) {
                $value = $property->getValue($object);
                $data[$property->getName()] = $this->serializeValue($value);
            }
        }

        return $data;
    }

    /**
     * @return mixed
     */
    private function serializeValue(mixed $value): mixed
    {
        if (is_object($value)) {
            // Handle enums specially
            if ($value instanceof \UnitEnum) {
                return [
                    '__class__' => get_class($value),
                    'name' => $value->name,
                    'value' => $value instanceof \BackedEnum ? $value->value : $value->name
                ];
            }

            $serialized = $this->serialize($value);
            $serialized['__class__'] = get_class($value);
            return $serialized;
        } elseif (is_array($value)) {
            return array_map(fn($item) => $this->serializeValue($item), $value);
        }

        return $value;
    }

    /**
     * @param class-string $className
     */
    private function deserialize(array $data, string $className): object
    {
        $reflection = new \ReflectionClass($className);

        // Handle enums specially
        if ($reflection->isEnum()) {
            // For enums, use the 'name' or 'value' from the serialized data
            $enumValue = $data['name'] ?? $data['value'] ?? null;
            if ($enumValue !== null) {
                // Try to use from() for backed enums, or find by name for unit enums
                if (method_exists($className, 'from')) {
                    try {
                        return $className::from($enumValue);
                    } catch (\ValueError $e) {
                        // Fall through to name-based lookup
                    }
                }

                // Find enum case by name
                foreach ($className::cases() as $case) {
                    if ($case->name === $enumValue) {
                        return $case;
                    }
                }
            }
            throw new \RuntimeException("Cannot deserialize enum $className without name or value");
        }

        // Simple deserialization - create object and set properties
        $object = $reflection->newInstanceWithoutConstructor();

        foreach ($data as $property => $value) {
            if ($reflection->hasProperty($property)) {
                $prop = $reflection->getProperty($property);
                $prop->setAccessible(true);
                $prop->setValue($object, $this->deserializeValue($value, $prop));
            }
        }

        return $object;
    }

    /**
     * @return mixed
     */
    private function deserializeValue(mixed $value, \ReflectionProperty $property): mixed
    {
        // Handle nested objects, arrays, etc.
        if (is_array($value) && isset($value['__class__'])) {
            $className = $value['__class__'];
            if (!is_string($className)) {
                throw new \RuntimeException('Invalid __class__ value in deserialization');
            }
            /** @var class-string $className */
            return $this->deserialize($value, $className);
        }

        return $value;
    }
}
