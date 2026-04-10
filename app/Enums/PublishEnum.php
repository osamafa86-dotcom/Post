<?php

namespace App\Enums;

enum PublishEnum: int
{
    case PUBLISHED = 1;
    case DRAFT = 2;
    case PENDING = 3;

    public function label(): string
    {
        return __('enums.publish.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }
}

