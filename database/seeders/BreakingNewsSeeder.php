<?php

namespace Database\Seeders;

use App\Enums\PublishEnum;
use App\Models\BreakingNews;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BreakingNewsSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newsCount = 10;

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualBreakingNews($newsCount);
        } else {
            $this->createSingleLanguageBreakingNews($newsCount);
        }
    }

    protected function createMultilingualBreakingNews(int $count): void
    {
        $languages = $this->getLanguages();
        $newsPerLanguage = ceil($count / count($languages));

        foreach ($languages as $locale) {
            for ($i = 0; $i < $newsPerLanguage; $i++) {
                $this->createBreakingNewsForLocale($locale, $i);
            }
        }
    }

    protected function createSingleLanguageBreakingNews(int $count): void
    {
        $locale = $this->getWebsiteLocale();

        for ($i = 0; $i < $count; $i++) {
            $this->createBreakingNewsForLocale($locale, $i);
        }
    }

    protected function createBreakingNewsForLocale(string $locale, int $index): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء العنوان المترجم يدوياً لكل لغة
        $title = $this->generateLocalizedBreakingNewsTitle($locale, $faker, $index);

        BreakingNews::create([
            'title' => $title,
            'url' => $faker->url(),
            'hide_date' => now()->addDays(rand(1, 10)),
            'publish_status' => PublishEnum::PUBLISHED->value,
            'team_id' => null,
            'lang' => $locale, // تخزين اللغة
        ]);
    }

    protected function generateLocalizedBreakingNewsTitle(string $locale, $faker, int $index): string
    {
        if ($locale === 'ar') {
            $arabicTitles = [
                'تطورات جديدة في الأحداث الجارية',
                'بيان عاجل من المصادر الرسمية',
                'آخر المستجدات في الملفات الساخنة',
                'تفاصيل جديدة في القضية المطروحة',
                'تحديثات فورية حول الأوضاع الحالية',
                'تصريحات هامة من المسؤولين',
                'تطورات متسارعة في الملف',
                'أنباء عاجلة من الميدان',
                'تفاصيل حصرية حول الأخبار',
                'آخر التطورات في الأحداث',
                'تقارير جديدة تكشف التفاصيل',
                'بيانات مهمة من الجهات المعنية',
                'تحديثات حية من موقع الأحداث',
                'تصريحات عاجلة من المصادر',
                'معلومات جديدة تظهر للحظة'
            ];
            return $arabicTitles[array_rand($arabicTitles)];
        }

        return $faker->sentence(rand(4, 8));
    }
}
