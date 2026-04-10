<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SpecialPages extends Component
{
    public object $page;
    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.special-pages');
    }

    public function mount($id): void
    {
        $specialPage = SpecialPage::where('id', $id)->with(['files.file'])->first();
        if ($specialPage) {
            $this->page = $specialPage;
        }
    }
}
