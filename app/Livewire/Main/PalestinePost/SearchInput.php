<?php
namespace App\Livewire\Main\PalestinePost;

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

class SearchInput extends Component
{

    public $search_text;
    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {

        return view('livewire.main.palestine-post.search-input');
    }

    public function getSearchPage()
    {
        return redirect()->route('main.palestine_post.search_page' , ['search' => $this->search_text]);

    }

}
