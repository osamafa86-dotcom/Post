<?php

namespace Database\Seeders;

use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LiveStreamSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $streamsConfig = [
            ['url' => 'https://youtube.com/live/tech-conference', 'hours' => 3],
            ['url' => 'https://youtube.com/live/product-launch', 'hours' => 2],
            ['url' => 'https://youtube.com/live/workshop-session', 'hours' => 4],
            ['url' => 'https://youtube.com/live/qna-session', 'hours' => 1],
            ['url' => 'https://youtube.com/live/training-course', 'hours' => 3],
        ];

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualStreams($streamsConfig);
        } else {
            $this->createSingleLanguageStreams($streamsConfig);
        }
    }

    protected function createMultilingualStreams(array $streamsConfig): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            foreach ($streamsConfig as $index => $streamConfig) {
                $this->createStreamForLocale($streamConfig, $index, $locale);
            }
        }
    }

    protected function createSingleLanguageStreams(array $streamsConfig): void
    {
        $locale = $this->getWebsiteLocale();

        foreach ($streamsConfig as $index => $streamConfig) {
            $this->createStreamForLocale($streamConfig, $index, $locale);
        }
    }

    protected function createStreamForLocale(array $streamConfig, int $index, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);

        // إنشاء العنوان المترجم يدوياً لكل لغة
        $title = $this->generateLocalizedStreamTitle($streamConfig['url'], $index, $locale, $faker);

        $this->insertStreamIfNotExists(
            $title,
            $streamConfig['url'],
            $streamConfig['hours'],
            $locale
        );
    }

    protected function generateLocalizedStreamTitle(string $url, int $index, string $locale, $faker): string
    {
        $streamType = $this->getStreamTypeFromUrl($url);

        if ($locale === 'ar') {
            return match($streamType) {
                'tech-conference' => 'مؤتمر التكنولوجيا الحي',
                'product-launch' => 'إطلاق المنتج الجديد',
                'workshop-session' => 'جلسة ورشة العمل',
                'qna-session' => 'جلسة الأسئلة والأجوبة',
                'training-course' => 'دورة التدريب المباشر',
                default => 'بث مباشر ' . ($index + 1)
            };
        }

        return match($streamType) {
            'tech-conference' => 'Live Tech Conference',
            'product-launch' => 'Product Launch Live',
            'workshop-session' => 'Workshop Session Live',
            'qna-session' => 'Q&A Session Live',
            'training-course' => 'Live Training Course',
            default => 'Live Stream ' . ($index + 1)
        };
    }

    protected function getStreamTypeFromUrl(string $url): string
    {
        $parts = explode('/', $url);
        $lastPart = end($parts);
        return $lastPart;
    }

    protected function insertStreamIfNotExists(string $title, string $url, int $hours, string $locale): void
    {
        $exists = DB::table('live_streams')
            ->where('title', $title)
            ->exists();

        if (!$exists) {
            DB::table('live_streams')->insert([
                'title' => $title,
                'url' => $url,
                'end_date' => now()->addHours($hours),
                'team_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
