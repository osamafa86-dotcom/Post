<?php

namespace App\Livewire\Main\Hongora;

use App\Models\Category;
use App\Models\Material;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class SinglePodcast extends Component
{
    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $title;
    public $item;
    public $randomPost;
    public $sameCategoryItems;
    public $previousPost;
    public $nextPost;
    public int $rating = 0;

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.single-podcast');
    }

    public function play(){
        $this->dispatch('playAudio', podcastId: $this->item->id);

    }
    public function setRating($value)
    {
        $this->rating = $value;

//        Material::
    }

    public function mount(): void
    {

        $this->id = request()->query('id');
        $this->title = request()->query('slug');
        $this->item = Material::with(['category.relationable', 'tags.relationable', 'presenters.relationable.participant_social_media'])->withTrashed()->where(function ($q) {
            $q->where('id', $this->id);
        })->first();
//        $this->randomPost = Post::where('id', '!=', $id)->inRandomOrder()->get();
//
        $this->sameCategoryItems = Material::where('id', '!=', $this->id)
            ->WhereHas('category', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->where('id', $this->item?->category?->relationable?->id);
                    }
                );
            })->latest()->take(6)->get();

        if (!Session::has('viewed_material_' . $this->item->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $this->item->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $this->item->views()->create([
                    'last_viewed_at' => $now,
                    'views_number'   => 1,
                ]);
            }
            Session::put('viewed_material_' . $this->item->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int) config('app.view_expiration_hours'))
            ]);
        }

    }
    #[Computed]
    public function tags()
    {
        return $this->Post?->tags()->get()->pluck('relationable');

    }
}
