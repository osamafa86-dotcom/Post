<?php

namespace App\Enums;

enum AdvertisementPlaceEnum: int
{
    case SIDE_ADS = 1;
    case MAIN_ADS = 2;
    case MID_ADS = 3;
    case BOTTOM_ADS = 4;


    public function label(): string
    {
        return __('enums.advertisementPlace.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.advertisementPlace.undefined');
    }

    public static function available(): array
    {
        $config = config('features.advertisement_place', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }

}
