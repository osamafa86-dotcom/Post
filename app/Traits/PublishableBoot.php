<?php

namespace App\Traits;

use App\Enums\PublishEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

trait PublishableBoot
{
    public static function bootPublishableBoot()
    {
/*        static::creating(function (Model $model) {
            $user = auth()->user();

            // Ensure there's an authenticated user
            if ($user) {
                $model->publisher_id = $user->id;
                $model->publish_status = $user->hasPermissionTo($model->getTable() . '_publish')
                    ? PublishEnum::PUBLISHED->value
                    : PublishEnum::DRAFT->value;
            } else {
                // Default values when no user is authenticated
                $model->publisher_id = null;
                $model->publish_status = PublishEnum::DRAFT->value;
            }
        });
*/
        static::addGlobalScope('publish_status', function (Builder $builder) {
            $user = auth()->user();
            $table = $builder->getModel()->getTable();
            $permissionName = $table . '_publish';

            if (!$user || !$user->checkPermissionTo($permissionName)) {
                $builder->where("publish_status", PublishEnum::PUBLISHED->value);
            }
        });
//        static::addGlobalScope('publish_status', function (Builder $builder) {
//            $user = auth()->user();
//            $permissionName = $builder->getModel()->getTable() . '_publish';
//
//            if (!$user || !in_array($permissionName, $user->getPermissionNames()->toArray())) {
//                $builder->where("publish_status", PublishEnum::PUBLISHED->value);
//            }
//        });
    }

}
