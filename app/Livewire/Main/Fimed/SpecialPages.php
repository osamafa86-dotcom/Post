<?php

namespace App\Livewire\Main\Fimed;

use App\Models\SpecialPage;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SpecialPages extends Component
{
    public $page;

    #[Layout('components.layouts.main.fimed.main')]
    public function render()
    {
        return view('livewire.main.fimed.special-pages');
    }

    public function mount($id): void
    {
        $this->page = SpecialPage::findOrFail($id);
    }
}
