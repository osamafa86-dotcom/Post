<?php

namespace App\Livewire\Main\Almohajer;

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

    #[Url(keep: true)]
    public string $search = '';

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {

        $posts = Post::when(!empty($this->search), function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%')
                ->orWhereHas('categories', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('category_title', 'like', '%' . $this->search . '%');
                        }
                    );
                })
                ->orWhereHas('tags', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Tag::class],
                        function ($subQuery) {
                            $subQuery->where('tag_name', 'like', '%' . $this->search . '%');
                        }
                    );
                })
                ->orWhereHas('authors', function ($query) {
                    $query->whereHasMorph(
                        'relationable',
                        [Participant::class],
                        function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%');
                        }
                    );
                });
        })->with(['category.relationable','author.relationable','files.file'])->paginate(6);



        return view('livewire.main.almohajer.search-page' , compact('posts' ));
    }

    public function mount($search = null): void
    {
        $this->search = $search ?? '';
    }

    #[Computed]
    public function top_view_posts(): Collection|array
    {
        return Post::where('publish_status', PublishEnum::PUBLISHED->value)
            ->withSum('views', 'views_number')
            ->orderBy('views_sum_views_number', 'desc')
            ->take(4)
            ->with(['files.file'])
            ->get();
    }

}
