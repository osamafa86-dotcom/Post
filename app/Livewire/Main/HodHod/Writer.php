<?php

namespace App\Livewire\Main\HodHod;

use App\Models\Participant;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Writer extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $id;
    public $search = '';

    #[Computed]
    public function author()
    {
        return Participant::where('id', $this->id)->with(['files.file', 'participant_social_media'])->first();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function authorPosts()
    {
        return Post::whereHas('authors', function ($q) {
            $q->where('relationable_id', $this->author?->id);
        })->when($this->search, function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%');
        })->with(['category.relationable', 'author.relationable', 'files.file'])->latest('publish_date')->paginate(8);
    }

    #[Layout('components.layouts.main.hodhod.main')]
    public function render()
    {
        return view('livewire.main.hod-hod.writer');
    }
}
