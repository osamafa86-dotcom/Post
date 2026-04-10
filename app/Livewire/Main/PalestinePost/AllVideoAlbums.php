<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Models\Category;
use App\Models\MaterialAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class  AllVideoAlbums extends Component
{
    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.all-video-albums');
    }
    #[Computed]
    public function albums(): Collection|array
    {
        return MaterialAlbum::query()
            ->where('type', MaterialTypeEnum::VIDEO->value)
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

            ->with('category.relationable')
            ->get();
    }
    #[Computed]
    public function categories(): Collection|array
    {

        return Category::query()
            ->whereIn('category_type', [CategoryTypeEnum::PODCAST->value,CategoryTypeEnum::VIDEO->value ,CategoryTypeEnum::IMAGE->value ])
            ->whereHas('material_album_relation')
            ->with('material_album_relation')
            ->withCount('material_album_relation')
            ->orderBy('material_album_relation_count', 'desc')
            ->get();
    }
}
