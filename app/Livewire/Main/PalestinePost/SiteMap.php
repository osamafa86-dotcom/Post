<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Author;
use App\Models\Category;
use App\Models\Material;
use App\Models\Participant;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SiteMap extends Component
{

    public $search_text;

    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.site-map');
    }

    #[Computed]
    public function categories(): Collection|array
    {
        return Category::query()->
       //     ->where('category_type', CategoryTypeEnum::NEWS->value)
           when(!empty($this->search_text), function ($query) {
               $query->where('category_title', 'like', '%' . $this->search_text . '%');
           })
            ->whereHas('post_relation')
            ->with('post_relation')
            ->withCount('post_relation')
            ->orderBy('post_relation_count', 'desc')
            ->get();
    }

    #[Computed]
    public function tags(): Collection|array
    {
        return Tag::query()
            ->where(function ($query) {
                $query->whereHas('post_relation.post');
            })
            ->when(!empty($this->search_text), function ($query) {
                $query->where('tag_name', 'like', '%' . $this->search_text . '%');
            })
            ->get();

    }

    #[Computed]
    public function authors(): Collection|array
    {

        return Participant::query()->
             when(!empty($this->search_text), function ($query) {
                 $query->where('name', 'like', '%' . $this->search_text . '%');
             })
            ->where('type', ParticipantTypeEnum::AUTHORS->value)

            ->get();
    }
}
