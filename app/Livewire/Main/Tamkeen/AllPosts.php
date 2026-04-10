<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\CategoryTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AllPosts extends Component
{
    public array $search_query = [];
    public $category_id , $tag_id;

    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tamkeen.all-posts');
    }


    public function mount($category_id = null): void
    {
        if ($category_id) {
            $this->search_query['category_id'] = $category_id;
        } else {
            $this->category_id = 'all';
        }

    }

    #[Computed]
    public function posts(): Collection|array
    {
      return Post::with(['category.relationable','files.file'])->when(!empty($this->search_query['search_text']), function ($query) {
          $query->where('title', 'like', '%' . $this->search_query['search_text'] . '%');
      })
          ->when(!empty($this->search_query['date']), function ($query) {
              $query->whereBetween('publish_date', [$this->search_query['date'] . ' 00:00:00', $this->search_query['date'] . ' 23:59:59']);
          })
          ->when(!empty($this->search_query['category_id'])  && $this->search_query['category_id'] != 'all', function ($query) {
              $query->whereHas('category', function ($query) {
                  $query->whereHasMorph(
                      'relationable',
                      [Category::class],
                      function ($subQuery) {
                          $subQuery->where('id', $this->search_query['category_id']);
                      }
                  );
              });
          })
          ->when(!empty($this->tag_id), function ($query)  {
              $query->whereHas('tag', function ($query) {
                  $query->whereHasMorph(
                      'relationable',
                      [Tag::class],
                      function ($subQuery) {
                          $subQuery->where('id', $this->tag_id);
                      }
                  );
              });
          })
          ->whereHas('category', function ($query) {
          $query->whereHasMorph(
              'relationable',
              [Category::class],
              function ($subQuery) {
                  $subQuery->where('category_type', CategoryTypeEnum::NEWS->value);
              }
          );
      })->latest()->get();


    }


    #[Computed]
    public function categories(): Collection|array
    {

        return Category::query()
            ->where('category_type', CategoryTypeEnum::NEWS->value)
            ->whereHas('post_relation')
            ->with('post_relation')
            ->withCount('post_relation')
            ->orderBy('post_relation_count', 'desc')
            ->get();
    }
}
