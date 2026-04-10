<?php

namespace App\Enums;

enum AdvertisementTypeEnum: int
{
    case PHOTO = 1;
    case CODE = 2;

    public function label(): string
    {
        return __('enums.advertisementType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}
