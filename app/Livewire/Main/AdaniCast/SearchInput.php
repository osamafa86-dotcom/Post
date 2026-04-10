<?php

namespace App\Livewire\Main\AdaniCast;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SearchInput extends Component
{
    public string $search = '';

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.search-input');
    }

    public function putSearch()
    {
        return redirect()->route('main.search_page', ['search' => $this->search]);
    }
}
