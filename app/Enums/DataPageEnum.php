<?php

namespace App\Enums;

enum DataPageEnum: int
{
    case CODE = 1;
    case FILE = 2;
    case OPPORTUNITY = 3;
     case SCHOLARSHIP =4;


    public function label(): string
    {
        return __('enums.dataPage.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}

