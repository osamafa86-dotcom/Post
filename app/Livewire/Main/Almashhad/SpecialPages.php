<?php

namespace App\Livewire\Main\Almashhad;

use App\Models\SpecialPage;
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
    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        return view('livewire.main.almashhad.special-pages');
    }
}
