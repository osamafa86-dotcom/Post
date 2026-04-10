<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class SinglePost extends Component
{
    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $slug;
    public $Post;
    public $randomPost;
    public $sameCategoryPost;
    public $last3Posts;
    public $categorys;
    public $tags;
    public $query ='';
    public $postsSearch = [];
    public $morePics=[];


    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $this->categorys=Category::whereHas('post_relation')->withCount('post_relation')->where('category_type','أخبار')->get();
        $this->postsSearch = Post::where('title', 'like', '%' . $this->query . '%')->take(4)->get();
        $this->tags=Tag::whereHas('post_relation')->withCount('post_relation')->orderBy('post_relation_count','asc')->get();
        $this->morePics=$this->Post?->files?->where('model_column','morePictures');
        return view('livewire.main.adani-cast.single-article');

    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');
        tap($this->post, function ($post) {
            $view = $this->post->views()
                ->whereDate('last_viewed_at', today())
                ->first();

            if (!$view) {
                $this->post->views()->create([
                    'last_viewed_at' => now(),
                    'views_number' => 1,
                ]);
            } else {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => now()]);
            }
        });
        $this->Post = Post::with('category', 'tags','author','files')->where(function ($q) {
            $q->where('slug', $this->slug)
                ->where('id', $this->id);
        })->first();
        $this->randomPost = Post::where('id', '!=', $this->id)->inRandomOrder()->first();
        $this->sameCategoryPost = Post::where('id', '!=', $this->id)
            ->orWhereHas('category', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('id',$this->Post?->category_id);
                    }
                );
            })->get();

        $this->last3Posts = Post::latest()->with('files')->where('id', '!=', $this->id)->
        orWhereHas('category', function ($query) {
            $query->whereHasMorph(
                'relationable',
                [Category::class],
                function ($subQuery) {
                    $subQuery->where('id',$this->Post?->category_id);
                }
            );
        })->take(3)->get();


    }

}
