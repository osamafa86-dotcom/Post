<?php

namespace Database\Seeders;

use App\Models\VideoAlbum;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class VideoAlbumSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualVideoAlbums();
        } else {
            $this->createSingleLanguageVideoAlbums();
        }
    }

    protected function createMultilingualVideoAlbums(): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createVideoAlbumsForLocale($locale);
        }
    }

    protected function createSingleLanguageVideoAlbums(): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createVideoAlbumsForLocale($locale);
    }

    protected function createVideoAlbumsForLocale(string $locale): void
    {
        App::setLocale($locale);

        $videoAlbums = $this->getLocalizedVideoAlbums($locale);

        foreach ($videoAlbums as $album) {
            VideoAlbum::firstOrCreate(
                [
                    'title' => $album['title'],
                ],
                [
                    'small_image' => $album['small_image'],
                    'background_image' => $album['background_image'],
                    'description' => $album['description'],
                    'team_id' => null,
                ]
            );
        }
    }

    protected function getLocalizedVideoAlbums(string $locale): array
    {
        if ($locale === 'ar') {
            return [
                [
                    'title' => 'مقاطع الفيديو التعليمية',
                    'small_image' => 'images/video-albums/educational_sm.jpg',
                    'background_image' => 'images/video-albums/educational_bg.jpg',
                    'description' => 'مجموعة من الفيديوهات التعليمية التي تقدم شروحات مفصلة ودروس عملية في مختلف المجالات.'
                ],
                [
                    'title' => 'فيديوهات الترفيه',
                    'small_image' => 'images/video-albums/entertainment_sm.jpg',
                    'background_image' => 'images/video-albums/entertainment_bg.jpg',
                    'description' => 'استمتع بمجموعة متنوعة من الفيديوهات الترفيهية التي تضم محتوى ممتعاً ومنوعاً للجميع.'
                ],
                [
                    'title' => 'الأخبار المصورة',
                    'small_image' => 'images/video-albums/news_sm.jpg',
                    'background_image' => 'images/video-albums/news_bg.jpg',
                    'description' => 'تغطية مرئية شاملة لأهم الأحداث والأخبار المحلية والعالمية بطريقة جذابة ومباشرة.'
                ],
                [
                    'title' => 'برامج الحوار والنقاش',
                    'small_image' => 'images/video-albums/talks_sm.jpg',
                    'background_image' => 'images/video-albums/talks_bg.jpg',
                    'description' => 'حلقات حوارية ونقاشات عميقة مع خبراء ومتخصصين في مختلف المجالات والتخصصات.'
                ],
                [
                    'title' => 'فيديوهات الرياضة',
                    'small_image' => 'images/video-albums/sports_sm.jpg',
                    'background_image' => 'images/video-albums/sports_bg.jpg',
                    'description' => 'أبرز اللحظات الرياضية وتحليلات المباريات وتغطية شاملة للبطولات المحلية والعالمية.'
                ],
                [
                    'title' => 'مقاطع تكنولوجيا',
                    'small_image' => 'images/video-albums/technology_sm.jpg',
                    'background_image' => 'images/video-albums/technology_bg.jpg',
                    'description' => 'أحدث التطورات التقنية ومراجعات الأجهزة وشروحات التطبيقات في عالم التكنولوجيا.'
                ],
                [
                    'title' => 'فيديوهات الطهي',
                    'small_image' => 'images/video-albums/cooking_sm.jpg',
                    'background_image' => 'images/video-albums/cooking_bg.jpg',
                    'description' => 'وصفات طبخ شهية ودروس طهي ممتعة تقدم بأسلوب سهل وبسيط للمبتدئين والمحترفين.'
                ],
                [
                    'title' => 'رحلات واستكشاف',
                    'small_image' => 'images/video-albums/travel_sm.jpg',
                    'background_image' => 'images/video-albums/travel_bg.jpg',
                    'description' => 'جولات افتراضية في أجمل الوجهات السياحية واستكشاف ثقافات وعادات الشعوب حول العالم.'
                ]
            ];
        }

        // English video albums (fallback)
        return [
            [
                'title' => 'Educational Videos',
                'small_image' => 'images/video-albums/educational_sm.jpg',
                'background_image' => 'images/video-albums/educational_bg.jpg',
                'description' => 'A collection of educational videos providing detailed explanations and practical lessons in various fields.'
            ],
            [
                'title' => 'Entertainment Videos',
                'small_image' => 'images/video-albums/entertainment_sm.jpg',
                'background_image' => 'images/video-albums/entertainment_bg.jpg',
                'description' => 'Enjoy a variety of entertaining videos featuring fun and diverse content for everyone.'
            ],
            [
                'title' => 'News Videos',
                'small_image' => 'images/video-albums/news_sm.jpg',
                'background_image' => 'images/video-albums/news_bg.jpg',
                'description' => 'Comprehensive visual coverage of the most important local and global events and news in an engaging and direct way.'
            ],
            [
                'title' => 'Talk Shows & Discussions',
                'small_image' => 'images/video-albums/talks_sm.jpg',
                'background_image' => 'images/video-albums/talks_bg.jpg',
                'description' => 'Dialogue episodes and in-depth discussions with experts and specialists in various fields and disciplines.'
            ],
            [
                'title' => 'Sports Videos',
                'small_image' => 'images/video-albums/sports_sm.jpg',
                'background_image' => 'images/video-albums/sports_bg.jpg',
                'description' => 'Top sports moments, match analyses, and comprehensive coverage of local and international tournaments.'
            ],
            [
                'title' => 'Technology Videos',
                'small_image' => 'images/video-albums/technology_sm.jpg',
                'background_image' => 'images/video-albums/technology_bg.jpg',
                'description' => 'The latest technological developments, device reviews, and application explanations in the world of technology.'
            ],
            [
                'title' => 'Cooking Videos',
                'small_image' => 'images/video-albums/cooking_sm.jpg',
                'background_image' => 'images/video-albums/cooking_bg.jpg',
                'description' => 'Delicious cooking recipes and fun cooking lessons presented in an easy and simple way for beginners and professionals.'
            ],
            [
                'title' => 'Travel & Exploration',
                'small_image' => 'images/video-albums/travel_sm.jpg',
                'background_image' => 'images/video-albums/travel_bg.jpg',
                'description' => 'Virtual tours to the most beautiful tourist destinations and exploration of cultures and customs of peoples around the world.'
            ]
        ];
    }
}
