<?php

namespace App\Enums;

enum WaterMarkEnum: int
{
    case UP_LEFT = 1;
    case UP_RIGHT = 2;
    case MID = 3;
    case DOWN_LEFT = 4;
    case DOWN_RIGHT = 5;

    public function label(): string
    {
        return __('enums.waterMark.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}
