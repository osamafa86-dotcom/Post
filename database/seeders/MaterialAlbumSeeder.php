<?php

namespace Database\Seeders;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\MaterialAlbum;
use App\Models\MaterialAlbumRelation;
use App\Models\ModelHasFile;
use App\Models\Tag;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class MaterialAlbumSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تجهيز البيانات المشتركة مرة واحدة
        $imageFiles = Files::whereIn('extension', ['jpg', 'jpeg', 'png', 'webp'])->pluck('id')->toArray();
        $materialTypes = array_map(fn($case) => $case->value, MaterialTypeEnum::cases());

        $categoriesPool = $this->getCategoriesPool();
        $tagsByType = $this->getTagsByType();

        for ($i = 1; $i <= 10; $i++) {
            $commonData = [
                'type' => Arr::random($materialTypes),
            ];

            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualAlbum($commonData, $imageFiles, $categoriesPool, $tagsByType, $i);
            } else {
                $this->createSingleLanguageAlbum($commonData, $imageFiles, $categoriesPool, $tagsByType, $i);
            }
        }
    }

    protected function createMultilingualAlbum(array $commonData, array $imageFiles, array $categoriesPool, array $tagsByType, int $index): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $materialAlbum = $this->createAlbumForLocale($commonData, $locale, $index);
            $this->attachAlbumRelations($materialAlbum, $imageFiles, $categoriesPool, $tagsByType);
        }
    }

    protected function createSingleLanguageAlbum(array $commonData, array $imageFiles, array $categoriesPool, array $tagsByType, int $index): void
    {
        $locale = $this->getWebsiteLocale();
        $materialAlbum = $this->createAlbumForLocale($commonData, $locale, $index);
        $this->attachAlbumRelations($materialAlbum, $imageFiles, $categoriesPool, $tagsByType);
    }

    protected function createAlbumForLocale(array $commonData, string $locale, int $index): MaterialAlbum
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $name = $this->generateLocalizedAlbumName($commonData['type'], $locale, $faker, $index);
        $description = $this->generateLocalizedAlbumDescription($commonData['type'], $locale, $faker);

        return MaterialAlbum::create([
            'name' => $name,
            'description' => $description,
            'type' => $commonData['type'],
        ]);
    }

    protected function generateLocalizedAlbumName(int $albumType, string $locale, $faker, int $index): string
    {
        if ($locale === 'ar') {
            $typeBasedNames = [
                MaterialTypeEnum::PODCAST->value => [
                    'مجموعة البودكاست المميزة',
                    'سلسلة الحلقات الصوتية',
                    'برامج البودكاست الحصرية',
                    'مجموعة المقابلات الصوتية',
                    'حلقات البودكاست الشهرية'
                ],
                MaterialTypeEnum::VIDEO->value => [
                    'مجموعة الفيديوهات التعليمية',
                    'سلسلة الفيديوهات الحصرية',
                    'مكتبة الفيديوهات المميزة',
                    'مجموعة العروض المرئية',
                    'فيديوهات الشهر الخاصة'
                ],
                MaterialTypeEnum::IMAGE->value => [
                    'أرشيف الأخبار المهمة',
                    'مجموعة التقارير الإخبارية',
                    'الأخبار الحصرية المختارة',
                    'ملفات الأخبار الخاصة',
                    'مجموعة التحليلات الإخبارية'
                ]
            ];

            $names = $typeBasedNames[$albumType] ?? ['مجموعة المواد المميزة', 'سلسلة المحتويات الحصرية'];
            return $names[array_rand($names)] . ' ' . ($index + 1);
        }

        $typeBasedNames = [
            MaterialTypeEnum::PODCAST->value => ['Premium Podcast Collection', 'Audio Series Bundle', 'Exclusive Podcast Programs'],
            MaterialTypeEnum::VIDEO->value => ['Educational Video Collection', 'Exclusive Video Series', 'Premium Video Library'],
            MaterialTypeEnum::IMAGE->value => ['Important News Archive', 'News Reports Collection', 'Exclusive News Selection']
        ];

        $names = $typeBasedNames[$albumType] ?? ['Featured Materials Collection', 'Exclusive Content Series'];
        return $names[array_rand($names)] . ' ' . ($index + 1);
    }

    protected function generateLocalizedAlbumDescription(int $albumType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $typeBasedDescriptions = [
                MaterialTypeEnum::PODCAST->value => 'مجموعة حصرية من حلقات البودكاست المميزة التي تناقش أهم المواضيع وتقدم محتوى قيماً للمستمعين.',
                MaterialTypeEnum::VIDEO->value => 'سلسلة من الفيديوهات المختارة بعناية لتقديم محتوى مرئي مميز يلبي مختلف الاهتمامات والاحتياجات.',
                MaterialTypeEnum::IMAGE->value => 'أرشيف شامل للأخبار والتقارير المهمة التي تغطي مختلف المجالات والمواضيع الحيوية.'
            ];

            return $typeBasedDescriptions[$albumType] ?? 'مجموعة مميزة من المواد والمحتويات المختارة بعناية لتقديم أفضل تجربة للمستخدم.';
        }

        $typeBasedDescriptions = [
            MaterialTypeEnum::PODCAST->value => 'An exclusive collection of premium podcast episodes discussing important topics and providing valuable content for listeners.',
            MaterialTypeEnum::VIDEO->value => 'A carefully selected series of videos offering distinctive visual content that meets various interests and needs.',
            MaterialTypeEnum::IMAGE->value => 'A comprehensive archive of important news and reports covering various fields and vital topics.'
        ];

        return $typeBasedDescriptions[$albumType] ?? 'A distinguished collection of carefully selected materials and content to provide the best user experience.';
    }

    protected function getCategoriesPool(): array
    {
        $categoryTypes = array_map(fn($case) => $case->value, CategoryTypeEnum::cases());
        $categoriesByType = [];

        foreach ($categoryTypes as $type) {
            $categoriesByType[$type] = Category::where('category_type', $type)->pluck('id')->toArray();
        }

        return collect($categoriesByType)->flatten()->values()->all();
    }

    protected function getTagsByType(): array
    {
        $tagTypes = array_map(fn($case) => $case->value, TagTypeEnum::cases());
        $tagsByType = [];

        foreach ($tagTypes as $type) {
            $tagsByType[$type] = Tag::where('tag_type', $type)->pluck('id')->toArray();
        }

        return $tagsByType;
    }

    protected function attachAlbumRelations($materialAlbum, $imageFiles, $categoriesPool, $tagsByType): void
    {
        // ربط صورة غلاف
        if (!empty($imageFiles)) {
            ModelHasFile::create([
                'file_id'      => Arr::random($imageFiles),
                'model_type'   => MaterialAlbum::class,
                'model_id'     => $materialAlbum->id,
                'model_column' => 'image',
                'team_id'      => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // ربط تصنيفات
        if (!empty($categoriesPool)) {
            $take = min(rand(1, 2), count($categoriesPool));
            $randomCategories = Arr::random($categoriesPool, $take);

            foreach ((array) $randomCategories as $catId) {
                MaterialAlbumRelation::create([
                    'material_album_id'    => $materialAlbum->id,
                    'relationable_id'      => $catId,
                    'relationable_type'    => Category::class,
                    'relationable_is_main' => 1,
                ]);
            }
        }

        // ربط وسوم
        foreach ($tagsByType as $tagIds) {
            if (!empty($tagIds)) {
                $take = min(rand(1, 3), count($tagIds));
                $randomTags = Arr::random($tagIds, $take);

                foreach ((array) $randomTags as $tagId) {
                    MaterialAlbumRelation::create([
                        'material_album_id'    => $materialAlbum->id,
                        'relationable_id'      => $tagId,
                        'relationable_type'    => Tag::class,
                        'relationable_is_main' => 0,
                    ]);
                }
            }
        }
    }
}
