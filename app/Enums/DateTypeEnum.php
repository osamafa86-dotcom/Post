<?php

namespace App\Enums;

enum DateTypeEnum: int
{
    case TIME = 1;
    case DATE_TIME = 2;

    public function label(): string
    {
        return __('enums.dateType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    public static function available(): array
    {
        $config = config('features.date_type', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }
}
