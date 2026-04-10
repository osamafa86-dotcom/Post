<?php

namespace App\Enums;

enum VideoTypeEnum: int
{
    case YOUTUBE = 1;
    case TELEGRAM = 2;
    case LOCAL = 3;

    public function label(): string
    {
        return __('enums.videoType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    public static function available(): array
    {
        $config = config('features.video_type', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }
}
