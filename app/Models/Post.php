<?php

namespace App\Models;

use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Traits\HasLanguage;
use App\Traits\Loggable;
use App\Traits\PublishableBoot;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Post extends Model
{
    use HasFactory,
        SoftDeletes,
        Loggable,
        PublishableBoot,
        Tenantable;
    use HasLanguage;

    protected $guarded = [];
    protected $casts = [
        'publish_date' => 'datetime',
//        'publish_status' => PublishEnum::class,
//        'news_type' => NewsTypeEnum::class,
//        'image_size' => ImageSizeTypeEnum::class,
    ];

    protected static function booted(): void
    {
        if (config('features.general.special_content_available') && method_exists(static::class, 'post_special_content')) {
            static::addGlobalScope('special_content_available', function (Builder $builder) {
                $builder->where('publish_status', PublishEnum::PUBLISHED->value)
                    ->where(function ($q) {
                        $q->doesntHave('post_special_content')
                            ->orWhereHas('post_special_content', function ($qq) {
                                $qq->whereNull('available_date')
                                    ->orWhere('available_date', '<=', now());
                            });
                    });
            });
        }
    }

    private function relation($type, $hasOne = false, $participantType = null): HasOne|HasMany
    {
        $rel = $hasOne
            ? $this->hasOne(PostRelation::class, 'post_id')
            : $this->hasMany(PostRelation::class, 'post_id');

        $rel->where('relationable_type', $type);

        if ($hasOne) {
            $rel->where('relationable_is_main', 1);
        }

        // فلترة نوع الـ Participant عبر المورف نفسه
        if (!is_null($participantType) && $type === Participant::class) {
            // participants.type BIGINT ⇒ لازم INT
            $value = is_object($participantType) && property_exists($participantType, 'value')
                ? (int)$participantType->value
                : (int)$participantType;

            $rel->whereHasMorph('relationable', [Participant::class], function ($q) use ($value) {
                $q->where('participants.type', $value);
                // لو Participant عليه SoftDeletes وبدك تضم المحذوفين:
                // if (method_exists($q->getModel(), 'withTrashed')) $q->withTrashed();
            });
        }

        return $rel;
    }

    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function categories(): HasMany
    {
        return $this->relation(Category::class);
    }

    public function category(): HasOne
    {
        return $this->relation(Category::class, true);
    }

    public function authors(): HasMany
    {
        return $this->relation(Participant::class, false, ParticipantTypeEnum::AUTHORS->value);
    }

    public function author(): HasOne
    {
        return $this->relation(Participant::class, true, ParticipantTypeEnum::AUTHORS->value);
    }

    public function publishers(): HasMany
    {
        return $this->relation(Participant::class, false, ParticipantTypeEnum::PUBLISHERS->value);
    }

    public function publisher(): HasOne
    {
        return $this->relation(Participant::class, true, ParticipantTypeEnum::PUBLISHERS->value);
    }

    public function resources(): HasMany
    {
        return $this->relation(Participant::class, false, ParticipantTypeEnum::RESOURCES->value);
    }

    public function resource(): HasOne
    {
        return $this->relation(Participant::class, true, ParticipantTypeEnum::RESOURCES->value);
    }

    public function type(): HasOne
    {
        return $this->relation(Type::class, true);
    }

    public function types(): HasMany
    {
        return $this->relation(Type::class);
    }

    public function tags(): HasMany
    {
        return $this->relation(Tag::class);
    }

    public function post_special_content(): HasOne
    {
        return $this->hasOne(PostSpecialContent::class);
    }

    public function tag(): HasOne
    {
        return $this->relation(Tag::class, true);
    }

    public function special_files(): HasMany
    {
        return $this->relation(SpecialFile::class);
    }

    public function special_file(): HasOne
    {
        return $this->relation(SpecialFile::class, true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user_logs(): MorphMany
    {
        return $this->morphMany(UserLog::class, 'actionable');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ModelHasFile::class, 'model_id')->where('model_type', Post::class);
    }

    public function thumbnail(): HasOne
    {
        return $this->hasOne(ModelHasFile::class, 'model_id')
            ->where('model_type', Post::class)
            ->where('model_column', 'image');
    }

    public function getThumbnailAttribute()
    {
        return $this->thumbnail()->first()?->file?->path;
    }

    public function sortable(): MorphOne
    {
        return $this->morphOne(SortData::class, 'sortable');
    }

    /**
     * Get all translations of this post (posts sharing the same translation_group).
     */
    public function translations()
    {
        return $this->hasMany(Post::class, 'translation_group', 'translation_group')
            ->withoutGlobalScopes()
            ->where('id', '!=', $this->id);
    }

    /**
     * Get the translation for a specific language.
     */
    public function translation(string $lang)
    {
        return $this->translations()->where('lang', $lang)->first();
    }

    /**
     * Get all languages that have a translation for this post.
     */
    public function getTranslatedLanguagesAttribute(): array
    {
        if (!$this->translation_group) {
            return [$this->lang ?? config('app.locale')];
        }

        return Post::withoutGlobalScopes()
            ->where('translation_group', $this->translation_group)
            ->pluck('lang')
            ->unique()
            ->toArray();
    }

    /**
     * Get languages that DON'T have a translation yet.
     */
    public function getMissingTranslationsAttribute(): array
    {
        $enabledLanguages = config('app.enabled_languages', ['ar', 'en']);
        $existingLanguages = $this->translated_languages;

        return array_values(array_diff($enabledLanguages, $existingLanguages));
    }

    /**
     * Get the translation map (lang => post_id) for quick lookups.
     */
    public function getTranslationMapAttribute(): array
    {
        if (!$this->translation_group) {
            return [$this->lang ?? config('app.locale') => $this->id];
        }

        return Post::withoutGlobalScopes()
            ->where('translation_group', $this->translation_group)
            ->pluck('id', 'lang')
            ->toArray();
    }
}
