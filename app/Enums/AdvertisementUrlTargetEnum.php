<?php

namespace App\Enums;

enum AdvertisementUrlTargetEnum: int
{
    case SAME_WINDOW = 1;
    case NEW_WINDOW = 2;

    public function label(): string
    {
        return __('enums.advertisementUrlTarget.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}
