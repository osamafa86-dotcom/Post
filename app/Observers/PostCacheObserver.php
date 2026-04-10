<?php

namespace App\Observers;

use App\Jobs\RenderStaticHomepage;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostCacheObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->clearRelatedCaches($post);
        $this->dispatchStaticRender('post_created');
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->clearPostCache($post);
        $this->clearRelatedCaches($post);
        $this->dispatchStaticRender('post_updated');
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->clearPostCache($post);
        $this->clearRelatedCaches($post);
        $this->dispatchStaticRender('post_deleted');
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        $this->clearPostCache($post);
        $this->clearRelatedCaches($post);
        $this->dispatchStaticRender('post_restored');
    }

    /**
     * Invalidate static cache and optionally dispatch queue job.
     * The static cache self-heals on next visitor request even without a queue worker.
     * The queue job is an optional accelerator — if it fails, nothing breaks.
     */
    private function dispatchStaticRender(string $reason): void
    {
        // Purge cached files so next anonymous visitor triggers lazy regeneration
        try {
            app(\App\Services\StaticPageRenderer::class)->purge();
        } catch (\Throwable $e) {
            // Non-critical — stale cache will be detected by age check
        }

        // Optional: dispatch queue job for faster regeneration (if worker is running)
        try {
            RenderStaticHomepage::dispatch($reason)->afterCommit();
        } catch (\Throwable $e) {
            // No queue worker? No problem — middleware handles lazy regen
        }
    }

    /**
     * Clear cache for specific post
     */
    private function clearPostCache(Post $post): void
    {
        Cache::forget("post_{$post->id}_{$post->slug}");
        Cache::forget("post_sidebar_{$post->id}");
        
        // Clear old slug cache if slug was changed
        if ($post->isDirty('slug') && $post->getOriginal('slug')) {
            Cache::forget("post_{$post->id}_{$post->getOriginal('slug')}");
        }
    }

    /**
     * Clear related caches (sidebar, category posts, etc.)
     */
    private function clearRelatedCaches(Post $post): void
    {
        // Clear same category cache
        if ($post->category?->relationable_id) {
            $categoryId = $post->category->relationable_id;
            // Clear for all posts in this category (simplified - clear pattern)
            Cache::forget("same_category_{$categoryId}_{$post->id}");
            
            // If we need to clear all posts in category, we'd need to iterate
            // For now, let's just clear this post's related caches
        }
        
        // Clear sidebar caches for all posts (since random/most read changed)
        // This is a trade-off - we clear all sidebar caches when any post changes
        // Alternatively, you could use cache tags if your driver supports it
        $this->clearSidebarCaches();
    }

    /**
     * Clear sidebar caches (random posts, most read, categories)
     * Note: This is simplified - in production you might want to use cache tags
     */
    private function clearSidebarCaches(): void
    {
        // If using Redis/Memcached with tags:
        // Cache::tags(['sidebar'])->flush();
        
        // Otherwise, we need to clear specific keys
        // For now, we'll clear the most recent posts' sidebar caches
        // A better approach would be to use cache tags
        $recentPosts = Post::latest()->take(100)->pluck('id');
        foreach ($recentPosts as $postId) {
            Cache::forget("post_sidebar_{$postId}");
        }
    }
}
