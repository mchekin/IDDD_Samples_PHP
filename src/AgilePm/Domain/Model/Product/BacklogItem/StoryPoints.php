<?php declare(strict_types=1);

namespace App\AgilePm\Domain\Model\Product\BacklogItem;

enum StoryPoints: int
{
    case ZERO = 0;
    case ONE = 1;
    case TWO = 2;
    case THREE = 3;
    case FIVE = 5;
    case EIGHT = 8;
    case THIRTEEN = 13;
    case TWENTY = 20;
    case FORTY = 40;
    case ONE_HUNDRED = 100;

    public function pointValue(): int
    {
        return $this->value;
    }
}