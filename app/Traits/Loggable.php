<?php

namespace App\Traits;

use App\Enums\LoggableModelsEnum;
use App\Enums\PublishEnum;
use App\Models\UserLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    public static function bootLoggable()
    {
        static::created(function (Model $model) {
            self::audit(\App\Enums\ActionsEnum::CREATE->value, $model,null,json_encode($model));
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            $changedField = array_key_first($changes);

            if ($changedField === 'publish_status') {
                if ($changes['publish_status'] == PublishEnum::PUBLISHED->value) {
                    self::audit(PublishEnum::PUBLISHED->value, $model, null, null);
                } else {
                    self::audit(\App\Enums\ActionsEnum::PEND->value, $model, null, null);
                }
            } else {
                self::audit(
                    \App\Enums\ActionsEnum::EDIT->value,
                    $model,
                    json_encode($model->getRawOriginal(), JSON_UNESCAPED_UNICODE),
                    json_encode($changes, JSON_UNESCAPED_UNICODE)
                );
            }
        });

        static::deleted(function (Model $model) {
            self::audit(\App\Enums\ActionsEnum::DELETE->value, $model, null,null);
        });
    }

    protected static function audit($description, Model $model,$original,$changes)
    {
        $modelEnum = LoggableModelsEnum::fromModel($model::class);
        UserLog::create([
            'user_id' => Auth::id(),
            'action_status' => $description,
            'actionable_id'=>$model->id ?? null,
            'actionable_type'=>$modelEnum,
            'actionable_data_before'=>$original,
            'actionable_data_after'=>$changes,
        ]);
    }
}
