<?php

namespace App\Enums;

enum ActionablePublishEnum: int
{
    case PENDING = 1;
    case PUBLISH = 2;
    case DRAFT = 3;
    case REJECTED = 4;
    case EDIT_NEEDED = 5;

    public function label(): string
    {
        return __('enums.actionablePublish.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}
