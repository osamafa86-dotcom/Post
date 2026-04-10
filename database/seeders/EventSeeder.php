<?php

namespace Database\Seeders;

use App\Enums\DateTypeEnum;
use App\Models\Event;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventsConfig = [
            ['url' => '/events/weekly-workshop', 'date_type' => DateTypeEnum::TIME->value],
            ['url' => '/events/special-conference', 'date_type' => DateTypeEnum::DATE_TIME->value],
            ['url' => '/events/regular-meeting', 'date_type' => DateTypeEnum::TIME->value],
            ['url' => '/events/annual-gala', 'date_type' => DateTypeEnum::DATE_TIME->value],
            ['url' => '/events/training-session', 'date_type' => DateTypeEnum::TIME->value],
            ['url' => '/events/networking-event', 'date_type' => DateTypeEnum::DATE_TIME->value],
        ];

        foreach ($eventsConfig as $eventConfig) {
            if ($this->isMultilingualEnabled()) {
                $this->createMultilingualEvent($eventConfig);
            } else {
                $this->createSingleLanguageEvent($eventConfig);
            }
        }
    }

    protected function createMultilingualEvent(array $eventConfig): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createEventForLocale($eventConfig, $locale);
        }
    }

    protected function createSingleLanguageEvent(array $eventConfig): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createEventForLocale($eventConfig, $locale);
    }

    protected function createEventForLocale(array $eventConfig, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $title = $this->generateLocalizedEventTitle($eventConfig['url'], $locale, $faker);
        $description = $this->generateLocalizedEventDescription($eventConfig['url'], $locale, $faker);

        Event::firstOrCreate(
            ['title' => $title],
            [
                'title' => $title,
                'description' => $description,
                'url' => $eventConfig['url'],
                'date_type' => $eventConfig['date_type'],
                'team_id' => null,
                'lang' => $locale, // تخزين اللغة
            ]
        );
    }

    protected function generateLocalizedEventTitle(string $url, string $locale, $faker): string
    {
        $eventType = $this->getEventTypeFromUrl($url);

        if ($locale === 'ar') {
            return match($eventType) {
                'weekly-workshop' => 'ورشة العمل الأسبوعية',
                'special-conference' => 'المؤتمر الخاص',
                'regular-meeting' => 'الاجتماع الدوري',
                'annual-gala' => 'الحفل السنوي',
                'training-session' => 'جلسة التدريب',
                'networking-event' => 'فعالية التواصل',
                default => 'فعالية خاصة'
            };
        }

        return match($eventType) {
            'weekly-workshop' => 'Weekly Workshop',
            'special-conference' => 'Special Conference',
            'regular-meeting' => 'Regular Meeting',
            'annual-gala' => 'Annual Gala',
            'training-session' => 'Training Session',
            'networking-event' => 'Networking Event',
            default => 'Special Event'
        };
    }

    protected function generateLocalizedEventDescription(string $url, string $locale, $faker): string
    {
        $eventType = $this->getEventTypeFromUrl($url);

        if ($locale === 'ar') {
            return match($eventType) {
                'weekly-workshop' => 'ورشة عمل أسبوعية تهدف إلى تطوير المهارات وتبادل الخبرات بين المشاركين في جو تفاعلي ممتع.',
                'special-conference' => 'مؤتمر خاص يجمع الخبراء والمتخصصين لمناقشة أحدث التطورات والاتجاهات في المجال.',
                'regular-meeting' => 'اجتماع دوري لمناقشة سير العمل واتخاذ القرارات الهامة المتعلقة بالمشاريع الجارية.',
                'annual-gala' => 'حفل سنوي لتكريم المتميزين والاحتفاء بالإنجازات التي تحققت على مدار العام.',
                'training-session' => 'جلسة تدريبية مكثفة تهدف إلى تطوير المهارات العملية والمعرفة النظرية للمشاركين.',
                'networking-event' => 'فعالية تواصل مهني تتيح فرصة للقاء والتفاعل مع محترفين من مختلف التخصصات.',
                default => 'فعالية مميزة تهدف إلى تحقيق أهداف محددة وتقديم قيمة مضافة للحضور.'
            };
        }

        return match($eventType) {
            'weekly-workshop' => 'A weekly workshop aimed at developing skills and exchanging experiences among participants in an interactive and enjoyable atmosphere.',
            'special-conference' => 'A special conference bringing together experts and specialists to discuss the latest developments and trends in the field.',
            'regular-meeting' => 'A regular meeting to discuss work progress and make important decisions related to ongoing projects.',
            'annual-gala' => 'An annual gala to honor outstanding individuals and celebrate achievements made throughout the year.',
            'training-session' => 'An intensive training session aimed at developing practical skills and theoretical knowledge for participants.',
            'networking-event' => 'A professional networking event that provides an opportunity to meet and interact with professionals from various fields.',
            default => 'A special event aimed at achieving specific goals and providing added value to attendees.'
        };
    }

    protected function getEventTypeFromUrl(string $url): string
    {
        $parts = explode('/', $url);
        return end($parts);
    }
}
