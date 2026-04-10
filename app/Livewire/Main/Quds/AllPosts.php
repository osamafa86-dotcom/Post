<?php

namespace App\Livewire\Main\Quds;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
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
use Livewire\WithPagination;

class AllPosts extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';
    public $type;
    public $id;
    public $category_id;
    public array $search_query = [];
    public string $title = '';
    public $currentAuthor;
    public $currentCategory;

    #[Layout('components.layouts.main.quds.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.quds.all-posts');
    }


    public function mount(?string $type = null, ?int $id = null): void
    {
        $this->type = $type;
        $this->id = $id;

        $this->search_query['category_id'] = 'all';
        $this->search_query['author_id'] = 'all';
        if ($this->id) {
            if ($this->type === 'category') {
                $this->search_query['category_id'] = $this->id;
                $this->currentCategory = Category::find($this->id); // Set current category
                $this->title = $this->currentCategory?->category_title ?? 'Category';


            } elseif ($this->type === 'author') {
                $this->search_query['author_id'] = $this->id;
                $this->currentAuthor = Participant::find($this->id);
            }
        }

    }

    #[Computed]
    public function posts()
    {
        return Post::latest('publish_date')
            ->when(!empty($this->search_query['search_text']), function ($query) {
                $query->where('title', 'like', '%' . $this->search_query['search_text'] . '%');
            })
            ->when(!empty($this->search_query['category_id']) && $this->search_query['category_id'] != 'all', function ($query) {
                $query->WhereHas('category', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->search_query['category_id']);
                        }
                    )->where('relationable_is_main',1);
                });
            })
            ->when(!empty($this->search_query['author_id']) && $this->search_query['author_id'] != 'all', function ($query) {
                $query->WhereHas('authors', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Participant::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->search_query['author_id']);
                        }
                    );
                });

            })
            ->when(!empty($this->search_query['date']) && !empty($this->search_query['to_date']), function ($query) {
                $query->whereBetween('publish_date', [$this->search_query['date'] . ' 00:00:00', $this->search_query['to_date'] . ' 23:59:59']);
            })
            ->when(!empty($this->id ) && $this->type == 'tag' , function ($query)  {
                $query->WhereHas('tag', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('id',  $this->id);
                        }
                    );
                });

            })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->paginate(9);

    }


    #[Computed]
    public function authors(): Collection|array
    {
        return Participant::query()
            ->where('type', ParticipantTypeEnum::AUTHORS->value)
            ->get();
    }

    #[Computed]
    public function categories(): Collection
    {
            return Category::query()
                ->select('id', 'category_title')
                ->where('category_type', CategoryTypeEnum::NEWS->value)
                ->has('post_relation')
                ->get();

    }

    public function updatedSearchQuery()
    {
        $this->resetPage();
        unset($this->posts);
    }
}
