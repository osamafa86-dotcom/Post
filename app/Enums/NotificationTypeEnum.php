<?php

namespace App\Enums;

enum NotificationTypeEnum: int
{
    case NOTIFICATION = 1;
    case EVENT = 2;

    public function label(): string
    {
        return __('enums.notificationType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

}
