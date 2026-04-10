<?php

namespace App\Livewire\Main\Fimed;

use App\Enums\PublishEnum;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';

    #[Layout('components.layouts.main.fimed.main')]
    public function render()
    {
        return view('livewire.main.fimed.search-page');
    }

    public function mount($search = ''): void
    {
        $this->search = $search ?: request('search', '');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function results()
    {
        if (empty($this->search)) return collect();

        return Post::select('id', 'slug', 'title', 'description', 'publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where(function ($q) {
                $q->where('title', 'LIKE', "%{$this->search}%")
                    ->orWhere('description', 'LIKE', "%{$this->search}%")
                    ->orWhere('body', 'LIKE', "%{$this->search}%");
            })
            ->orderByDesc('publish_date')
            ->with(['files.file', 'author.relationable.files.file', 'category.relationable'])
            ->paginate(6);
    }
}
