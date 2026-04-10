<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\Participant;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SingleVideo extends Component
{

    public $category_ids = [];
    public $album_ids = [];
    public $sort;

    public $category_id;


    public $isPlaying = false;
    public $video_id;

    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.single-video');
    }
    public function mount($category_id=null)
    {
        if ($category_id){
            $this->category_id=$category_id;
            $this->category_ids[]=$category_id;
        }

    }

    #[Computed]
    public function videos(): Collection|array
    {
        return Material::query()
            ->whereHas('category', function ($q2) {
                $q2->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) {
                        $subQuery->whereIn('id', $this->category_ids);
                    }
                );
            })->where('type', MaterialTypeEnum::VIDEO->value)
            ->with(['category.relationable','files.file'])
            ->get();
    }


    #[Computed]
    public function categories(): Collection|array
    {
        return Category::query()
            ->where('category_type', CategoryTypeEnum::VIDEO->value)
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
            ->where('type', MaterialTypeEnum::VIDEO->value)
            ->whereHas('album_materials')
            ->with('album_materials')
            ->withCount('album_materials')
            ->orderBy('album_materials_count', 'desc')
            ->get();
    }


    function playVideo($video_id = null): void
    {

        $this->isPlaying = true;
        $this->video_id = $video_id;
    }
}
