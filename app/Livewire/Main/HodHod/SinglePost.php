<?php

namespace App\Livewire\Main\HodHod;

use App\Livewire\Main\Ufok\NewsLetterEmail;
use App\Models\Category;
use App\Models\NewsLetterEmails;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class SinglePost extends Component
{
    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $slug;
    public $Post;
    public $randomPost,$mostReadPost,$randomCategory;
    public object $sameCategoryPost;
    public $newsletterEmail;

    public function submitNewsletterEmail(){
        $this->validate(['newsletterEmail' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->newsletterEmail]);
        $this->reset('newsletterEmail');
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
    }
    
    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');
        
        // Cache main post for 1 hour with relations
        $cacheKey = "post_{$this->id}_{$this->slug}";
        $this->Post = Cache::remember($cacheKey, 3600, function () {
            return Post::with([
                'category.relationable:id,category_title',
                'tags.relationable:id,tag_name',
                'author.relationable:id,name',
                'files.file:id,path'
            ])
            ->withTrashed()
            ->where([['id', $this->id], ['slug', $this->slug]])
            ->first();
        });
        
        if (!$this->Post) {
            abort(404);
        }
        
        $categoryId = $this->Post->category?->relationable_id;
        
        // Cache sidebar data for 30 minutes
        $sidebarCacheKey = "post_sidebar_{$this->Post->id}";
        $sidebarData = Cache::remember($sidebarCacheKey, 1800, function () {
            return [
                'randomPost' => $this->getRandomPosts(),
                'mostReadPost' => $this->getMostReadPosts(),
                'randomCategory' => $this->getRandomCategories(),
            ];
        });
        
        $this->randomPost = $sidebarData['randomPost'];
        $this->mostReadPost = $sidebarData['mostReadPost'];
        $this->randomCategory = $sidebarData['randomCategory'];
        
        // Cache same category posts for 30 minutes
        $sameCategoryCacheKey = "same_category_{$categoryId}_{$this->Post->id}";
        $this->sameCategoryPost = Cache::remember($sameCategoryCacheKey, 1800, function () use ($categoryId) {
            return $this->getSameCategoryPosts($categoryId);
        });
    }
    
    /**
     * Get random posts efficiently using RAND() with limit
     */
    private function getRandomPosts()
    {
        // More efficient than inRandomOrder() on large tables
        return Post::with([
            'category.relationable:id,category_title',
            'author.relationable:id,name',
            'files.file:id,path'
        ])
        ->where('id', '!=', $this->Post->id)
        ->where('publish_status', 1)
        ->orderByRaw('RAND()')
        ->limit(3)
        ->get();
    }
    
    /**
     * Get most read posts with optimized query
     */
    private function getMostReadPosts()
    {
        return Post::with([
            'category.relationable:id,category_title',
            'author.relationable:id,name',
            'files.file:id,path'
        ])
        ->where('posts.id', '!=', $this->Post->id)
        ->where('publish_status', 1)
        ->leftJoin('views', function($join) {
            $join->on('posts.id', '=', 'views.viewable_id')
                 ->where('views.viewable_type', '=', 'App\\Models\\Post');
        })
        ->select('posts.*', DB::raw('COALESCE(SUM(views.views_number), 0) as total_views'))
        ->groupBy('posts.id')
        ->orderByDesc('total_views')
        ->orderByDesc('posts.publish_date')
        ->limit(3)
        ->get();
    }
    
    /**
     * Get random categories efficiently
     */
    private function getRandomCategories()
    {
        return Category::with('files.file:id,path')
            ->orderByRaw('RAND()')
            ->limit(4)
            ->get();
        if (!Session::has('viewed_post_' . $this->Post->id)) {
            $now = \Carbon\Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->Post->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $this->Post->views()->create([
                    'last_viewed_at' => $now,
                    'views_number'   => 1,
                ]);
            }
            Session::put('viewed_post_' . $this->Post->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int) config('app.view_expiration_hours'))
            ]);
        }
    }
    
    /**
     * Get posts from same category with optimized join
     */
    private function getSameCategoryPosts($categoryId)
    {
        if (!$categoryId) {
            return collect();
        }
        
        return Post::with([
            'category.relationable:id,category_title',
            'author.relationable:id,name',
            'files.file:id,path'
        ])
        ->where('posts.id', '!=', $this->Post->id)
        ->where('publish_status', 1)
        ->join('post_relations as pr', function($join) use ($categoryId) {
            $join->on('posts.id', '=', 'pr.post_id')
                 ->where('pr.relationable_type', '=', 'App\\Models\\Category')
                 ->where('pr.relationable_id', '=', $categoryId);
        })
        ->select('posts.*')
        ->orderByDesc('posts.publish_date')
        ->limit(4)
        ->get();
    }
    #[Layout('components.layouts.main.hodhod.main')]
    public function render()
    {
        return view('livewire.main.hod-hod.single-post');
    }
}
