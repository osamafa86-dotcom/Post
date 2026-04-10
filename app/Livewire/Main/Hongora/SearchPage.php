<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Author;
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

    #[Url]
    public string $search = '';

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {

        $posts = Post::when(!empty($this->search), function ($q) {
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
        })->with(['category.relationable', 'author.relationable', 'files.file'])->paginate(5);



        return view('livewire.main.hongora.search-page' , compact('posts' ));
    }

    public function mount($search = null): void
    {
        $this->search = $search ?? '';
    }

    #[Computed]
    public function top_view_posts(): Collection|array
    {
        return Post::
            where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('views', 'desc')
            ->take(4)
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->get();
    }

}
