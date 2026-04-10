<?php

namespace Database\Seeders;

use App\Models\Files;
use App\Models\ModelHasFile;
use App\Models\SpecialFile;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialFileSeeder extends Seeder
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

        for ($i = 1; $i <= 10; $i++) {
            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualSpecialFile($imageFiles, $i);
            } else {
                $this->createSingleLanguageSpecialFile($imageFiles, $i);
            }
        }
    }

    protected function createMultilingualSpecialFile(array $imageFiles, int $index): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createSpecialFileForLocale($locale, $imageFiles, $index);
        }
    }

    protected function createSingleLanguageSpecialFile(array $imageFiles, int $index): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createSpecialFileForLocale($locale, $imageFiles, $index);
    }

    protected function createSpecialFileForLocale(string $locale, array $imageFiles, int $index): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $fileName = $this->generateLocalizedFileName($locale, $faker, $index);
        $fileDescription = $this->generateLocalizedFileDescription($locale, $faker);

        // إنشاء الملف الخاص مع البيانات المترجمة
        $specialFile = SpecialFile::create([
            'file_name' => $fileName,
            'file_description' => $fileDescription,
            'lang' => $locale, // تخزين اللغة
        ]);

        $this->attachImage($specialFile, $imageFiles);
    }

    protected function generateLocalizedFileName(string $locale, $faker, int $index): string
    {
        if ($locale === 'ar') {
            $arabicFileNames = [
                'ملف التقارير السنوية',
                'وثيقة السياسات والإجراءات',
                'دليل المستخدم الشامل',
                'كتيب التعليمات والإرشادات',
                'تقرير الأداء الربعي',
                'ملف العروض التقديمية',
                'وثيقة الشروط والأحكام',
                'دليل التشغيل والصيانة',
                'كتيب أفضل الممارسات',
                'تقرير التحليل الشهري',
                'ملف المواصفات الفنية',
                'وثيقة الاستراتيجية الطويلة',
                'دليل الجودة والمعايير',
                'كتيب السلامة والأمان',
                'تقرير المتابعة والتقييم'
            ];
            return $arabicFileNames[array_rand($arabicFileNames)] . ' ' . $index;
        }

        return $faker->words(3, true) . ' ' . $index;
    }

    protected function generateLocalizedFileDescription(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicDescriptions = [
                'هذا الملف يحتوي على معلومات شاملة ومفصلة تغطي جميع الجوانب المتعلقة بالموضوع، مع تركيز على الدقة والوضوح في العرض.',
                'وثيقة معتمدة تحتوي على إرشادات وتوجيهات مهمة تساعد في فهم المتطلبات وتنفيذ المهام بالشكل الصحيح.',
                'دليل عملي مصمم لتسهيل عملية الاستخدام والفهم، مع أمثلة تطبيقية وشرح مفصل للخطوات والإجراءات.',
                'تقرير شامل يعرض البيانات والإحصائيات بشكل منظم، مع تحليل للنتائج وتقديم توصيات عملية للتحسين.',
                'ملف يحتوي على مجموعة من القوالب والنماذج الجاهزة للاستخدام، مما يوفر الوقت والجهد في إعداد المستندات.',
                'وثيقة مرجعية تحتوي على معلومات أساسية ومهمة يمكن الرجوع إليها عند الحاجة لأي استفسار أو توضيح.',
                'دليل إرشادي يوضح الطرق الصحيحة للتعامل مع الأنظمة والإجراءات، مع التركيز على تحقيق أفضل النتائج.',
                'تقرير مفصل يسلط الضوء على النقاط الرئيسية والإنجازات، مع تحليل للتحديات واقتراح حلول مناسبة.',
                'ملف تعريفي يحتوي على معلومات عامة عن الخدمات والميزات المتاحة، مع شرح لكيفية الاستفادة منها.',
                'وثيقة تنظيمية تحدد الأدوار والمسؤوليات، مع توضيح لآليات العمل وسير الإجراءات المتبعة.'
            ];
            return $arabicDescriptions[array_rand($arabicDescriptions)];
        }

        return $faker->paragraph(2);
    }

    protected function attachImage(SpecialFile $specialFile, array $imageFiles): void
    {
        ModelHasFile::create([
            'file_id' => fake()->randomElement($imageFiles),
            'model_type' => SpecialFile::class,
            'model_id' => $specialFile->id,
            'model_column' => 'image',
            'team_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
