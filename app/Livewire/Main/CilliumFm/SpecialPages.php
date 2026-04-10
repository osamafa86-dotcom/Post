<?php

namespace App\Livewire\Main\CilliumFm;

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
    #[Layout('components.layouts.main.cillium_fm.main')]
    public function render()
    {
        return view('livewire.main.cillium-fm.special-pages');
    }
}
