<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\CategoryTypeEnum;
use App\Models\Author;
use App\Models\Category;
use App\Models\PodcastAlbum;
use App\Models\Post;
use App\Models\Tag;
use App\Models\VideoAlbum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPage extends Component
{
    use withPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';
    #[Url]
    public string $tab = 'posts';

    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $posts = Post::when(!empty($this->search), function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%');
        })->with(['files.file', 'category.relationable'])->paginate(10, ['*'], 'posts');
        $tags = Tag::when(!empty($this->search), function ($q) {
            $q->where('tag_name', 'like', '%' . $this->search . '%');
        })->paginate(10, ['*'], 'tags');
        $categories = Category::when(!empty($this->search), function ($q) {
            $q->where('category_title', 'like', '%' . $this->search . '%');
        })->where('category_type', CategoryTypeEnum::NEWS->value)->paginate(10, ['*'], 'categories');
//        $authors = Author::when(!empty($this->search), function ($q) {
//            $q->where('author_name', 'like', '%' . $this->search . '%');
//        })->paginate(10, ['*'], 'authors');
        $podcasts = PodcastAlbum::when(!empty($this->search), function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%');
        })->paginate(10, ['*'], 'podcasts');
        $videos = VideoAlbum::when(!empty($this->search), function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%');
        })->paginate(10, ['*'], 'videos');
        return view('livewire.main.tamkeen.search-page', [
            'posts' => $posts,
            'tags' => $tags,
            'categories' => $categories,
//            'authors' => $authors,
            'podcasts' => $podcasts,
            'videos' => $videos
        ]);
    }

    public function mount($search = null): void
    {
        $this->search = $search ?? '';
    }

    public function changeTab($tab): void
    {
        $this->tab = $tab;
        $this->resetPage('posts');
        $this->resetPage('tags');
        $this->resetPage('categories');
        $this->resetPage('authors');
        $this->resetPage('podcasts');
        $this->resetPage('videos');
    }
}
