<?php

namespace App\Livewire\Main\Hongora;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AllAuthorArticles extends Component
{
    public object $author_articles;
    public object $otherArticles;

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.all-author-articles');
    }

    public function mount($author_id): void
    {
        $this->author_articles = Author::with('posts')->find($author_id);
        $this->otherArticles = Post::with('author')
            ->whereNotNull('author_id')
            ->where('author_id','!=', $author_id)
            ->orderBy('publish_date', 'desc')
            ->take(5)
            ->get();
    }
}
