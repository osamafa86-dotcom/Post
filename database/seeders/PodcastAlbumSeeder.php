<?php

namespace Database\Seeders;

use App\Models\PodcastAlbum;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PodcastAlbumSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $podcastTypes = ['technology', 'news', 'business', 'health', 'entertainment', 'education', 'sports', 'culture'];

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualPodcastAlbums($podcastTypes);
        } else {
            $this->createSingleLanguagePodcastAlbums($podcastTypes);
        }
    }

    protected function createMultilingualPodcastAlbums(array $podcastTypes): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            foreach ($podcastTypes as $index => $type) {
                $this->createPodcastAlbumForLocale($type, $index + 1, $locale);
            }
        }
    }

    protected function createSingleLanguagePodcastAlbums(array $podcastTypes): void
    {
        $locale = $this->getWebsiteLocale();

        foreach ($podcastTypes as $index => $type) {
            $this->createPodcastAlbumForLocale($type, $index + 1, $locale);
        }
    }

    protected function createPodcastAlbumForLocale(string $type, int $id, string $locale): void
    {
        $faker = $this->createFakerForLocale($locale);
        $config = $this->createPodcastConfig($type, $id);

        // إنشاء البيانات المترجمة يدوياً لكل لغة
        $title = $this->generateLocalizedPodcastTitle($type, $locale, $faker);
        $description = $this->generateLocalizedPodcastDescription($type, $locale, $faker);

        PodcastAlbum::create([
            'title' => $title,
            'description' => $description,
            'small_image' => $config['small_image'],
            'background_image' => $config['background_image'],
            'sound_cloud' => $config['sound_cloud'],
            'google_cast' => $config['google_cast'],
            'spotify' => $config['spotify'],
            'apple_cast' => $config['apple_cast'],
            'team_id' => null,
        ]);
    }

    protected function generateLocalizedPodcastTitle(string $type, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicTitles = [
                'technology' => 'بودكاست التكنولوجيا والابتكار',
                'news' => 'بودكاست الأخبار والمستجدات',
                'business' => 'بودكاست الأعمال والاستثمار',
                'health' => 'بودكاست الصحة والعافية',
                'entertainment' => 'بودكاست الترفيه والفنون',
                'education' => 'بودكاست التعليم والمعرفة',
                'sports' => 'بودكاست الرياضة والمنافسات',
                'culture' => 'بودكاست الثقافة والمجتمع',
                'science' => 'بودكاست العلوم والاكتشافات',
                'history' => 'بودكاست التاريخ والحضارات'
            ];

            return $arabicTitles[$type] ?? 'بودكاست ' . $type;
        }

        $englishTitles = [
            'technology' => 'Technology and Innovation Podcast',
            'news' => 'News and Updates Podcast',
            'business' => 'Business and Investment Podcast',
            'health' => 'Health and Wellness Podcast',
            'entertainment' => 'Entertainment and Arts Podcast',
            'education' => 'Education and Knowledge Podcast',
            'sports' => 'Sports and Competitions Podcast',
            'culture' => 'Culture and Society Podcast',
            'science' => 'Science and Discoveries Podcast',
            'history' => 'History and Civilizations Podcast'
        ];

        return $englishTitles[$type] ?? ucfirst($type) . ' Podcast';
    }

    protected function generateLocalizedPodcastDescription(string $type, string $locale, $faker): string
    {
        if ($locale === 'ar') {
            $arabicDescriptions = [
                'technology' => 'استكشف عالم التكنولوجيا والابتكار من خلال حلقات شيقة تناقش أحدث التطورات التقنية وتأثيرها على حياتنا اليومية.',
                'news' => 'تابع أهم الأخبار والمستجدات المحلية والعالمية مع تحليلات عميقة ونقاشات هامة حول القضايا الراهنة.',
                'business' => 'انغمس في عالم الأعمال والاستثمار مع نصائح الخبراء وتحليلات السوق واستراتيجيات النجاح في القطاعات المختلفة.',
                'health' => 'اهتم بصحتك وعافيتك مع نصائح طبية ورياضية ونفسية تساعدك على عيش حياة أكثر صحة وسعادة.',
                'entertainment' => 'استمتع بأجمل أوقات الترفيه مع مناقشات حول الأفلام، المسلسلات، الموسيقى، والفنون بمختلف أنواعها.',
                'education' => 'ارتقِ بمستوى معرفتك مع حلقات تعليمية شيقة تغطي مختلف المجالات العلمية والأكاديمية والمهنية.',
                'sports' => 'عش شغف الرياضة مع تغطية لأهم البطولات والمنافسات وتحليلات للأداء والاستراتيجيات الرياضية.',
                'culture' => 'اكتشف ثراء الثقافة والمجتمع مع حوارات حول العادات، التقاليد، الأدب، والفنون المختلفة حول العالم.',
                'science' => 'انطلق في رحلة الاكتشاف مع أحدث الأبحاث العلمية والتقنيات الحديثة التي تشكل مستقبل البشرية.',
                'history' => 'اغوص في أعماق التاريخ مع سرد لقصص الحضارات والشخصيات التي شكلت عالمنا اليوم.'
            ];

            return $arabicDescriptions[$type] ?? 'بودكاست مميز يقدم محتوى قيماً وشيقاً في مجال ' . $type;
        }

        $englishDescriptions = [
            'technology' => 'Explore the world of technology and innovation through engaging episodes discussing the latest tech developments and their impact on our daily lives.',
            'news' => 'Follow the most important local and global news with in-depth analysis and important discussions about current issues.',
            'business' => 'Immerse yourself in the world of business and investment with expert advice, market analysis, and success strategies in various sectors.',
            'health' => 'Take care of your health and wellness with medical, sports, and psychological tips that help you live a healthier and happier life.',
            'entertainment' => 'Enjoy the best entertainment times with discussions about movies, series, music, and various arts.',
            'education' => 'Elevate your knowledge level with engaging educational episodes covering various scientific, academic, and professional fields.',
            'sports' => 'Live the passion of sports with coverage of major tournaments and competitions and analysis of performance and sports strategies.',
            'culture' => 'Discover the richness of culture and society with dialogues about customs, traditions, literature, and various arts around the world.',
            'science' => 'Embark on a journey of discovery with the latest scientific research and modern technologies that shape the future of humanity.',
            'history' => 'Dive into the depths of history with narratives of civilizations and personalities that shaped our world today.'
        ];

        return $englishDescriptions[$type] ?? 'A distinguished podcast that provides valuable and interesting content in the field of ' . $type;
    }

    protected function createPodcastConfig(string $type, int $id): array
    {
        return [
            'small_image' => "images/podcasts/{$type}_sm.jpg",
            'background_image' => "images/podcasts/{$type}_bg.jpg",
            'sound_cloud' => "https://soundcloud.com/example/{$type}",
            'google_cast' => "https://podcasts.google.com/feed/{$type}",
            'spotify' => "https://open.spotify.com/show/{$type}",
            'apple_cast' => "https://podcasts.apple.com/us/podcast/{$type}/id0000000{$id}",
        ];
    }
}
