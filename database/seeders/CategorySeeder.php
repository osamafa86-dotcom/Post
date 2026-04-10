<?php

namespace Database\Seeders;

use App\Enums\CategoryStyleEnum;
use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\ModelHasFile;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imageFiles = Files::whereIn('extension', ['jpg', 'jpeg', 'png', 'webp'])->pluck('id')->toArray();

        if (empty($imageFiles)) {
            $this->command->error('No images found in files table');
            return;
        }

        $order = 1;

        foreach (CategoryTypeEnum::available() as $enum) {
            for ($i = 0; $i < 5; $i++) {
                $isParent = $i === 0;

                $availableStyles = CategoryStyleEnum::available($enum->value);
                $randomStyle = !empty($availableStyles) ? fake()->randomElement($availableStyles)->value : null;

                if ($this->isMultilingualEnabled()) {
                    $this->createMultilingualCategory([
                        'is_parent' => $isParent,
                        'parent_id' => $isParent ? null : ($parent->id ?? null),
                        'category_type' => $enum->value,
                        'category_style' => $randomStyle,
                        'order' => $order++,
                    ], $imageFiles);
                } else {
                    $this->createSingleLanguageCategory([
                        'is_parent' => $isParent,
                        'parent_id' => $isParent ? null : ($parent->id ?? null),
                        'category_type' => $enum->value,
                        'category_style' => $randomStyle,
                        'order' => $order++,
                    ], $imageFiles);
                }

                // حفظ الأب للمرجعية
                if ($isParent) {
                    $parent = $category ?? null;
                }
            }
        }
    }

    protected function createMultilingualCategory(array $data, array $imageFiles): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $category = $this->createCategoryForLocale($data, $locale);

            // إضافة الصورة
            ModelHasFile::create([
                'file_id' => fake()->randomElement($imageFiles),
                'model_type' => Category::class,
                'model_id' => $category->id,
                'model_column' => 'image',
                'team_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function createSingleLanguageCategory(array $data, array $imageFiles): void
    {
        $locale = $this->getWebsiteLocale();
        $category = $this->createCategoryForLocale($data, $locale);

        // إضافة الصورة
        ModelHasFile::create([
            'file_id' => fake()->randomElement($imageFiles),
            'model_type' => Category::class,
            'model_id' => $category->id,
            'model_column' => 'image',
            'team_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createCategoryForLocale(array $data, string $locale): Category
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $categoryTitle = $this->generateLocalizedCategoryTitle($data['category_type'], $locale, $faker);
        $categoryDescription = $this->generateLocalizedCategoryDescription($data['category_type'], $locale, $faker);

        return Category::create([
            'category_title' => $categoryTitle,
            'category_description' => $categoryDescription,
            'parent_id' => $data['parent_id'],
            'category_type' => $data['category_type'],
            'category_style' => $data['category_style'],
            'show_index' => 1,
            'order' => $data['order'],
            'lang' => $locale, // تخزين اللغة
        ]);
    }

    protected function generateLocalizedCategoryTitle(int $categoryType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicTitles = [
                'أخبار عامة',
                'تقارير خاصة',
                'تحليلات',
                'مقالات رأي',
                'تحقيقات',
                'فيديوهات',
                'بودكاست',
                'فعاليات',
                'دورات تدريبية',
                'خدمات',
                'منتجات',
                'عروض',
                'أخبار رياضية',
                'أخبار اقتصادية',
                'أخبار تكنولوجيا'
            ];

            $typeBasedTitles = [
                CategoryTypeEnum::NEWS->value => ['أخبار', 'تقارير', 'أحداث', 'مستجدات'],
                CategoryTypeEnum::PODCAST->value => ['بودكاست', 'حلقات صوتية', 'برامج إذاعية'],
                CategoryTypeEnum::VIDEO->value => ['فيديوهات', 'مقاطع مرئية', 'برامج تلفزيونية'],
                CategoryTypeEnum::LIST->value => ['فعاليات', 'مناسبات', 'أحداث'],
                CategoryTypeEnum::COURSES->value => ['دورات', 'تدريبات', 'كورسات'],
                CategoryTypeEnum::CONFERENCE->value => ['خدمات', 'حلول', 'استشارات'],
            ];

            $titles = $typeBasedTitles[$categoryType] ?? $arabicTitles;
            return $titles[array_rand($titles)] . ' ' . $faker->randomElement(['المتنوعة', 'الحصرية', 'الجديدة', 'المميزة']);
        }

        return $faker->words(3, true);
    }

    protected function generateLocalizedCategoryDescription(int $categoryType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $descriptions = [
                'مجموعة متنوعة من المحتويات المميزة في هذا القسم',
                'اكتشف أحدث المحتويات والأخبار في هذا التصنيف',
                'محتوى حصري ومتميز يناقش أهم المواضيع',
                'تصفح مجموعة واسعة من المواد المختارة بعناية',
                'كل ما تحتاجه من معلومات ومواد في مكان واحد',
                'محتويات متنوعة تغطي مختلف الجوانب والاهتمامات',
                'استكشف عالم المعرفة من خلال هذا التصنيف الشامل'
            ];
            return $descriptions[array_rand($descriptions)];
        }

        return $faker->paragraph(2);
    }
}
