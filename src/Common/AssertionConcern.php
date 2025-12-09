<?php

declare(strict_types=1);

namespace App\Common;

class AssertionConcern
{
    protected function __construct() {}

    protected function assertArgumentEquals(object $anObject1, object $anObject2, string $aMessage): void
    {
        // Check if both objects have equals() method (Value Objects)
        if (method_exists($anObject1, 'equals')) {
            if (!$anObject1->equals($anObject2)) {
                throw new \InvalidArgumentException($aMessage);
            }
        } else {
            // Fallback to strict comparison for objects without equals()
            if ($anObject1 !== $anObject2) {
                throw new \InvalidArgumentException($aMessage);
            }
        }
    }

    protected function assertArgumentFalse(bool $aBoolean, string $aMessage): void
    {
        if ($aBoolean) {
            throw new \InvalidArgumentException($aMessage);
        }
    }

    protected function assertArgumentLength(string $aString, int $aMaximum, string $aMessage): void
    {
        $length = strlen(trim($aString));
        if ($length > $aMaximum) {
            throw new \InvalidArgumentException($aMessage);
        }
    }

    protected function assertArgumentLengthRange(string $aString, int $aMinimum, int $aMaximum, string $aMessage): void
    {
        $length = strlen(trim($aString));
        if ($length < $aMinimum || $length > $aMaximum) {
            throw new \InvalidArgumentException($aMessage);
        }
    }

    protected function assertArgumentNotEmpty(string $aString, string $aMessage): void
    {
        if (trim($aString) === '') {
            throw new \InvalidArgumentException($aMessage);
        }
    }

    protected function assertArgumentNotEquals(object $anObject1, object $anObject2, string $aMessage): void
    {
        // Check if both objects have equals() method (Value Objects)
        if (method_exists($anObject1, 'equals')) {
            if ($anObject1->equals($anObject2)) {
                throw new \InvalidArgumentException($aMessage);
            }
        } else {
            // Fallback to strict comparison for objects without equals()
            if ($anObject1 === $anObject2) {
                throw new \InvalidArgumentException($aMessage);
            }
        }
    }

    protected function assertArgumentNotNull(mixed $anObject, string $aMessage): void
    {
        if ($anObject === null) {
            throw new \InvalidArgumentException($aMessage);
        }
    }

    protected function assertArgumentRange(int|float $aValue, int|float $aMinimum, int|float $aMaximum, string $aMessage): void
    {
        if ($aValue < $aMinimum || $aValue > $aMaximum) {
            throw new \InvalidArgumentException($aMessage);
        }
    }

    protected function assertArgumentTrue(bool $aBoolean, string $aMessage): void
    {
        if (!$aBoolean) {
            throw new \InvalidArgumentException($aMessage);
        }
    }

    protected function assertStateFalse(bool $aBoolean, string $aMessage): void
    {
        if ($aBoolean) {
            throw new InvalidStateException($aMessage);
        }
    }

    protected function assertStateTrue(bool $aBoolean, string $aMessage): void
    {
        if (!$aBoolean) {
            throw new InvalidStateException($aMessage);
        }
    }
}
