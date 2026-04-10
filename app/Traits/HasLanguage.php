<?php

namespace App\Traits;

use App\Scope\LanguageScope;

trait HasLanguage
{
    /**
     * Boot the trait.
     */
    protected static function bootHasLanguage(): void
    {
        if (config('app.active_languages_system')) {
            static::addGlobalScope(new LanguageScope);

            // اضبط قيمة اللغة تلقائياً عند الإنشاء لو كانت فارغة
            static::creating(function ($model) {
                if (empty($model->lang)) {
                    $model->lang = config('app.locale'); // أو app()->getLocale()
                }
            });
        }
    }
}
