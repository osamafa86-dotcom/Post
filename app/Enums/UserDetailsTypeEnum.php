<?php

namespace App\Enums;

enum UserDetailsTypeEnum: int
{
    case CONTENT_CREATOR = 1;
    case HONGORA_TEAM = 2;

    public function label(): string
    {
        return __('enums.userDetailsType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}

