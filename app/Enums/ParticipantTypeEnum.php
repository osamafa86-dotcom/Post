<?php

namespace App\Enums;

enum ParticipantTypeEnum: int
{
    case AUTHORS = 1;
    case PRESENTERS = 2;
    case GUESTS = 3;
    case CLIENTS = 4;
    case PARTNERS = 5;
    case TEAM = 6;
    case SUPPORTERS = 7;
    case PUBLISHERS =8;
    case RESOURCES =9;

    public function label(): string
    {
        return __('enums.participantType.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }


    public static function available(): array
    {
        $config = config('features.participant_type', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }
}
