<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Services\Payment\Contract\PaymentGatewayContract;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PlanSeeder extends Seeder
{
    use HasMultilingualSeeder;

    private PaymentGatewayContract $paymentService;

    public function __construct(PaymentGatewayContract $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->resetPlansTable();

            $plans = $this->fetchPlansFromGateway();

            if (empty($plans['items'])) {
                $this->command->warn('No plans found from payment gateway. Creating sample plans...');
                $this->createSamplePlans();
                return;
            }

            $this->createPlansFromGateway($plans['items']);

            $this->command->info('Successfully seeded plans from payment gateway.');

        } catch (\Exception $e) {
            $this->command->error('Failed to fetch plans from payment gateway: ' . $e->getMessage());
            $this->command->warn('Creating sample plans as fallback...');
            $this->createSamplePlans();
        }
    }

    protected function resetPlansTable(): void
    {
        Schema::disableForeignKeyConstraints();
        Plan::truncate();
        Schema::enableForeignKeyConstraints();
    }

    protected function fetchPlansFromGateway(): array
    {
        return $this->paymentService->fetchPlans()->toArray();
    }

    protected function createPlansFromGateway(array $planItems): void
    {
        $createdCount = 0;

        foreach ($planItems as $plan) {
            if ($this->isValidPlan($plan)) {
                if ($this->isMultilingualEnabled()) {
                    $this->createMultilingualPlan($plan);
                } else {
                    $this->createSingleLanguagePlan($plan);
                }
                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} plans from payment gateway.");
    }

    protected function isValidPlan(array $plan): bool
    {
        return isset(
                $plan['id'],
                $plan['item']['id'],
                $plan['item']['name'],
                $plan['item']['amount'],
                $plan['item']['currency']
            ) && $plan['item']['active'];
    }

    protected function createMultilingualPlan(array $plan): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createPlanForLocale($plan, $locale);
        }
    }

    protected function createSingleLanguagePlan(array $plan): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createPlanForLocale($plan, $locale);
    }

    protected function createPlanForLocale(array $plan, string $locale): void
    {
        // ترجمة اسم ووصف الخطة حسب اللغة
        $itemName = $this->generateLocalizedPlanName($plan['item']['name'], $locale);
        $itemDescription = $this->generateLocalizedPlanDescription($plan['item']['name'], $locale);

        Plan::create([
            'plan_id' => $plan['id'],
            'type' => $plan['item']['currency'] == 'INR' ? 'india' : 'international',
            'period' => $plan['period'] ?? 'monthly',
            'item_id' => $plan['item']['id'],
            'item_name' => $itemName,
            'item_description' => $itemDescription,
            'item_amount' => $plan['item']['amount'],
            'item_currency' => $plan['item']['currency'],
            'item' => json_encode($plan['item']),
            'notes' => json_encode($plan['notes'] ?? []),
        ]);
    }

    protected function createSamplePlans(): void
    {
        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualSamplePlans();
        } else {
            $this->createSingleLanguageSamplePlans();
        }
    }

    protected function createMultilingualSamplePlans(): void
    {
        $languages = $this->getLanguages();
        $samplePlans = $this->getSamplePlans();

        foreach ($languages as $locale) {
            foreach ($samplePlans as $plan) {
                $this->createSamplePlanForLocale($plan, $locale);
            }
        }

        $this->command->info('Created ' . (count($samplePlans) * count($languages)) . ' sample plans across ' . count($languages) . ' languages.');
    }

    protected function createSingleLanguageSamplePlans(): void
    {
        $locale = $this->getWebsiteLocale();
        $samplePlans = $this->getSamplePlans();

        foreach ($samplePlans as $plan) {
            $this->createSamplePlanForLocale($plan, $locale);
        }

        $this->command->info('Created ' . count($samplePlans) . ' sample plans.');
    }

    protected function createSamplePlanForLocale(array $planData, string $locale): void
    {
        // ترجمة اسم ووصف الخطة حسب اللغة
        $itemName = $this->generateLocalizedPlanName($planData['item_name'], $locale);
        $itemDescription = $this->generateLocalizedPlanDescription($planData['item_name'], $locale);

        Plan::create([
            'plan_id' => $planData['plan_id'] . '_' . $locale,
            'type' => $planData['type'],
            'period' => $planData['period'],
            'item_id' => $planData['item_id'] . '_' . $locale,
            'item_name' => $itemName,
            'item_description' => $itemDescription,
            'item_amount' => $planData['item_amount'],
            'item_currency' => $planData['item_currency'],
            'item' => json_encode(['name' => $itemName, 'amount' => $planData['item_amount'], 'currency' => $planData['item_currency']]),
            'notes' => $planData['notes'],
        ]);
    }

    protected function generateLocalizedPlanName(string $originalName, string $locale): string
    {
        if ($locale === 'ar') {
            $nameTranslations = [
                'Basic Plan' => 'الخطة الأساسية',
                'Pro Plan' => 'الخطة الاحترافية',
                'India Plan' => 'الخطة الهندية',
                'Premium Plan' => 'الخطة المميزة',
                'Enterprise Plan' => 'الخطة المؤسسية',
                'Starter Plan' => 'الخطة المبدئية',
                'Business Plan' => 'الخطة التجارية',
                'Ultimate Plan' => 'الخطة الشاملة'
            ];

            return $nameTranslations[$originalName] ?? $originalName . ' (عربي)';
        }

        return $originalName;
    }

    protected function generateLocalizedPlanDescription(string $planName, string $locale): string
    {
        if ($locale === 'ar') {
            $descriptionTranslations = [
                'Basic Plan' => 'مثالية للبدء، تشمل الميزات الأساسية التي تحتاجها للانطلاق',
                'Pro Plan' => 'للمحترفين والشركات، مع ميزات متقدمة وأدوات متكاملة',
                'India Plan' => 'خطة مخصصة للعملاء في الهند مع طرق دفع محلية',
                'Premium Plan' => 'تجربة مميزة مع جميع الميزات المتقدمة والدعم المميز',
                'Enterprise Plan' => 'حلول شاملة للشركات الكبيرة مع دعم مخصص',
                'Starter Plan' => 'بداية رائعة مع الميزات الأساسية بأسعار مناسبة',
                'Business Plan' => 'كل ما تحتاجه لنمو عملك مع أدوات متقدمة',
                'Ultimate Plan' => 'الحل الشامل مع كل الميزات وأعلى مستويات الدعم'
            ];

            return $descriptionTranslations[$planName] ?? 'خطة شاملة تلبي احتياجاتك المختلفة';
        }

        // الوصف الإفتراضي بالإنجليزية
        $defaultDescriptions = [
            'Basic Plan' => 'Perfect for getting started with essential features',
            'Pro Plan' => 'For professionals and businesses with advanced tools',
            'India Plan' => 'Special plan for Indian customers with local payment methods',
            'Premium Plan' => 'Premium experience with all advanced features and priority support',
            'Enterprise Plan' => 'Comprehensive solutions for large enterprises with dedicated support',
            'Starter Plan' => 'Great start with basic features at affordable prices',
            'Business Plan' => 'Everything you need for business growth with advanced tools',
            'Ultimate Plan' => 'Complete solution with all features and highest support levels'
        ];

        return $defaultDescriptions[$planName] ?? 'Comprehensive plan to meet your various needs';
    }

    protected function getSamplePlans(): array
    {
        return [
            [
                'plan_id' => 'sample_basic_monthly',
                'type' => 'international',
                'period' => 'monthly',
                'item_id' => 'item_basic',
                'item_name' => 'Basic Plan',
                'item_description' => 'Perfect for getting started',
                'item_amount' => 999,
                'item_currency' => 'USD',
                'notes' => json_encode(['feature' => 'Basic features included']),
            ],
            [
                'plan_id' => 'sample_pro_monthly',
                'type' => 'international',
                'period' => 'monthly',
                'item_id' => 'item_pro',
                'item_name' => 'Pro Plan',
                'item_description' => 'For professionals and businesses',
                'item_amount' => 1999,
                'item_currency' => 'USD',
                'notes' => json_encode(['feature' => 'All features included']),
            ],
            [
                'plan_id' => 'sample_india_yearly',
                'type' => 'india',
                'period' => 'yearly',
                'item_id' => 'item_india',
                'item_name' => 'India Plan',
                'item_description' => 'Special plan for Indian customers',
                'item_amount' => 49900,
                'item_currency' => 'INR',
                'notes' => json_encode(['feature' => 'Local payment methods']),
            ],
        ];
    }
}
