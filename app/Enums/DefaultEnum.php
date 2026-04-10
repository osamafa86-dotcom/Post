<?php

namespace App\Enums;

enum DefaultEnum: int
{
    case YES = 1;
    case NO = 2;

    public function label(): string
    {
        return __('enums.default.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}

