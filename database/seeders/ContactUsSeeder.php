<?php

namespace Database\Seeders;

use App\Models\ContactUs;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactUsSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 8; $i++) {
            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualContact();
            } else {
                $this->createSingleLanguageContact();
            }
        }
    }

    protected function createMultilingualContact(): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createContactForLocale($locale);
        }
    }

    protected function createSingleLanguageContact(): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createContactForLocale($locale);
    }

    protected function createContactForLocale(string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $fullName = $this->generateLocalizedName($locale, $faker);
        $subject = $this->generateLocalizedSubject($locale, $faker);
        $message = $this->generateLocalizedMessage($locale, $faker);

        ContactUs::create([
            'full_name' => $fullName,
            'email' => $faker->email(),
            'subject' => $subject,
            'message' => $message,
            'team_id' => null,
        ]);
    }

    protected function generateLocalizedName(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicFirstNames = ['أحمد', 'محمد', 'علي', 'خالد', 'سعيد', 'محمود', 'حسن', 'يوسف', 'طارق', 'مصطفى'];
            $arabicLastNames = ['الفهيد', 'الخالد', 'السعد', 'الرشيد', 'المبارك', 'الغامدي', 'الحمود', 'الجعفر', 'النجار', 'الزهراني'];
            return $arabicFirstNames[array_rand($arabicFirstNames)] . ' ' . $arabicLastNames[array_rand($arabicLastNames)];
        }

        return $faker->name();
    }

    protected function generateLocalizedSubject(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicSubjects = [
                'استفسار عن الخدمات المقدمة',
                'طلب معلومات إضافية',
                'مشكلة في استخدام الموقع',
                'اقتراح لتحسين الخدمة',
                'شكوى أو ملاحظة',
                'طلب دعم فني',
                'استفسار عن الأسعار',
                'تعليق على المحتوى',
                'طلب تعاون أو شراكة',
                'استفسار عام'
            ];
            return $arabicSubjects[array_rand($arabicSubjects)];
        }

        return $faker->sentence(rand(3, 6));
    }

    protected function generateLocalizedMessage(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicMessages = [
                'أرغب في الحصول على مزيد من المعلومات حول الخدمات التي تقدمونها. هل يمكنكم تزويدي بالتفاصيل الكاملة؟',
                'لدي استفسار بخصوص استخدام المنصة، وأحتاج إلى مساعدة في حل بعض المشاكل التقنية التي أواجهها.',
                'أود تقديم اقتراح لتحسين تجربة المستخدم على الموقع، حيث لاحظت بعض النقاط التي يمكن تطويرها.',
                'أعجبني المحتوى المقدم وأرغب في معرفة المزيد عن الخدمات الإضافية المتاحة للعملاء.',
                'واجهت مشكلة أثناء عملية التسجيل، وأحتاج إلى مساعدة لحلها في أسرع وقت ممكن.',
                'أرغب في التعاون معكم في مشروع مشترك، هل يمكنكم التواصل معي لمناقشة التفاصيل؟',
                'لدي ملاحظة حول أحد الخدمات المقدمة، وأعتقد أن تطويرها سيساهم في تحسين التجربة بشكل كبير.',
                'أحتاج إلى دعم فني بخصوص استخدام إحدى الميزات في المنصة، هل يمكنكم مساعدتي؟',
                'أرغب في معرفة المزيد عن العروض والتخفيضات الحالية على الخدمات المقدمة.',
                'لدي سؤال عام عن سياسة الاستخدام والشروط والأحكام المطبقة على المنصة.'
            ];
            return $arabicMessages[array_rand($arabicMessages)];
        }

        return $faker->paragraphs(rand(2, 4), true);
    }
}
