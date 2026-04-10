<?php

namespace Database\Seeders;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\AdvertisementTypeEnum;
use App\Enums\AdvertisementUrlTargetEnum;
use App\Enums\PublishEnum;
use App\Models\Advertisement;
use App\Models\Files;
use App\Models\ModelHasFile;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imageFiles = Files::whereIn('extension', ['jpg', 'jpeg', 'png', 'webp'])->pluck('id')->toArray();

        $samples = [
            ['place' => AdvertisementPlaceEnum::SIDE_ADS->value, 'type' => AdvertisementTypeEnum::PHOTO->value],
            ['place' => AdvertisementPlaceEnum::MAIN_ADS->value, 'type' => AdvertisementTypeEnum::PHOTO->value],
            ['place' => AdvertisementPlaceEnum::MID_ADS->value,  'type' => AdvertisementTypeEnum::CODE->value],
            ['place' => AdvertisementPlaceEnum::SIDE_ADS->value, 'type' => AdvertisementTypeEnum::CODE->value],
            ['place' => AdvertisementPlaceEnum::MAIN_ADS->value, 'type' => AdvertisementTypeEnum::PHOTO->value],
            ['place' => AdvertisementPlaceEnum::MID_ADS->value,  'type' => AdvertisementTypeEnum::PHOTO->value],
        ];

        foreach ($samples as $i => $sample) {
            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualAdvertisement($sample, $imageFiles, $i);
            } else {
                $this->createSingleLanguageAdvertisement($sample, $imageFiles, $i);
            }
        }
    }

    protected function createMultilingualAdvertisement(array $sample, array $imageFiles, int $index): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createAdvertisementForLocale($sample, $imageFiles, $index, $locale);
        }
    }

    protected function createSingleLanguageAdvertisement(array $sample, array $imageFiles, int $index): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createAdvertisementForLocale($sample, $imageFiles, $index, $locale);
    }

    protected function createAdvertisementForLocale(array $sample, array $imageFiles, int $index, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء عنوان مناسب للغة
        $title = $this->generateLocalizedTitle($locale, $faker);

        // إنشاء الإعلان مع البيانات المترجمة
        $ad = Advertisement::create([
            'title' => $title,
            'image' => null,
            'type' => $sample['type'],
            'place' => $sample['place'],
            'url' => $faker->url(),
            'url_target' => AdvertisementUrlTargetEnum::NEW_WINDOW->value,
            'code' => $sample['type'] === AdvertisementTypeEnum::CODE->value ? '<div class="ad">AD CODE</div>' : null,
            'end_hour_time' => null,
            'end_min_time' => null,
            'user_id' => null,
            'publish_status' => PublishEnum::PUBLISHED->value,
            'team_id' => null,
            'lang' => $locale, // تخزين اللغة
        ]);

        // Add image for photo-type ads
        if ($sample['type'] === AdvertisementTypeEnum::PHOTO->value && !empty($imageFiles)) {
            ModelHasFile::create([
                'file_id' => $faker->randomElement($imageFiles),
                'model_type' => Advertisement::class,
                'model_id' => $ad->id,
                'model_column' => 'image',
                'team_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function generateLocalizedTitle(string $locale, $faker): string
    {
        $arabicWords = [
            'إعلان', 'عرض', 'تخفيض', 'مميز', 'جديد', 'حصري', 'محدود', 'رائع',
            'فرصة', 'سعر', 'منتج', 'خدمة', 'متجر', 'موقع', 'تطبيق', 'برنامج'
        ];

        $englishWords = [
            'Ad', 'Offer', 'Discount', 'Special', 'New', 'Exclusive', 'Limited', 'Amazing',
            'Opportunity', 'Price', 'Product', 'Service', 'Store', 'Website', 'App', 'Program'
        ];

        if ($locale === 'ar') {
            // توليد عنوان عربي
            $wordCount = rand(2, 4);
            $words = [];
            for ($i = 0; $i < $wordCount; $i++) {
                $words[] = $arabicWords[array_rand($arabicWords)];
            }
            return implode(' ', $words);
        } else {
            // استخدام الفيكر للغات الأخرى
            return $faker->words(rand(2, 4), true);
        }
    }
}
