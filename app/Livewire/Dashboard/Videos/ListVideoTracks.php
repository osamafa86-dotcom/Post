<?php

namespace App\Livewire\Dashboard\Videos;

use App\Models\Files;
use App\Models\PodcastTrack;
use App\Models\SortData;
use App\Models\VideoAlbum;
use App\Models\VideoTrack;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListVideoTracks extends Component
{
    use WithPagination, WithFileUploads, WhereIn;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Tracks_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.videos.list-video-tracks');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function tracks(): LengthAwarePaginator
    {
        return VideoTrack::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->with('files')->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function albums(): Collection
    {
        return VideoAlbum::all();
    }

    public function sortBy($field): void
    {
        $this->sortDirection = isset($this->sortDirection) && $this->sortDirection == 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_track_form');
    }

    public function edit(VideoTrack $track): void
    {
        $this->state = [];
        $this->Tracks_ = $track;
        $this->state = $track->toArray();
        if (isset($this->Tracks_->files->file_id)) {
            $this->state['image'] = $this->Tracks_->files->file_id;
            $this->state['image_name'] = Files::where('id', $this->Tracks_->files->file_id)->value('path');
        }
        $this->showEdit = true;
        $this->dispatch('show_track_form');
    }

    /**
     * @throws ValidationException
     */
    public function createTrack(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200|unique:video_tracks',
            'image' => 'required|integer|exists:files,id',
            'url' => 'required|url',
            'video_album_id' => 'required|in:' . $this->whereIn()['VideoAlbum'],
            'description' => 'nullable|string|max:255',

        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);
        $VideoTrack = VideoTrack::query()->create($validation);
        $VideoTrack->files()->create([
            'file_id' => $image ?? null,
            'model_type' => VideoTrack::class,
            'model_column' => 'image',
        ]);
        $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
        $VideoTrack->sortable()->create([
            'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
        ]);
        $VideoTrack->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_track_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateTrack(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200|unique:video_tracks,title,' . $this->Tracks_->id,
            'image' => 'required|integer|exists:files,id',
            'description' => 'nullable|string|max:255',
            'url' => 'required|url',
            'video_album_id' => 'required|in:' . $this->whereIn()['VideoAlbum'],
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $before_data = $this->Tracks_->toArray();

        $this->Tracks_->update($validation);
        $this->Tracks_->files()->update([
            'file_id' => $image ?? null,
        ]);

        $new_data = $this->Tracks_->toArray();

        $this->Tracks_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_track_form', ['message' => __('validation.edit_success')]);
    }

    public function trackDelete(VideoTrack $track): void
    {
        $this->Tracks_ = $track;
        $this->dispatch('show_track_delete');
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['image'] = $id;
        $this->state['image_name'] = Files::where('id', $id)->value('path');
    }

    public function trackDeleteConfirm(): void
    {
        $this->Tracks_->delete();
        $this->Tracks_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_track_delete');
    }
}
