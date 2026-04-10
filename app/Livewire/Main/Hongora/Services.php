<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Service;
use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Services extends Component
{

    #[Computed]
    public function services()
    {
        return Service::where('is_show', 1)
            ->orderByDesc('created_at', 'desc')
            ->with(['files.file'])
            ->take(3)
            ->get();
    }

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.services');
    }


}
