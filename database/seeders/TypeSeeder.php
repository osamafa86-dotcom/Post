<?php

namespace Database\Seeders;

use App\Models\Type;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeCount = 20;

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualTypes($typeCount);
        } else {
            $this->createSingleLanguageTypes($typeCount);
        }
    }

    protected function createMultilingualTypes(int $count): void
    {
        $languages = $this->getLanguages();
        $typesPerLanguage = ceil($count / count($languages));

        foreach ($languages as $locale) {
            for ($i = 0; $i < $typesPerLanguage; $i++) {
                $this->createTypeForLocale($locale, $i);
            }
        }
    }

    protected function createSingleLanguageTypes(int $count): void
    {
        $locale = $this->getWebsiteLocale();

        for ($i = 0; $i < $count; $i++) {
            $this->createTypeForLocale($locale, $i);
        }
    }

    protected function createTypeForLocale(string $locale, int $index): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء اسم النوع المترجم يدوياً لكل لغة
        $typeName = $this->generateLocalizedTypeName($locale, $faker, $index);

        Type::create([
            'type_name' => $typeName,
            'lang' => $locale, // تخزين اللغة
        ]);
    }

    protected function generateLocalizedTypeName(string $locale, $faker, int $index): string
    {
        if ($locale === 'ar') {
            $arabicTypes = [
                // أنواع المحتوى
                'مقال', 'تقرير', 'تحليل', 'رأي', 'تحقيق',
                'مقابلة', 'تغطية', 'برنامج', 'حلقة', 'بودكاست',
                'فيديو', 'إنفوجرافيك', 'صور', 'رسم', 'تصميم',

                // أنواع الوسائط
                'نصي', 'مرئي', 'سمعي', 'تفاعلي', 'متعدد الوسائط',
                'مباشر', 'مسجل', 'مشفر', 'مفتوح', 'حصري',

                // أنواع التصنيف
                'أساسي', 'فرعي', 'مشترك', 'مخصص', 'افتراضي',
                'داخلي', 'خارجي', 'عام', 'خاص', 'مؤقت',

                // أنواع المستخدمين
                'عادي', 'مميز', 'احترافي', 'شركة', 'مؤسسة',
                'تعليمي', 'حكومي', 'غير ربحي', 'فردي', 'جماعي',

                // أنواع الملفات
                'وثيقة', 'عرض', 'جدول', 'قاعدة بيانات', 'سكريبت',
                'قالب', 'مكتبة', 'أداة', 'برنامج', 'تطبيق',

                // أنواع الأحداث
                'ندوة', 'ورشة عمل', 'مؤتمر', 'معرض', 'دورة',
                'مسابقة', 'جائزة', 'تكريم', 'احتفال', 'مناسبة'
            ];
            return $arabicTypes[array_rand($arabicTypes)];
        }

        $englishTypes = [
            // Content Types
            'Article', 'Report', 'Analysis', 'Opinion', 'Investigation',
            'Interview', 'Coverage', 'Program', 'Episode', 'Podcast',
            'Video', 'Infographic', 'Images', 'Drawing', 'Design',

            // Media Types
            'Text', 'Visual', 'Audio', 'Interactive', 'Multimedia',
            'Live', 'Recorded', 'Encrypted', 'Open', 'Exclusive',

            // Classification Types
            'Basic', 'Sub', 'Shared', 'Custom', 'Default',
            'Internal', 'External', 'Public', 'Private', 'Temporary',

            // User Types
            'Regular', 'Premium', 'Professional', 'Company', 'Institution',
            'Educational', 'Government', 'Non-profit', 'Individual', 'Group',

            // File Types
            'Document', 'Presentation', 'Spreadsheet', 'Database', 'Script',
            'Template', 'Library', 'Tool', 'Program', 'Application',

            // Event Types
            'Seminar', 'Workshop', 'Conference', 'Exhibition', 'Course',
            'Competition', 'Award', 'Recognition', 'Celebration', 'Occasion'
        ];
        return $englishTypes[array_rand($englishTypes)];
    }
}
