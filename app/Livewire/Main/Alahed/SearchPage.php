<?php

namespace App\Livewire\Main\Alahed;

use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap_main';
    #[Url]
    public string $search = '';
    public int $total_posts = 0;

    #[Layout('components.layouts.main.alahed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $query = Post::query()
            ->with(['files.file'])
            ->when($this->search, fn($q) =>
            $q->where('title', 'like', '%' . $this->search . '%')
            );

        $this->total_posts = (clone $query)->count();

        $posts = $query->paginate(9);

        return view('livewire.main.alahed.search-page', [
            'posts' => $posts,
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}
