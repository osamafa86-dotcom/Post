<?php

namespace App\Livewire\Main\Almohajer;

use App\Enums\PublishEnum;
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

    protected $paginationTheme = 'bootstrap';
    #[Url(keep: true)]
    public $author_id;
    public $search = '';

    #[Computed]
    public function author()
    {
        return Participant::where('id', $this->author_id)->with(['files.file', 'participant_social_media'])->first();
    }

    #[Computed]
    public function authorPosts()
    {
        $authorId = $this->author?->id;

        return Post::query()
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->whereHas('authors', function ($qq) use ($authorId) {
                $qq->where('relationable_id', $authorId);
            })
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->with(['category.relationable', 'authors.relationable', 'files.file'])
            ->orderByDesc('publish_date')   // اختياري
            ->paginate(9);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Layout('components.layouts.main.almohajer.main')]
    public function render()
    {
        return view('livewire.main.almohajer.writer');
    }
}
