<?php

namespace App\Enums;

enum UserStatusEnum: int
{
    case ADMIN = 1;
    case DATA_ENTRY = 2;
    case EDITOR = 3;
    case VOICEOVER = 4;
    case SUBSCRIBER = 5;

    public function label(): string
    {
        return __('enums.userStatus.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}
