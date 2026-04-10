<?php

namespace App\Enums;

enum AlertTypeEnum: int
{
    case DANGER = 1;
    case SUCCESS = 2;

    public function label(): string
    {
        return __('enums.alertType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    /**
     * @return int
     */
    public function status(): string
    {
        return __('enums.alertType.status.' . $this->value);
    }
}
