<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.main.adani_cast.main')]
class EducationNews extends Component
{
    public $education_title = 'ورشة عمل';

    public function mount($education_title = null)
    {
        if ($education_title) {
            $this->education_title = $education_title;
        }
    }

    #[Computed]
    public function posts()
    {
        $categoryId = Category::where('category_title', $this->education_title)->first()?->id;

        return Post::
            whereHas('category', function ($q) use  ($categoryId){
                $q->whereHasMorph(
                    'relationable',
                    [Category::class],
                    function ($subQuery) use ($categoryId) {
                        $subQuery->where('id', $categoryId);
                    }
                );


            })
            ->selectRaw('YEAR(publish_date) as year, GROUP_CONCAT(id) as post_ids')
            ->groupByRaw('YEAR(publish_date)')
            ->get()
            ->map(function ($group) {
                return [
                    'year' => $group->year,
                    'posts' => Post::whereIn('id', explode(',', $group->post_ids))->get(),
                ];
            })->sortByDesc('year');
    }


    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.education-news');
    }
}
