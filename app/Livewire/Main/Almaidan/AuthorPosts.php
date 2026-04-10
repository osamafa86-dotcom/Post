<?php

namespace App\Livewire\Main\Almaidan;

use App\Enums\PublishEnum;
use App\Models\Author;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class AuthorPosts extends Component
{
    use withPagination;

    protected string $paginationTheme = 'bootstrap';
    public int $author_id;
    public object $author;
    public string $author_search = '';

    #[Layout('components.layouts.main.almaidan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almaidan.author-posts');
    }

    public function mount(): void
    {

        $this->author = Participant::where('id',$this->author_id)->with(['files.file','participant_social_media'])->first();

    }

    #[Computed]
    public function posts()
    {

        return Post::latest('publish_date')
            ->when(!empty($this->author_id) , function ($query) {
                $query->WhereHas('author', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Participant::class],
                        function ($subQuery) {
                            $subQuery->where('id',$this->author_id);
                        }
                    );
                });

            })
            ->when(!empty($this->author_search) , function ($query) {
                $query->where('title', 'like', '%' . $this->author_search . '%');
            })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->paginate(9);
    }

    public function updatedAuthorSearch(): void
    {
        $this->resetPage();
    }


}
