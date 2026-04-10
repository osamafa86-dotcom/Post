<?php

namespace App\Livewire\Main\CilliumFm;

use Livewire\Attributes\On;
use Livewire\Component;

class FooterAudioPlayer extends Component
{
    #[On('playerStatusChanged')]
    public function playerStatusChanged($status){
        $this->dispatch('control-player', status: $status);

    }
    public function render()
    {
        return view('livewire.main.cillium-fm.footer-audio-player');
    }
}
