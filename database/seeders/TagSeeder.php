<?php

namespace Database\Seeders;

use App\Enums\TagTypeEnum;
use App\Models\Tag;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagCount = 20;

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualTags($tagCount);
        } else {
            $this->createSingleLanguageTags($tagCount);
        }
    }

    protected function createMultilingualTags(int $count): void
    {
        $languages = $this->getLanguages();
        $tagsPerLanguage = ceil($count / count($languages));

        foreach ($languages as $locale) {
            for ($i = 0; $i < $tagsPerLanguage; $i++) {
                $this->createTagForLocale($locale, $i);
            }
        }
    }

    protected function createSingleLanguageTags(int $count): void
    {
        $locale = $this->getWebsiteLocale();

        for ($i = 0; $i < $count; $i++) {
            $this->createTagForLocale($locale, $i);
        }
    }

    protected function createTagForLocale(string $locale, int $index): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء اسم الوسم المترجم يدوياً لكل لغة
        $tagName = $this->generateLocalizedTagName($locale, $faker, $index);
        $tagType = $this->getRandomTagType();

        Tag::create([
            'tag_name' => $tagName,
            'tag_type' => $tagType->value,
            'lang' => $locale, // تخزين اللغة
        ]);
    }

    protected function generateLocalizedTagName(string $locale, $faker, int $index): string
    {
        if ($locale === 'ar') {
            $arabicTags = [
                // تكنولوجيا
                'الذكاء الاصطناعي', 'التعلم الآلي', 'بلوك تشين', 'الحوسبة السحابية', 'الأمن السيبراني',
                'إنترنت الأشياء', 'الواقع الافتراضي', 'البيانات الضخمة', 'التطبيقات الذكية', 'البرمجة',

                // أخبار
                'أخبار عاجلة', 'تغطية خاصة', 'تحليل إخباري', 'تقارير حصرية', 'مقابلات',
                'تحقيقات', 'آراء وتحليلات', 'فيديوهات إخبارية', 'بث مباشر', 'تغطية شاملة',

                // أعمال واقتصاد
                'الاستثمار', 'الأسواق المالية', 'ريادة الأعمال', 'التسويق الرقمي', 'الإدارة',
                'الاقتصاد العالمي', 'التمويل', 'التجارة الإلكترونية', 'الابتكار', 'الاستراتيجية',

                // صحة
                'الطب الوقائي', 'الصحة النفسية', 'التغذية', 'اللياقة البدنية', 'الأمراض المزمنة',
                'الرعاية الصحية', 'الطب البديل', 'الصحة العامة', 'العناية الذاتية', 'الرياضة',

                // ترفيه
                'أفلام', 'مسلسلات', 'موسيقى', 'فنون', 'ألعاب',
                'ثقافة', 'سفر', 'طهي', 'موضة', 'فعاليات',

                // تعليم
                'التعلم عن بعد', 'المهارات', 'التطوير المهني', 'اللغات', 'العلوم',
                'التقنية', 'الإبداع', 'القيادة', 'التخطيط', 'البحث'
            ];
            return $arabicTags[array_rand($arabicTags)];
        }

        $englishTags = [
            // Technology
            'Artificial Intelligence', 'Machine Learning', 'Blockchain', 'Cloud Computing', 'Cybersecurity',
            'Internet of Things', 'Virtual Reality', 'Big Data', 'Smart Apps', 'Programming',

            // News
            'Breaking News', 'Special Coverage', 'News Analysis', 'Exclusive Reports', 'Interviews',
            'Investigations', 'Opinions & Analysis', 'News Videos', 'Live Broadcast', 'Comprehensive Coverage',

            // Business & Economy
            'Investment', 'Financial Markets', 'Entrepreneurship', 'Digital Marketing', 'Management',
            'Global Economy', 'Finance', 'E-commerce', 'Innovation', 'Strategy',

            // Health
            'Preventive Medicine', 'Mental Health', 'Nutrition', 'Fitness', 'Chronic Diseases',
            'Healthcare', 'Alternative Medicine', 'Public Health', 'Self Care', 'Sports',

            // Entertainment
            'Movies', 'TV Series', 'Music', 'Arts', 'Games',
            'Culture', 'Travel', 'Cooking', 'Fashion', 'Events',

            // Education
            'Remote Learning', 'Skills', 'Professional Development', 'Languages', 'Sciences',
            'Technology', 'Creativity', 'Leadership', 'Planning', 'Research'
        ];
        return $englishTags[array_rand($englishTags)];
    }

    protected function getRandomTagType(): TagTypeEnum
    {
        $tagTypes = TagTypeEnum::cases();
        return $tagTypes[array_rand($tagTypes)];
    }
}
