<?php

namespace App\Livewire\Main\CilliumFm;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SearchInput extends Component
{
    public string $search = '';

    #[Layout('components.layouts.main.cillium_fm.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.cillium-fm.search-input');
    }

    public function putSearch()
    {
        return redirect()->route('main.search_page', ['search' => $this->search]);
    }
}
