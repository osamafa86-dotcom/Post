<?php

namespace App\Http\Controllers;

use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * SitemapController
 * ----------------------------------------------------------------
 * يولّد خرائط الموقع (XML) المُشار إليها في public/robots.txt:
 *   /sitemap.xml          فهرس يجمع كل الخرائط الفرعية (sitemapindex)
 *   /sitemap-pages.xml    الصفحات الثابتة العامة
 *   /sitemap-posts-{n}.xml المقالات المنشورة (مقسّمة على شرائح)
 *   /sitemap-categories.xml صفحات التصنيفات
 *   /sitemap-tags.xml      صفحات الوسوم
 *   /sitemap-news.xml      خريطة Google News (آخر 48 ساعة)
 *
 * كل المسارات مساراتٌ نظيفة (بلا query string) حتى لا يحجبها
 * robots.txt (القاعدة: Disallow: /*?*).
 */
class SitemapController extends Controller
{
    /** عدد المقالات في كل شريحة خريطة (الحد الأقصى لمعيار sitemap هو 50000). */
    private const POSTS_PER_CHUNK = 5000;

    /** حدود Google News: آخر يومين وبحد أقصى 1000 رابط. */
    private const NEWS_WINDOW_HOURS = 48;
    private const NEWS_MAX = 1000;

    /** فهرس الخرائط: /sitemap.xml */
    public function index(): Response
    {
        $sitemaps = Cache::remember('sitemap_index', 3600, function () {
            $now = now()->toAtomString();

            $items = [
                ['loc' => route('sitemap.pages'),      'lastmod' => $now],
                ['loc' => route('sitemap.news'),       'lastmod' => $now],
                ['loc' => route('sitemap.categories'), 'lastmod' => $now],
                ['loc' => route('sitemap.tags'),       'lastmod' => $now],
            ];

            $chunks = max(1, (int) ceil($this->publishedPosts()->count() / self::POSTS_PER_CHUNK));
            for ($page = 1; $page <= $chunks; $page++) {
                $items[] = ['loc' => route('sitemap.posts', ['page' => $page]), 'lastmod' => $now];
            }

            return $items;
        });

        return $this->xml('sitemap.index', ['sitemaps' => $sitemaps]);
    }

    /** الصفحات الثابتة العامة: /sitemap-pages.xml */
    public function pages(): Response
    {
        $urls = Cache::remember('sitemap_pages', 3600, function () {
            $launch = config('app.launch');
            $pages = [
                ["main.$launch.index",                '1.0', 'hourly'],
                ["main.$launch.all_posts",            '0.8', 'hourly'],
                ["main.$launch.all_video_albums",     '0.6', 'daily'],
                ["main.$launch.all_podcast_albums",   '0.6', 'daily'],
                ["main.$launch.about_us",             '0.4', 'monthly'],
                ["main.$launch.send_news",            '0.3', 'monthly'],
                ["main.$launch.sitemap",              '0.3', 'weekly'],
            ];

            $urls = [];
            foreach ($pages as [$name, $priority, $changefreq]) {
                if (Route::has($name)) {
                    $urls[] = [
                        'loc'        => route($name),
                        'priority'   => $priority,
                        'changefreq' => $changefreq,
                    ];
                }
            }

            return $urls;
        });

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    /** شريحة من المقالات المنشورة: /sitemap-posts-{page}.xml */
    public function posts(int $page): Response
    {
        $urls = Cache::remember("sitemap_posts_{$page}", 1800, function () use ($page) {
            return $this->publishedPosts()
                ->select('id', 'slug', 'title', 'publish_date', 'updated_at')
                ->with(['files.file'])
                ->orderByDesc('publish_date')
                ->forPage($page, self::POSTS_PER_CHUNK)
                ->get()
                ->map(function (Post $post) {
                    $url = [
                        'loc'        => $this->postUrl($post),
                        'lastmod'    => ($post->updated_at ?? $post->publish_date)?->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority'   => '0.7',
                    ];

                    if ($path = $post->files->first()?->file?->path) {
                        $url['image'] = ['loc' => file_url($path), 'title' => $post->title];
                    }

                    return $url;
                })
                ->all();
        });

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    /** صفحات التصنيفات: /sitemap-categories.xml */
    public function categories(): Response
    {
        $urls = Cache::remember('sitemap_categories', 3600, function () {
            $launch = config('app.launch');

            return Category::query()
                ->whereHas('post_relation')
                ->get()
                ->filter(fn ($c) => filled($c->category_title))
                ->map(fn ($c) => [
                    'loc'        => route("main.$launch.category_news", ['category_title' => $c->category_title]),
                    'changefreq' => 'daily',
                    'priority'   => '0.6',
                ])
                ->values()
                ->all();
        });

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    /** صفحات الوسوم: /sitemap-tags.xml */
    public function tags(): Response
    {
        $urls = Cache::remember('sitemap_tags', 3600, function () {
            $launch = config('app.launch');

            return Tag::query()
                ->whereHas('post_relation.post')
                ->get()
                ->filter(fn ($t) => filled($t->tag_name))
                ->map(fn ($t) => [
                    'loc'        => route("main.$launch.tag_news", ['tag_name' => $t->tag_name]),
                    'changefreq' => 'weekly',
                    'priority'   => '0.5',
                ])
                ->values()
                ->all();
        });

        return $this->xml('sitemap.urlset', ['urls' => $urls]);
    }

    /** خريطة Google News (آخر 48 ساعة): /sitemap-news.xml */
    public function news(): Response
    {
        $posts = Cache::remember('sitemap_news', 600, function () {
            return $this->publishedPosts()
                ->select('id', 'slug', 'title', 'lang', 'publish_date', 'created_at')
                ->where('publish_date', '>=', Carbon::now()->subHours(self::NEWS_WINDOW_HOURS))
                ->orderByDesc('publish_date')
                ->limit(self::NEWS_MAX)
                ->get();
        });

        return $this->xml('sitemap.news', ['posts' => $posts]);
    }

    /** استعلام المقالات المنشورة (نقطة مركزية واحدة). */
    private function publishedPosts(): Builder
    {
        return Post::query()->where('publish_status', PublishEnum::PUBLISHED->value);
    }

    /** رابط المقال الحقيقي: /post?id={id}&slug={slug} */
    private function postUrl(Post $post): string
    {
        return route('main.' . config('app.launch') . '.show_post', [
            'id'   => $post->id,
            'slug' => $post->slug,
        ]);
    }

    /** استجابة XML بترويسة المحتوى الصحيحة. */
    private function xml(string $view, array $data): Response
    {
        return response()
            ->view($view, $data)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
