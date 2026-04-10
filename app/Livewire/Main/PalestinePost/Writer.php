<?php

namespace App\Livewire\Main\PalestinePost;

use App\Models\Participant;
use App\Models\Post;
use App\Models\Quote;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Writer extends Component
{
    use withPagination;

    protected string $paginationTheme = 'bootstrap';
    public $author ;
    public $postLatest;
    public $quoteLatest;
    public string $currant_tab = "مقالات";
    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.palestine-post.writer');
    }

    public function updatingPage()
    {
//        $this->dispatch('scroll-to-bottom');
    }

    public function mount($author_id): void
    {
        $this->author = Participant::where('id', $author_id)->first();
        $this->currant_tab = count($this->posts) > 0 ? "مقالات" : "أقتباسات";

    }

    #[Computed]
    public function posts()
    {
        return Post::whereHas('author', function ($q) {
            $q->where('relationable_id',$this->author->id);

        })->with('category.relationable')->latest()->paginate(8);
    }

    #[Computed]
    public function quotes()
    {
        return Quote::where('author_id', $this->author->id)->with(['author.files.file'])->latest()->paginate(8);
    }

    public function setTab($tab): void
    {
        $this->currant_tab = $tab;
        $this->resetPage(); // Ensure pagination resets when switching tabs
    }

}
