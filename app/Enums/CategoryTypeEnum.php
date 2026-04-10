<?php

namespace App\Enums;

enum CategoryTypeEnum: int
{
    case NEWS = 1;
    case LIST = 2;
    case PAGES = 3;
    case PODCAST = 4;
    case VIDEO = 5;
    case IMAGE = 6;
    case EVENTS = 7;
    case USERS = 8;
    case COURSES = 9;
    case VOLUNTEERING = 10;
    case CONFERENCE = 11;
    case TRAINING =12;


    public function label(): string
    {
        return __("enums.CategoryTypeEnum.{$this->value}");
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    public static function available(): array
    {
        $config = config('features.category_type', []);
        return array_filter(self::cases(), function ($case) use ($config) {
            return $config[$case->name] ?? false;
        });
    }


}
