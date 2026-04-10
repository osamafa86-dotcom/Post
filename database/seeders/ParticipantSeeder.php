<?php

namespace Database\Seeders;

use App\Enums\ParticipantTypeEnum;
use App\Models\Files;
use App\Models\ModelHasFile;
use App\Models\Participant;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
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

        $participantCount = 20;

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualParticipants($participantCount, $imageFiles);
        } else {
            $this->createSingleLanguageParticipants($participantCount, $imageFiles);
        }
    }

    protected function createMultilingualParticipants(int $count, array $imageFiles): void
    {
        $languages = $this->getLanguages();
        $participantsPerLanguage = ceil($count / count($languages));

        foreach ($languages as $locale) {
            for ($i = 0; $i < $participantsPerLanguage; $i++) {
                $this->createParticipantForLocale($locale, $imageFiles);
            }
        }
    }

    protected function createSingleLanguageParticipants(int $count, array $imageFiles): void
    {
        $locale = $this->getWebsiteLocale();

        for ($i = 0; $i < $count; $i++) {
            $this->createParticipantForLocale($locale, $imageFiles);
        }
    }

    protected function createParticipantForLocale(string $locale, array $imageFiles): void
    {
        $faker = $this->createFakerForLocale($locale);
        $participantType = $this->getRandomParticipantType();

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $participantData = [
            'name' => $this->generateLocalizedName($locale, $faker),
            'description' => $this->generateLocalizedDescription($locale, $faker),
            'work' => $this->generateLocalizedWork($locale, $faker),
            'type' => $participantType,
            'participants_data' => null,
            'lang' => $locale,
        ];

        // إنشاء المشارك
        $participant = Participant::create($participantData);

        $this->attachParticipantImage($participant, $imageFiles);
        $this->updateParticipantData($participant, $faker);
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

    protected function generateLocalizedDescription(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicDescriptions = [
                'مختص في مجال التكنولوجيا والبرمجة مع خبرة تزيد عن 10 سنوات.',
                'خبير في التسويق الرقمي واستراتيجيات التواصل الاجتماعي.',
                'مستشار أعمال معتمد مع سجل حافل في تحسين الأداء المؤسسي.',
                'باحث أكاديمي متخصص في الدراسات الإنسانية والاجتماعية.',
                'مطور تطبيقات ويب وجوال مع مشاريع ناجحة على مستوى عالمي.',
                'مدرب معتمد في التنمية البشرية وتطوير المهارات القيادية.',
                'كاتب ومحرر محتوى مع خبرة في وسائل الإعلام المختلفة.',
                'خبير في التصميم الجرافيكي والهوية البصرية للمؤسسات.',
                'مستشار تقني مع خبرة في تحول المؤسسات الرقمية.',
                'متخصص في تحليل البيانات واتخاذ القرارات المستندة إلى الأدلة.'
            ];
            return $arabicDescriptions[array_rand($arabicDescriptions)];
        }

        return $faker->paragraph(2);
    }

    protected function generateLocalizedWork(string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicJobs = [
                'مطور برمجيات',
                'مدير مشاريع',
                'مصمم جرافيك',
                'كاتب محتوى',
                'مدير تسويق',
                'باحث علمي',
                'مستشار أعمال',
                'مدرب معتمد',
                'محلل بيانات',
                'مطور ويب',
                'مهندس نظم',
                'أخصائي تسويق رقمي',
                'مدير مبيعات',
                'مصمم واجهات',
                'مطور تطبيقات'
            ];
            return $arabicJobs[array_rand($arabicJobs)];
        }

        return $faker->jobTitle();
    }

    protected function getRandomParticipantType(): string
    {
        $types = [
            ParticipantTypeEnum::PUBLISHERS->value,
            ParticipantTypeEnum::RESOURCES->value,
            ParticipantTypeEnum::PRESENTERS->value,
            ParticipantTypeEnum::GUESTS->value,
            ParticipantTypeEnum::AUTHORS->value,
        ];

        return $types[array_rand($types)];
    }

    protected function attachParticipantImage(Participant $participant, array $imageFiles): void
    {
        if (!ModelHasFile::where([
            ['model_id', $participant->id],
            ['model_type', Participant::class]
        ])->exists()) {
            ModelHasFile::create([
                'file_id' => fake()->randomElement($imageFiles),
                'model_type' => Participant::class,
                'model_id' => $participant->id,
                'model_column' => 'image',
                'team_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function updateParticipantData(Participant $participant, $faker): void
    {
        if (in_array($participant->type, [
            ParticipantTypeEnum::PUBLISHERS->value,
            ParticipantTypeEnum::RESOURCES->value
        ])) {
            $participant->update([
                'participants_data' => [
                    'url' => $faker->url(),
                ]
            ]);
        }
    }
}
