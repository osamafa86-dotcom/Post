<?php

namespace App\Livewire\Main\Alahed;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class SearchForm extends Component
{
    public ?string $search;
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.alahed.search-form');
    }

    public function searchPost()
    {
        return redirect('/search?search=' . urlencode($this->search ?? ''));
    }
}
