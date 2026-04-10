<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class TagNews extends Component
{
    use withPagination;
    protected string $paginationTheme = 'bootstrap';
    public array $search_query = [];
    public $tag_name ;
    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.tag-news');
    }
    #[Computed]
    public function posts()
    {
        return   Post::when(!empty($this->search_query['search_text']), function ($query) {
            $query->where('title', 'like', '%' . $this->search_query['search_text'] . '%');
        })
            ->when(!empty($this->search_query['date']) && !empty($this->search_query['to_date']), function ($query) {
                $query->whereBetween('publish_date', [$this->search_query['date'] . ' 00:00:00', $this->search_query['to_date'] . ' 23:59:59']);
            })
            ->when(!empty($this->tag_name) , function ($query) {
                $query->whereHas('tag', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('tag_name', $this->tag_name);
                        }
                    );
                });
            })

            ->whereHas('tag', function ($query) {
                $query->whereHasMorph(
                    'relationable',
                    [Tag::class],
                    function ($subQuery) {
                        $subQuery->where('tag_type', TagTypeEnum::NEWS->value);
                    }
                );
            })
            ->with(['author.relationable','files.file'])->latest()->paginate(6);
    }

    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }
}
