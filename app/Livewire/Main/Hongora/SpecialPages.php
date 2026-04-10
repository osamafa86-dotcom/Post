<?php

namespace App\Livewire\Main\Hongora;

use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SpecialPages extends Component
{
    public object $page;
    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.special-pages');
    }

    public function mount($id): void
    {
        $search = SpecialPage::where('id', $id)->first();
        if ($search) {
            $this->page = $search;
        }
    }
}
