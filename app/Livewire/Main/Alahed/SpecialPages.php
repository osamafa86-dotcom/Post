<?php

namespace App\Livewire\Main\Alahed;

use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SpecialPages extends Component
{
    public object $page;
    public function mount($id): void
    {
        $search = SpecialPage::where('id', $id)->with(['files.file'])->first();
        if ($search) {
            $this->page = $search;
        }
    }
    #[Layout('components.layouts.main.alahed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.alahed.special-pages');
    }
}
