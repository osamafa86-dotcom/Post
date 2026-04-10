<?php

namespace App\Livewire\Main\Hongora;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SearchInput extends Component
{

    public $search_text;

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.search-input');
    }


    public function getSearchPage()
    {
        return redirect()->route('main.hongora.search_page' , ['search' => $this->search_text]);

    }
}
