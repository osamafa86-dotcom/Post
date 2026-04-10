<?php

namespace App\Http\Controllers;

use App\Enums\PublishEnum;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class RSSFeedController extends Controller
{
    public function index(): Response
    {
        $posts = Cache::remember('rss_feed_posts', 3600, function () {
            return Post::query()
                ->where('publish_status', PublishEnum::PUBLISHED->value)
                ->with(['category.relationable', 'author.relationable', 'files.file'])
                ->orderBy('publish_date', 'desc')
                ->take(50)
                ->get();
        });

        return response()->view('rss_feed', [
            'posts' => $posts
        ])->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
