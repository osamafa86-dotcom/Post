<?php

namespace App\Livewire\Main\Almohajer;

use App\Enums\CategoryTypeEnum;
use App\Enums\DataPageEnum;
use App\Models\Category;
use App\Models\Event;
use App\Models\ImmigrantData;
use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Opportunities extends Component
{
    use WithFileUploads;
    #[Computed]
    public function data_opportunities()
    {
        return ImmigrantData::query()->with(['files'])
            ->where('type', DataPageEnum::OPPORTUNITY)
            ->orderBy('created_at','asc')
            ->take(5)
            ->get();
    }

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.opportunities');
    }
}
