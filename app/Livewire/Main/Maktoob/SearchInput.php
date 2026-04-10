<?php

namespace App\Livewire\Main\Maktoob;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SearchInput extends Component
{

    public $search_text;

    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.search-input');
    }


    public function getSearchPage()
    {
        return redirect()->route('main.maktoob.search_page' , ['search' => $this->search_text]);

    }
}
