<?php
namespace App\Enums;

enum LoggableModelsEnum: int
{
    case MATERIAL        = 1;
    case POST            = 2;
    case PARTICIPANT     = 3;
    case CATEGORY        = 4;
    case PODCAST_ALBUM   = 5;
    case VIDEO_ALBUM     = 6;
    case SETTING         = 7;
    case BREAKING_NEWS   = 8;
    case TYPE            = 9;
    case SPECIAL_FILE    = 10;
    case TAG             = 11;
    case QUOTE           = 12;
    case SPECIAL_PAGE    = 13;
    case ADVERTISEMENT   = 14;
    case PODCAST_TRACK   = 15;
    case VIDEO_TRACK     = 16;
    case ALERT           = 17;
    case USER            = 18;
    case CONTACT_US      = 19;
    case SEND_NEWS       = 20;

    public function label(): string
    {
        return __('enums.models.' . $this->value);
    }

    public static function fromValue(?int $value): string
    {
        $case = self::tryFrom($value);
        return $case?->label() ?? __('enums.default.undefined');
    }

    public function fields(): array {
        return __('enums.fields.' . $this->modelClass());
    }
    public static function fromModel(string $class): ?self
    {
        return match ($class) {
            \App\Models\Material::class       => self::MATERIAL,
            \App\Models\Post::class           => self::POST,
            \App\Models\Participant::class    => self::PARTICIPANT,
            \App\Models\Category::class       => self::CATEGORY,
            \App\Models\PodcastAlbum::class   => self::PODCAST_ALBUM,
            \App\Models\VideoAlbum::class     => self::VIDEO_ALBUM,
            \App\Models\Setting::class        => self::SETTING,
            \App\Models\BreakingNews::class   => self::BREAKING_NEWS,
            \App\Models\Type::class           => self::TYPE,
            \App\Models\SpecialFile::class    => self::SPECIAL_FILE,
            \App\Models\Tag::class            => self::TAG,
            \App\Models\Quote::class          => self::QUOTE,
            \App\Models\SpecialPage::class    => self::SPECIAL_PAGE,
            \App\Models\Advertisement::class  => self::ADVERTISEMENT,
            \App\Models\PodcastTrack::class   => self::PODCAST_TRACK,
            \App\Models\VideoTrack::class     => self::VIDEO_TRACK,
            \App\Models\Alert::class          => self::ALERT,
            \App\Models\User::class           => self::USER,
            \App\Models\ContactUs::class      => self::CONTACT_US,
            \App\Models\SendNews::class       => self::SEND_NEWS,
            default => null,
        };
    }

    public function modelClass(): string
    {
        return match ($this) {
            self::MATERIAL       => \App\Models\Material::class,
            self::POST           => \App\Models\Post::class,
            self::PARTICIPANT    => \App\Models\Participant::class,
            self::CATEGORY       => \App\Models\Category::class,
            self::PODCAST_ALBUM  => \App\Models\PodcastAlbum::class,
            self::VIDEO_ALBUM    => \App\Models\VideoAlbum::class,
            self::SETTING        => \App\Models\Setting::class,
            self::BREAKING_NEWS  => \App\Models\BreakingNews::class,
            self::TYPE           => \App\Models\Type::class,
            self::SPECIAL_FILE   => \App\Models\SpecialFile::class,
            self::TAG            => \App\Models\Tag::class,
            self::QUOTE          => \App\Models\Quote::class,
            self::SPECIAL_PAGE   => \App\Models\SpecialPage::class,
            self::ADVERTISEMENT  => \App\Models\Advertisement::class,
            self::PODCAST_TRACK  => \App\Models\PodcastTrack::class,
            self::VIDEO_TRACK    => \App\Models\VideoTrack::class,
            self::ALERT          => \App\Models\Alert::class,
            self::USER           => \App\Models\User::class,
            self::CONTACT_US     => \App\Models\ContactUs::class,
            self::SEND_NEWS      => \App\Models\SendNews::class,
        };
    }
}
