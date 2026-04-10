<?php

namespace App\Enums;

enum SubscriptionStatusEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;

    public function label(): string
    {
        return __('enums.subscriptionStatus.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    public static function tryFromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }
        return null;
    }

}
