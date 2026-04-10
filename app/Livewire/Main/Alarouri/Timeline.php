<?php

namespace App\Livewire\Main\Alarouri;

use App\Models\Participant;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Timeline extends Component
{
    public function mount()
    {
        return redirect()->route('main.alarouri.index');
    }
    #[Layout('components.layouts.main.alarouri.main')]
    public function render()
    {
        return view('livewire.main.alarouri.timeline');
    }
}
