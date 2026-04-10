<?php

namespace App\Enums;

enum SubscriptionPaymentMethodEnum: int
{
    case PAYPAL = 1;
    case CREDIT_CARD = 2;
    case CASH = 3;


    public function label(): string
    {
        return __('enums.subscriptionPaymentMethod.' . $this->value);
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

