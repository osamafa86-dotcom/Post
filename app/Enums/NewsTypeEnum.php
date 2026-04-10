<?php

namespace App\Enums;

enum NewsTypeEnum: int
{
    case MAIN_NEWS  = 1;
    case SUB_NEWS   = 2;

    public function label(): string
    {
        return __('enums.newsType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}

