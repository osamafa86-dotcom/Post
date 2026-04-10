<?php

namespace App\Livewire\Main\Almohajer;

use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CategoryPosts extends Component
{

    public int $category_id;
    public object $category;

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.category-posts');
    }

    public function mount(): void
    {

        $this->category = Category::find($this->category_id);

    }

    #[Computed]
    public function posts()
    {

        return Post::latest('publish_date')
            ->when(!empty($this->category_id) , function ($query) {
                $query->WhereHas('category', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('id',$this->category_id);
                        }
                    );
                });

            })
//            ->when(!empty($this->category_id) , function ($query) {
//                $query->where('category_id', $this->category_id);
//            })

            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('publish_date', 'desc')
            ->with('category', 'author', 'files')
            ->paginate(8);
    }


    #[Computed]
    public function top_view_posts(): Collection|array
    {
        return Post::latest('publish_date')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->orderBy('views', 'desc')
            ->take(5)
            ->with('category', 'author', 'files')
            ->get();
    }
}
