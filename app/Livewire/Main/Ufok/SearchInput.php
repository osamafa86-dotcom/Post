<?php

namespace App\Livewire\Main\Ufok;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SearchInput extends Component
{
    public string $search = '';

    #[Layout('components.layouts.main.ufok.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.ufok.search-input');
    }

    public function putSearch()
    {
        return redirect()->route('main.search_page', ['search' => $this->search]);
    }
}
