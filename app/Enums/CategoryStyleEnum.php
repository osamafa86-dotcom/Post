<?php

namespace App\Enums;

enum CategoryStyleEnum: int
{
//almaidan
    case ONE_BIG_FOUR_SMALL_STYLE = 1;
    case FOUR_BIG_REST_SMALL_STYLE = 2;
    case ONE_BIG_TWO_MID_STYLE = 3;

// almashhad
    case THREE_MID_WITH_SUB_STYLE = 4;
    case THREE_PODCAST_STYLE = 5;
    case ONE_BIG_THREE_MID_VIDEO_STYLE = 6;
    case TWO_BIG_FOUR_MID_STYLE = 7;
    case FOUR_MID_STYLE = 8;
    case SIX_WIDTH_MID_STYLE = 9;
    case THREE_HEIGHT_MID_STYLE = 10;
    case ONE_HEIGHT_SIX_SUB = 11;
    case SIX_SLIDER = 12;
    case ONE_HEIGHT_FOUR_SUB = 13;
    case THREE_MID_WITH_THREE_SUB_SMALL = 14;

// hodhod
    case ONE_BIG_THREE_MID_STYLE = 15;
    case ONE_BIG_FOUR_MID_VIDEO_STYLE = 16;
    case ONE_MID_WITH_THREE_SUB_STYLE = 17;
    case THREE_MID_WITH_SLIDER_STYLE = 18;
    case TWO_BIG_THREE_MID_STYLE = 19;
    case ONE_BIG_FOUR_MID_STYLE = 20;
    case ONE_BIG_ONE_WIDE_TWO_MID_STYLE = 21;

// maktoob
    case ONE_MID_WITH_VERTICAL_SLIDER = 22;
    case FOUR_MID_WITH_SUB_STYLE = 23;
    case ONE_BIG_WITH_FOUR_SUB_STYLE = 24;
    case ONE_BIG_VIDEO_WITH_THREE_SUB = 25;
    case THREE_MID_SLIDER_STYLE = 26;
    case THREE_MID_SLIDER_DARK_STYLE = 27;
    case TWO_CATEGORY_STYLE = 28;
    case THREE_SUB_ONE_MID_FOUR_SUB = 29; // it was on almohajer need to check

// almohajer
    case FOUR_MID_ONE_MID_SLIDER_TWO_SUB = 30;
    case EIGHT_MID_EIGHT_SMALL_SUB = 31;
    case ONE_MID_SLIDER_THREE_SUB = 32;
    case ONE_MID_THREE_SUB = 33;

// fimed
    case FIMED_REPORTS = 34;         // 1 featured big + 4 list cards
    case FIMED_DARK_VOICE = 35;      // 3 cards on dark background
    case FIMED_CATEGORY_GRID = 36;   // 4 cards grid with colored badges
    case FIMED_OPINION = 37;         // 3 opinion cards with author avatar


    public function label(): string
    {
        return __('enums.categoryStyle.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    private static function isEnabled(self $case): bool
    {
        // config('features.category_style_enum.case_name') should be true/false
        return (bool)(config('features.category_style_enum.' . $case->name) ?? false);
    }

    /** Styles that conceptually belong to "video". */
    private static function videoSet(): array
    {
        return [
            self::ONE_BIG_THREE_MID_VIDEO_STYLE,
            self::ONE_BIG_VIDEO_WITH_THREE_SUB,
            self::ONE_HEIGHT_FOUR_SUB,
        ];
    }

    /** Styles that conceptually belong to "podcast". */
    private static function podcastSet(): array
    {
        return [self::THREE_PODCAST_STYLE];
    }

    public static function available($type = null): array
    {
        if ($type === null) {
            return [];
        }

        $enabled = array_filter(self::cases(), fn(self $c) => self::isEnabled($c));
        $video = self::videoSet();
        $podcast = self::podcastSet();

        if ($type == CategoryTypeEnum::PODCAST->value) {
            // Only podcast styles (and enabled)
            return array_values(array_filter($enabled, fn(self $c) => in_array($c, $podcast, true)));
        }

        if ($type == CategoryTypeEnum::VIDEO->value) {
            // Only video styles (and enabled)
            return array_values(array_filter($enabled, fn(self $c) => in_array($c, $video, true)));
        }

        if ($type == CategoryTypeEnum::NEWS->value) {
            // Everything enabled EXCEPT video/podcast styles
            $exclude = array_merge($video, $podcast);
            return array_values(array_filter($enabled, fn(self $c) => !in_array($c, $exclude, true)));
        }
        return [];
    }


}
