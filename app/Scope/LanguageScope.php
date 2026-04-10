<?php

namespace App\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LanguageScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!config('app.active_languages_system')) {
            return;
        }

        // هذا يضيف اسم العمود مؤهَّلاً بالجدول أو الألياس (مثلاً posts.lang)
        $column = $builder->qualifyColumn('lang');
        $locale = app()->getLocale(); // أو config('app.locale')

        $builder->where($column, $locale);
    }
}
