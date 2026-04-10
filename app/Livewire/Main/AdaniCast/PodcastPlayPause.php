<?php

namespace App\Livewire\Main\AdaniCast;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class PodcastPlayPause extends Component
{
    public $id;
    public function mount($id=null){
        $this->id=$id;
    }
    #[On('refreshPodcastButton')]
    public function refresh()
    {
        $this->render();
    }
    public function render()
    {
        return view('livewire.main.adani-cast.podcast-play-pause', [
            'isPlaying' => Cache::get('podcast_id'),
        ]);
    }
}
