<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Session;


class SinglePodcast extends Component
{

    public $category_ids = [];
    public $album_ids =[];
    public $sort;

    public $podcast_album_id;
    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.single-podcast');
    }

    #[Computed]
    public function podcasts(): Collection|array
    {
        $material = Material::query()
            ->where('album_id' , $this->podcast_album_id)
            ->where('type', MaterialTypeEnum::PODCAST->value)
            ->when(!empty($this->category_ids), function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->whereIn('id', $this->category_ids);
                        }
                    );
                });
            })
//            ->when(!empty($this->album_ids), function ($query) {
//                $query->whereHas('material_album', function ($query) {
//                    $query->whereIn('album_id', $this->album_ids);
//                });
//            })
            ->with(['category.relationable','files.file'])
            ->get();

        if (!Session::has('viewed_podcast_' . $material->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $material->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $material->views()->create([
                    'last_viewed_at' => $now,
                    'views_number'   => 1,
                ]);
            }
            Session::put('viewed_podcast_' . $material->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int) config('app.view_expiration_hours'))
            ]);
        }
        return $material;
    }


    #[Computed]
    public function categories(): Collection|array
    {

        return Category::query()
            ->where('category_type', CategoryTypeEnum::PODCAST->value  )
            ->whereHas('material_relation')
            ->with('material_relation')
            ->withCount('material_relation')
            ->orderBy('material_relation_count', 'desc')
            ->get();
    }

    #[Computed]
    public function albums(): Collection|array
    {

        return MaterialAlbum::query()
            ->whereHas('album_materials')
            ->with('album_materials')
            ->withCount('album_materials')
            ->orderBy('album_materials_count', 'desc')
            ->get();
    }

}
