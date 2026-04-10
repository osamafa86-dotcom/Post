<?php

namespace App\Livewire\Main\Almashhad;

use App\Enums\ParticipantTypeEnum;
use App\Models\Participant;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Writer extends Component
{
    #[Url]
    public $id;
    public $writer;
    public $writerPost;
    public function mount($id)
    {
        $this->id=$id;
        $this->writer=Participant::where('id',$id)->with(['files.file','quotes','participant_social_media.icon'])->first();
        $this->wirterPost=Post::with(['files.file','author.relationable','category.relationable'])->whereHas('authors',function ($q){
            $q->where('relationable_id',$this->writer->id);
        })->latest()->take(6)->get();

        if (!$this->writer){
            abort(404);
        }
    }
    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        return view('livewire.main.almashhad.writer');
    }
}
