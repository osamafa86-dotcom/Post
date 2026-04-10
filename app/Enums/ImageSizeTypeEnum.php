<?php

namespace App\Enums;

enum ImageSizeTypeEnum: int
{
    case LARGE_IMAGE = 1;
    case MID_IMAGE = 2;
    case SMALL_IMAGE = 3;
    case COVER_ARTICLE = 4;
    case SIDE_POST = 5;

    public function label(): string
    {
        return __('enums.imageSizeType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }


    public static function available(): array
    {
        $config = config('features.image_size_type', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }
}
