<?php

namespace App\Livewire\Main\Maktoob;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Participant;
use App\Models\PodcastAlbum;
use App\Models\Post;
use App\Models\Tag;
use App\Models\VideoAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use PhpParser\Node\Expr\Array_;

class SearchPage extends Component
{

    use withPagination;

    protected string $paginationTheme = 'bootstrap';

    public $tag_id;
    #[Url(keep: true)]
    public $category = null;
    #[Url(keep: true)]
    public $date_from = null;
    #[Url(keep: true)]
    public $date_to = null;
    #[Url(keep: true)]
    public $search = '';

    public array $search_query = [];

    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.search-page');
    }

    public function mount($tag_id = null): void
    {
        $this->search_query['tag_id'] = $tag_id;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }
    #[Computed]
    public function combined()
    {
        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $postsQuery = Post::where('publish_status', PublishEnum::PUBLISHED->value)->latest('publish_date')
            ->when(!empty($this->search), fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when(!empty($this->category) && $this->category != 'all', function ($q) {
                $q->whereHas('categories', function ($q2) {
                    $q2->whereHasMorph('relationable', [Category::class], fn($sub) => $sub->where('id', $this->category));
                });
            })
            ->when(!empty($this->date_from) && !empty($this->date_to),
                fn($q) => $q->whereBetween('publish_date', ["{$this->date_from} 00:00:00", "{$this->date_to} 23:59:59"]))
            ->when(!empty($this->search_query['tag_id']), function ($q) {
                $q->whereHas('tags', function ($q2) {
                    $q2->whereHasMorph('relationable', [Tag::class], fn($sub) => $sub->where('id', $this->search_query['tag_id']));
                });
            })
            ->select('id', DB::raw("'post' as stype"), DB::raw('publish_date as date'));
        $materialsQuery = Material::where('publish_status', PublishEnum::PUBLISHED->value)->latest('created_at')
            ->when(!empty($this->search), fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when(!empty($this->category) && $this->category != 'all', function ($q) {
                $q->whereHas('categories', function ($q2) {
                    $q2->whereHasMorph('relationable', [Category::class], fn($sub) => $sub->where('id', $this->category));
                });
            })
            ->when(!empty($this->date_from) && !empty($this->date_to),
                fn($q) => $q->whereBetween('created_at', ["{$this->date_from} 00:00:00", "{$this->date_to} 23:59:59"]))
            ->when(!empty($this->search_query['tag_id']), function ($q) {
                $q->whereHas('tags', function ($q2) {
                    $q2->whereHasMorph('relationable', [Tag::class], fn($sub) => $sub->where('id', $this->search_query['tag_id']));
                });
            })
            ->select('id', DB::raw("'material' as stype"), DB::raw('created_at as date'));
        $union = $postsQuery->unionAll($materialsQuery);
        $base = DB::query()->fromSub($union, 'u');
        $total = $base->count();
        $rows = $base->orderByDesc('date')
            ->forPage($page, $perPage)
            ->get();
        $postIds = $rows->where('stype', 'post')->pluck('id')->all();
        $materialIds = $rows->where('stype', 'material')->pluck('id')->all();
        $posts = Post::with(['categories.relationable', 'authors.relationable.files.file', 'files.file'])
            ->whereIn('id', $postIds)
            ->latest('publish_date')->get()
            ->keyBy('id');
        $materials = Material::with(['categories.relationable', 'presenters.relationable.files.file', 'files.file'])
            ->whereIn('id', $materialIds)
            ->latest('created_at')->get()
            ->keyBy('id');
        $items = $rows->map(function ($r) use ($posts, $materials) {
            if ($r->stype === 'post' && isset($posts[$r->id])) {
                $model = $posts[$r->id];
                $model->video_type = null;
                $model->link = null;
                $model->date = $model->publish_date;
                $model->stype = 'post';
                return $model;
            }
            if ($r->stype === 'material' && isset($materials[$r->id])) {
                $model = $materials[$r->id];
                $model->date = $model->created_at;
                $model->slug = null;
                $model->stype = 'material';
                return $model;
            }
            return null;
        })->filter()->values();
        $paginated = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );
        return $paginated;
    }

    #[Computed]
    public function categories()
    {
        return Category::all();
    }
}
