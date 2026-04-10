<?php

namespace App\Enums;

enum LinkStatusEnum: int
{
    case CATEGORY = 1;
    case TAG = 2;
    case PODCAST = 3;
    case VIDEO = 4;
    case SPECIAL_PAGE = 5;
    case CUSTOM_LINK = 6;
    case TYPE = 7;

    public function label(): string
    {
        return __('enums.linkStatus.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    public static function tryFromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        return null;
    }

    public static function available(): array
    {
        $config = config('features.link_status', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }
}
