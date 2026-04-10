<?php

namespace App\Livewire\Main\Ufok;

use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Event;
use App\Models\Material;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Courses extends Component
{


    #[Layout('components.layouts.main.ufok.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.ufok.courses');
    }

    #[Computed]
    public function courses()
    {
        return Event::query()

           ->whereHas('category', function ($q2) {

                    $q2->whereHasMorph(
                        'relationable',
                        [Category::class],
                        function ($subQuery) {
                            $subQuery->where('category_type', CategoryTypeEnum::COURSES->value);
                        }
                    );


                })

            ->with(['tags','category','files'])
            ->get();
    }



}
