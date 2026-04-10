<?php

namespace App\Enums;

enum GenderEnum: int
{
    case MALE = 1;
    case FEMALE = 2;

    public function label(): string
    {
        return __('enums.gender.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

}
