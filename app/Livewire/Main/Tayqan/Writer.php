<?php

namespace App\Livewire\Main\Tayqan;

use App\Models\Participant;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Writer extends Component
{
    use WithPagination;
    public $id;
    public $writer;
    public function mount($id)
    {
        $this->id=$id;
        $this->writer=Participant::where('id',$id)->with(['files.file','participant_social_media.icon'])->first();
        if (!$this->writer){
            abort(404);
        }
    }
    #[Computed]
    public function writerPosts()
    {
        return Post::whereHas('authors',function ($q){
            $q->where('relationable_id',$this->writer->id);
        })->latest('publish_date')->with(['files.file','category.relationable'])->paginate(6);

    }
    #[Layout('components.layouts.main.tayqan.main')]
    public function render()
    {
        return view('livewire.main.tayqan.writer');
    }
}
