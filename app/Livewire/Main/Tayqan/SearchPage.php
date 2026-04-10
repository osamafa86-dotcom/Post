<?php

namespace App\Livewire\Main\Tayqan;

use App\Models\Category;
use App\Models\Participant;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Type;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPage extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';
    #[Url(keep: true)]
    public string $search = '';
    #[Url(keep: true)]
    public string $search_category = '';
    #[Url(keep: true)]
    public string $search_type = '';
    #[Url(keep: true)]
    public string $from_date = '';
    #[Url(keep: true)]
    public string $to_date = '';
    protected $rules = [
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:from_date',
    ];
    protected $messages = [
        'to_date.after_or_equal' => 'يجب أن يكون التاريخ إلى بعد أو مساوي للتاريخ من',
    ];
    public $category;
    public $type;
    public function mount($category = null, $type = null)
    {
        if ($category) {
            $this->search_category = $category;
        }
        if ($type) {
            $this->search_type = $type;
        }
    }
    public function resetAllFilters()
    {
        $this->reset(['from_date', 'to_date','search_category','search_type','search']);
        $this->resetPage();
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    #[Computed]
    public function posts()
    {
        $this->validate();
        $posts = Post::query()
            ->when(!empty($this->search), function ($q) {
                $search = $this->search;
                // Wrap the text search in a closure to group conditions
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhereHas('categories', function ($catQuery) use ($search) {
                            $catQuery->whereHasMorph(
                                'relationable',
                                [Category::class],
                                function ($subQuery) use ($search) {
                                    $subQuery->where('category_title', 'like', '%' . $search . '%');
                                }
                            );
                        })
                        ->orWhereHas('tags', function ($tagQuery) use ($search) {
                            $tagQuery->whereHasMorph(
                                'relationable',
                                [Tag::class],
                                function ($subQuery) use ($search) {
                                    $subQuery->where('tag_name', 'like', '%' . $search . '%');
                                }
                            );
                        })
                        ->orWhereHas('authors', function ($authorQuery) use ($search) {
                            $authorQuery->whereHasMorph(
                                'relationable',
                                [Participant::class],
                                function ($subQuery) use ($search) {
                                    $subQuery->where('name', 'like', '%' . $search . '%');
                                }
                            );
                        });
                });
            })
            ->when(!empty($this->search_category) && $this->search_category !== 'all', function ($q) {
                $q->whereHas('categories', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('category_title', $this->search_category);
                        }
                    );
                });
            })
            ->when(!empty($this->search_type) && $this->search_type !== 'all', function ($q) {
                $q->whereHas('types', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Type::class],
                        function ($subQuery) {
                            $subQuery->where('type_name', $this->search_type);
                        }
                    );
                });
            })
            ->when(!empty($this->from_date), function ($q) {
                $q->whereDate('publish_date', '>=', $this->from_date);
            })
            ->when(!empty($this->to_date), function ($q) {
                $q->whereDate('publish_date', '<=', $this->to_date);
            })
            ->with(['categories.relationable','authors.relationable.files.file','publishers.relationable.files.file', 'types.relationable', 'files.file'])
            ->latest('publish_date')
            ->paginate(6);
        return $posts;
    }
    #[Layout('components.layouts.main.tayqan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tayqan.search-page');
    }
    #[Computed]
    public function categories()
    {
        return Category::whereNotNull('category_title')
            ->pluck('category_title')
            ->unique()
            ->filter()
            ->values();
    }
    #[Computed]
    public function types()
    {
        return \App\Models\Type::whereNotNull('type_name')
            ->where('show_index',true)
            ->orderBy('order')
            ->pluck('type_name')
            ->unique()
            ->filter()
            ->values();
    }
}
