<?php

namespace App\Livewire\Main\Hongora;

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

class AuthorPosts extends Component
{

    public int $author_id;
    public object $author;

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.author-posts');
    }

    public function mount(): void
    {

        $this->author = Participant::find($this->author_id);

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
            //    $query->where('author_id', $this->author_id);
          //  })

            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->paginate(8);
    }


    #[Computed]
    public function top_view_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('views', 'desc')
            ->take(5)
            ->with(['category.relationable', 'author.relationable', 'files.file'])
            ->get();
    }
}
