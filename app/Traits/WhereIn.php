<?php

namespace App\Traits;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\AdvertisementTypeEnum;
use App\Enums\AdvertisementUrlTargetEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\DataPageEnum;
use App\Enums\DateTypeEnum;
use App\Enums\DayEnum;
use App\Enums\GenderEnum;
use App\Enums\ImageSizeTypeEnum;
use App\Enums\LinkStatusEnum;
use App\Enums\LinkUrlTargetEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\NotificationTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\SubscriptionPaymentMethodEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Enums\TagTypeEnum;
use App\Enums\UserDetailsTypeEnum;
use App\Enums\UserStatusEnum;
use App\Enums\VideoTypeEnum;
use App\Enums\WaterMarkEnum;
use App\Models\Category;
use Spatie\Permission\Models\Role as RoleModel;
use App\Models\MaterialAlbum;
use App\Models\SpecialFile;
use App\Models\Tag;
use App\Models\Type;
use App\Models\VideoAlbum;
use Spatie\Permission\Models\Role;

trait WhereIn
{
    public function whereIn(): array
    {
        return [
            'Role' => Role::query()->pluck('id')->implode(','),
            'Type' => Type::query()->pluck('id')->implode(','),
            'Category' => Category::query()->pluck('id')->implode(','),
            'RoleModel' => RoleModel::query()->pluck('id')->implode(','),
            'SpecialFile' => SpecialFile::query()->pluck('id')->implode(','),
            'Tag' => Tag::query()->pluck('id')->implode(','),
            'VideoAlbum' => VideoAlbum::query()->pluck('id')->implode(','),
            'MaterialAlbum' => MaterialAlbum::query()->pluck('id')->implode(','),
            'UserStatusEnum' => join(",", array_column(UserStatusEnum::cases(), 'value')),
            'UserDetailsTypeEnum' => join(",", array_column(UserDetailsTypeEnum::cases(), 'value')),
            'CategoryTypeEnum' => join(",", array_column(CategoryTypeEnum::available(), 'value')),
            'AdvertisementTypeEnum' => join(",", array_column(AdvertisementTypeEnum::cases(), 'value')),
            'AdvertisementPlaceEnum' => join(",", array_column(AdvertisementPlaceEnum::available(), 'value')),
            'AdvertisementUrlTargetEnum' => join(",", array_column(AdvertisementUrlTargetEnum::cases(), 'value')),
            'WaterMarkEnum' => join(",", array_column(WaterMarkEnum::cases(), 'value')),
            'LinkStatusEnum' => implode(',', array_merge(
                array_keys(config('features.link_status.default_pages', [])), array_column(LinkStatusEnum::available(), 'value')
            )),
            'LinkUrlTargetEnum' => join(",", array_column(LinkUrlTargetEnum::cases(), 'value')),
            'DateTypeEnum' => join(",", array_column(DateTypeEnum::available(), 'value')),
            'DayEnum' => join(",", array_column(DayEnum::cases(), 'value')),
            'NewsTypeEnum' => join(",", array_column(NewsTypeEnum::cases(), 'value')),
            'ImageSizeTypeEnum' => join(",", array_column(ImageSizeTypeEnum::available(), 'value')),
            'TagTypeEnum' => join(",", array_column(TagTypeEnum::available(), 'value')),
            'MaterialTypeEnum' => join(",", array_column(MaterialTypeEnum::available(), 'value')),
            'ParticipantTypeEnum' => join(",", array_column(ParticipantTypeEnum::available(), 'value')),
            'VideoTypeEnum' => join(",", array_column(VideoTypeEnum::available(), 'value')),
            'DataPageEnum' => join(",", array_column(DataPageEnum::cases(), 'value')),
            'GenderEnum' => join(",", array_column(GenderEnum::cases(), 'value')),
            'SubscriptionTypeEnum' => join(",", array_column(SubscriptionTypeEnum::cases(), 'value')),
            'SubscriptionStatusEnum' => join(",", array_column(SubscriptionStatusEnum::cases(), 'value')),
            'SubscriptionPaymentMethodEnum' => join(",", array_column(SubscriptionPaymentMethodEnum::cases(), 'value')),
            'NotificationType' => join(",", array_column(NotificationTypeEnum::cases(), 'value')),
        ];
    }
}
