<?php

namespace App\Livewire\Main\Almohajer;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SearchInput extends Component
{

    public $search_text;

    #[Layout('components.layouts.main.almohajer.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.almohajer.search-input');
    }


    public function getSearchPage()
    {
        return redirect()->route('main.almohajer.search_page' , ['search' => $this->search_text]);

    }
}
