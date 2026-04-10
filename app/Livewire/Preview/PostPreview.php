<?php

namespace App\Livewire\Preview;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\CategoryTypeEnum;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\NewsLetterEmails;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PostPreview extends Component
{
    public $randomPost;
    public $sameCategoryPost, $randomCategory;
    public $lastPosts ,$mostReadPost;
    public array $state = [];
    public $type = 'news';
    public $side_advert , $bottom_advert;
    public $newsletterEmail;

    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $layout = 'components.layouts.main.' . config('app.launch') . '.main';
        return view('livewire.preview.'.config('app.launch').'_post-preview')->layout($layout);
    }

    public function mount(string $token): void
    {
        $data = Cache::get("post-preview:$token");
        if (!$data) {
            abort(404);
        }

        $this->state = $data;

        // Only fetch related posts if we have a saved post ID
        if ($this->state['post_id']) {
            $this->loadRelatedPosts();
        } else {
            $this->loadGenericPosts();
        }

        $this->loadAdvertisements();
        $this->loadRandomCategory();
    }

    protected function loadRelatedPosts(): void
    {
        $this->randomPost = Post::where('id', '!=', $this->state['post_id'])
            ->with(['files.file'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $this->sameCategoryPost = Post::where('id', '!=', $this->state['post_id'])
            ->whereHas('category', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('id', $this->state['category_id'][0] ?? null);
                    }
                );
            })->with(['files.file'])
            ->limit(3)
            ->get();

        $this->lastPosts = Post::with(['category.relationable', 'tags.relationable', 'files.file'])
            ->whereNot('id', $this->state['post_id'])
            ->latest()
            ->take(6)
            ->get();

        $this->mostReadPost = Post::with('category.relationable', 'tags.relationable', 'author.relationable', 'files.file')
            ->where('id', '!=', $this->state['post_id'])
            ->withSum('views as views_sum', 'views_number')
            ->orderByRaw('COALESCE(views_sum, 0) DESC')
            ->orderByDesc('publish_date')
            ->take(3)
            ->get();
    }

    protected function loadGenericPosts(): void
    {
        // Load generic posts when no specific post ID exists
        $this->randomPost = Post::with(['files.file'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $this->sameCategoryPost = Post::with(['files.file'])
            ->limit(3)
            ->get();

        $this->lastPosts = Post::with(['category.relationable', 'tags.relationable', 'files.file'])
            ->latest()
            ->take(6)
            ->get();

        $this->mostReadPost = Post::with('category.relationable', 'tags.relationable', 'author.relationable', 'files.file')
            ->withSum('views as views_sum', 'views_number')
            ->orderByRaw('COALESCE(views_sum, 0) DESC')
            ->orderByDesc('publish_date')
            ->take(3)
            ->get();
    }

    protected function loadAdvertisements(): void
    {
        $this->side_advert = Advertisement::query()->where('place' , AdvertisementPlaceEnum::SIDE_ADS->value)
            ->latest()->first();

        $this->bottom_advert = Advertisement::query()->where('place' , AdvertisementPlaceEnum::MAIN_ADS->value)
            ->latest()->first();
    }

    protected function loadRandomCategory(): void
    {
        $this->randomCategory = Category::with('files.file')->inRandomOrder()
            ->take(4)
            ->get();
    }

    #[Computed]
    public function tags()
    {
        // For unsaved posts, use the tags from state
        if (!$this->state['post_id']) {
            return collect($this->state['tags'] ?? []);
        }

        $post = Post::find($this->state['post_id']);
        return $post?->tags()->get()->pluck('relationable') ?? collect();
    }

    public function submitNewsletterEmail()
    {
        $this->validate(['newsletterEmail' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->newsletterEmail]);
        $this->reset('newsletterEmail');
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
    }

    #[Computed]
    public function lookAlikePosts()
    {
        // For unsaved posts, return empty or generic posts
        if (!$this->state['post_id']) {
            return Post::with(['files.file'])
                ->inRandomOrder()
                ->take(3)
                ->get();
        }

        $post = Post::find($this->state['post_id']);

        if (!$post) {
            return collect();
        }

        return Post::whereHas('categories', function ($q) use ($post) {
            $q->where('relationable_id', $post->category?->relationable?->id);
        })
            ->where('id', '!=', $post->id)
            ->with([
                'files.file',
                'author.relationable.files.file',
                'category.relationable'
            ])
            ->take(3)
            ->get();
    }
}
