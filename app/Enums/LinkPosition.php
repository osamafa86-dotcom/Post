<?php

namespace App\Enums;

enum LinkPosition: int
{
    case HEADER = 1;
    case FOOTER = 2;
    case HeaderAndFooter = 3;
    case AllPlaces = 4;

    public function label(): string
    {
        return __('enums.linkPosition.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}
