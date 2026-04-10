<?php

namespace App\Livewire\Main\Quds;

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
    #[Layout('components.layouts.main.quds.main')]
public function render()
    {
        return view('livewire.main.quds.special-pages');
    }
}
