<?php

namespace Database\Seeders;

use App\Models\PendingAction;
use App\Models\Post;
use App\Models\User;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PendingActionSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $posts = Post::take(5)->get();

        if (!$user || $posts->isEmpty()) {
            $this->command->info('Skipping PendingActionSeeder - need users and posts first.');
            return;
        }

        $actionConfigs = $this->generateActionConfigs();

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualActions($user, $posts, $actionConfigs);
        } else {
            $this->createSingleLanguageActions($user, $posts, $actionConfigs);
        }
    }

    protected function createMultilingualActions(User $user, $posts, array $actionConfigs): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createActionsForLocale($user, $posts, $actionConfigs, $locale);
        }
    }

    protected function createSingleLanguageActions(User $user, $posts, array $actionConfigs): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createActionsForLocale($user, $posts, $actionConfigs, $locale);
    }

    protected function generateActionConfigs(): array
    {
        return [
            ['type' => 'edit', 'publish' => 'pending', 'index' => 0],
            ['type' => 'create', 'publish' => 'draft', 'index' => 1],
            ['type' => 'delete', 'publish' => 'pending', 'index' => 2],
            ['type' => 'publish', 'publish' => 'approved', 'index' => 3],
            ['type' => 'unpublish', 'publish' => 'review', 'index' => 4],
        ];
    }

    protected function createActionsForLocale(User $user, $posts, array $actionConfigs, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);

        foreach ($posts as $postIndex => $post) {
            foreach ($actionConfigs as $config) {
                $actionData = [
                    'title' => $this->generateLocalizedActionTitle($config['type'], $locale, $faker),
                    'content' => $this->generateLocalizedActionContent($config['type'], $locale, $faker),
                    'reason' => $this->generateLocalizedActionReason($config['type'], $locale, $faker),
                ];

                $note = $this->getTranslatedNote($config['type'], $locale);

                PendingAction::firstOrCreate([
                    'user_id' => $user->id,
                    'actionable_id' => $post->id,
                    'actionable_type' => Post::class,
                    'actionable_status' => $config['type'],

                ], [
                    'actionable_data' => json_encode($actionData, JSON_UNESCAPED_UNICODE),
                    'actionable_publish' => $config['publish'],
                    'actionable_note' => $note,
                    'team_id' => null,

                ]);
            }
        }
    }

    protected function generateLocalizedActionTitle(string $actionType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            return match($actionType) {
                'edit' => 'تعديل على المحتوى الحالي',
                'create' => 'إنشاء محتوى جديد',
                'delete' => 'طلب حذف المحتوى',
                'publish' => 'نشر المحتوى الجديد',
                'unpublish' => 'إيقاف نشر المحتوى',
                default => 'إجراء معلق'
            };
        }

        return match($actionType) {
            'edit' => 'Edit Existing Content',
            'create' => 'Create New Content',
            'delete' => 'Delete Content Request',
            'publish' => 'Publish New Content',
            'unpublish' => 'Unpublish Content',
            default => 'Pending Action'
        };
    }

    protected function generateLocalizedActionContent(string $actionType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $contents = [
                'edit' => 'تم إجراء تعديلات على المحتوى الحالي وتحتاج إلى المراجعة والموافقة قبل التطبيق.',
                'create' => 'تم إنشاء محتوى جديد ويحتاج إلى المراجعة والموافقة قبل النشر.',
                'delete' => 'تم طلب حذف هذا المحتوى ويحتاج إلى التأكيد قبل المتابعة.',
                'publish' => 'المحتوى جاهز للنشر ويحتاج إلى الموافقة النهائية.',
                'unpublish' => 'تم طلب إيقاف نشر هذا المحتوى ويحتاج إلى المراجعة.'
            ];
            return $contents[$actionType] ?? 'هذا الإجراء يحتاج إلى مراجعة ومعالجة من قبل المسؤول.';
        }

        return $faker->paragraphs(2, true);
    }

    protected function generateLocalizedActionReason(string $actionType, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $reasons = [
                'edit' => 'تحسين جودة المحتوى وتحديث المعلومات',
                'create' => 'إضافة محتوى جديد يلبي احتياجات المستخدمين',
                'delete' => 'المحتوى لم يعد مناسباً أو متوافقاً مع السياسات',
                'publish' => 'المحتوى مستوفٍ لجميع الشروط وجاهز للعرض',
                'unpublish' => 'المحتوى يحتاج إلى تحديث أو مراجعة'
            ];
            return $reasons[$actionType] ?? 'إجراء روتيني للمراجعة والتحسين';
        }

        return $faker->sentence();
    }

    protected function getTranslatedNote(string $actionType, string $locale): string
    {
        $notes = [
            'edit' => [
                'ar' => 'يرجى مراجعة التعديلات على المحتوى',
                'en' => 'Please review content changes'
            ],
            'create' => [
                'ar' => 'مسودة جديدة تحتاج للموافقة',
                'en' => 'New draft requires approval'
            ],
            'delete' => [
                'ar' => 'طلب حذف المحتوى',
                'en' => 'Content deletion request'
            ],
            'publish' => [
                'ar' => 'المحتوى جاهز للنشر',
                'en' => 'Content ready for publishing'
            ],
            'unpublish' => [
                'ar' => 'طلب إيقاف النشر',
                'en' => 'Request to unpublish content'
            ],
        ];

        return $notes[$actionType][$locale] ?? $notes[$actionType]['en'];
    }
}
