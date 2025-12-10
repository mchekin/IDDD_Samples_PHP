<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\BacklogItem;

enum BacklogItemType: string
{
    case FEATURE = 'feature';
    case ENHANCEMENT = 'enhancement';
    case DEFECT = 'defect';
    case FOUNDATION = 'foundation';
    case INTEGRATION = 'integration';

    public function isDefect(): bool
    {
        return $this === self::DEFECT;
    }

    public function isEnhancement(): bool
    {
        return $this === self::ENHANCEMENT;
    }

    public function isFeature(): bool
    {
        return $this === self::FEATURE;
    }

    public function isFoundation(): bool
    {
        return $this === self::FOUNDATION;
    }

    public function isIntegration(): bool
    {
        return $this === self::INTEGRATION;
    }
}