<?php

namespace App\Enums;

enum ActionsEnum: int
{
    case CREATE = 1;
    case EDIT = 2;
    case PEND = 3;

    case DELETE = 4;
    case CHANGE_POST_STATUS = 5;

    public function label(): string
    {
        return __('enums.actions.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }




}
