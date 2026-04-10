<?php

namespace App\Enums;

enum TagTypeEnum: int
{
    case NEWS = 1;
    case INTEREST = 2;
    case EVENTS = 3;
    case MATERIALS = 4;
    case SKILLS = 5;
    case SPECIALIZATION = 6;
    case USERS = 7;
    case LANGUAGES = 8;
    case DIALECTS = 9;

    public function label(): string
    {
        return __('enums.tagType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }


    public static function available(): array
    {
        $config = config('features.tag_type', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }
}
