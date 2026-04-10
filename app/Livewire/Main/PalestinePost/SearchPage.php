<?php

namespace App\Livewire\Main\PalestinePost;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Participant;
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

    //public $posts;
   // public $podcasts;
    //public $videos;

    #[Layout('components.layouts.main.palestine_post.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $posts = Post::with(['files.file','category.relationable','tag.relationable','author.relationable'])->when(!empty($this->search), function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%')
                ->orWhereHas('category', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('category_title', 'like', '%' . $this->search . '%');
                        }
                    );
                })
                ->orWhereHas('tag', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('tag_name', 'like', '%' . $this->search . '%');
                        }
                    );
                })
                ->orWhereHas('author', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Participant::class],
                        function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%');
                        }
                    );
                });
        })->paginate(5);

        $podcasts = Material::with(['files.file','category.relationable','tag.relationable','presenter.relationable'])->when(!empty($this->search), function ($q) {
            $q->where('type', MaterialTypeEnum::PODCAST->value)
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhereHas('material_album', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('category', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('category_title', 'like', '%' . $this->search . '%');
                        }
                    );
                })
                ->orWhereHas('tag', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('tag_name', 'like', '%' . $this->search . '%');
                        }
                    );
                });
        })->paginate(5);

        $videos = Material::when(!empty($this->search), function ($q) {
            $q->where('type', MaterialTypeEnum::VIDEO->value)

                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhereHas('material_album', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('category', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('category_title', 'like', '%' . $this->search . '%');
                        }
                    );
                })
                ->orWhereHas('tag', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('tag_name', 'like', '%' . $this->search . '%');
                        }
                    );
                });
        })->paginate(5);

        return view('livewire.main.palestine-post.search-page' , compact('posts' , 'videos' , 'podcasts' ));
    }

    public function mount($search = null): void
    {
        $this->search = $search ?? request('search', '');
    }


}
