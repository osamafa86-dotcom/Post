<?php

namespace Database\Seeders;

use App\Enums\AlertTypeEnum;
use App\Models\Alert;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alertTypes = [
            [
                'type' => AlertTypeEnum::DANGER->value,
                'status' => true,
                'theme' => 'security',
            ],
            [
                'type' => AlertTypeEnum::SUCCESS->value,
                'status' => false,
                'theme' => 'success',
            ],
        ];

        foreach ($alertTypes as $alertType) {
            $this->createAlertWithTranslations($alertType);
        }
    }

    protected function createAlertWithTranslations(array $alertType): void
    {
        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualAlert($alertType);
        } else {
            $this->createSingleLanguageAlert($alertType);
        }
    }

    protected function createMultilingualAlert(array $alertType): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createAlertForLocale($alertType, $locale);
        }
    }

    protected function createSingleLanguageAlert(array $alertType): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createAlertForLocale($alertType, $locale);
    }

    protected function createAlertForLocale(array $alertType, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $title = $this->generateLocalizedAlertTitle($alertType['type'], $locale, $faker);
        $message = $this->generateLocalizedAlertMessage($alertType['type'], $locale, $faker);

        Alert::create([
            'title' => $title,
            'type' => $alertType['type'],
            'status' => $alertType['status'],
            'content' => json_encode([
                'message' => $message,
                'theme' => $alertType['theme']
            ], JSON_UNESCAPED_UNICODE),
            'team_id' => null,
        ]);
    }

    protected function generateLocalizedAlertTitle(string $alertType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            return match($alertType) {
                AlertTypeEnum::DANGER->value => 'تحذير  مهم',
                AlertTypeEnum::SUCCESS->value => 'عملية ناجحة',

                default => 'إشعار النظام'
            };
        }

        return match($alertType) {
            AlertTypeEnum::DANGER->value => 'Important Security Alert',
            AlertTypeEnum::SUCCESS->value => 'Operation Successful',

            default => 'System Notification'
        };
    }

    protected function generateLocalizedAlertMessage(string $alertType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            return match($alertType) {
                AlertTypeEnum::DANGER->value => 'يوجد مشكلة أمنية تتطلب انتباهك الفوري. يرجى التحقق من إعدادات الحساب.',
                AlertTypeEnum::SUCCESS->value => 'تم تنفيذ العملية بنجاح. يمكنك متابعة استخدام النظام بشكل طبيعي.',

                default => 'هذا إشعار عام من نظام الإدارة.'
            };
        }

        return match($alertType) {
            AlertTypeEnum::DANGER->value => 'There is a security issue that requires your immediate attention. Please check your account settings.',
            AlertTypeEnum::SUCCESS->value => 'The operation has been completed successfully. You can continue using the system normally.',

            default => 'This is a general notification from the management system.'
        };
    }
}
