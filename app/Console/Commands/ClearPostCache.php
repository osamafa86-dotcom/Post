<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPostCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-posts {post_id? : Specific post ID to clear cache for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear post-related caches for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $postId = $this->argument('post_id');

        if ($postId) {
            // Clear specific post cache
            $this->clearPostCache($postId);
            $this->info("Cache cleared for post ID: {$postId}");
        } else {
            // Clear all post-related caches
            $this->clearAllPostCaches();
            $this->info('All post-related caches cleared successfully!');
        }

        return 0;
    }

    /**
     * Clear cache for a specific post
     */
    private function clearPostCache($postId)
    {
        // Get the post to find its slug
        $post = \App\Models\Post::find($postId);
        
        if ($post) {
            Cache::forget("post_{$post->id}_{$post->slug}");
            Cache::forget("post_sidebar_{$post->id}");
            
            // Clear same category cache if category exists
            if ($post->category?->relationable_id) {
                Cache::forget("same_category_{$post->category->relationable_id}_{$post->id}");
            }
        }
    }

    /**
     * Clear all post-related caches
     */
    private function clearAllPostCaches()
    {
        // Clear post caches using pattern matching (if supported by cache driver)
        $posts = \App\Models\Post::select('id', 'slug')->get();
        
        foreach ($posts as $post) {
            Cache::forget("post_{$post->id}_{$post->slug}");
            Cache::forget("post_sidebar_{$post->id}");
        }

        // Clear category-related caches
        $categories = \App\Models\Category::pluck('id');
        foreach ($categories as $categoryId) {
            Cache::forget("same_category_{$categoryId}");
        }

        $this->info('Cleared ' . $posts->count() . ' post caches');
    }
}
