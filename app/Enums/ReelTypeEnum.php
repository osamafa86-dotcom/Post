<?php

namespace App\Enums;

enum ReelTypeEnum: int
{
    case YOUTUBE = 1;
    case INSTAGRAM = 2;
    case LOCAL = 3;

    public function label(): string
    {
        return __('enums.reelType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}

