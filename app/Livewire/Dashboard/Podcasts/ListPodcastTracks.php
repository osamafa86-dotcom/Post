<?php

namespace App\Livewire\Dashboard\Podcasts;

use App\Models\Files;
use App\Models\PodcastAlbum;
use App\Models\PodcastTrack;
use App\Models\SortData;
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

class ListPodcastTracks extends Component
{
    use WithPagination, WithFileUploads;

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
        return view('livewire.dashboard.podcasts.list-podcast-tracks');
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
        return PodcastTrack::select('*')
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
        return PodcastAlbum::all();
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

    public function edit(PodcastTrack $track): void
    {
        $this->state = [];
        $this->Tracks_ = $track;
        $this->state = $track->toArray();
        if (isset($this->Tracks_->files)) {
            foreach ($this->Tracks_->files as $file) {
                $this->state[$file->model_column] = $file->file_id;
                $this->state[$file->model_column . '_name'] = Files::where('id', $file->file_id)->value('path');
            }
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
            'title' => 'required|string|max:200',
            'image' => 'required|integer|exists:files,id',
            'description' => 'nullable|string|max:255',
            'url' => 'required_without:url_id|url',
            'url_id' => 'required_without:url',
            'podcast_album_id' => 'required|exists:podcast_albums,id',
        ])->validate();

        $image = $validation['image'] ?? null;
        $url_id = $validation['url_id'] ?? null;
        unset($validation['image'], $validation['url_id']);
        $PodcastTrack = PodcastTrack::query()->create($validation);
        $PodcastTrack->files()->create([
            'file_id' => $image ?? null,
            'model_type' => PodcastTrack::class,
            'model_column' => 'image',
        ]);
        if (isset($url_id)) {
            $PodcastTrack->files()->create([
                'file_id' => $url_id,
                'model_type' => PodcastTrack::class,
                'model_column' => 'url_id',
            ]);
        }
        $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
        $PodcastTrack->sortable()->create([
            'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
        ]);
        $PodcastTrack->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_track_form', ['message' => __('validation.saved_success')]);
    }

    #[On('imageSelected')]
    public function imageSelected($id, $column = null): void
    {
        if (isset($column)) {
            $this->state[$column] = $id;
            $this->state[$column . '_name'] = Files::where('id', $id)->value('path');
        }
    }

    public function clearColumn($column, $value = null)
    {
        if (!isset($value)) {
            $this->state[$column] = null;
            $this->state[$column . '_name'] = null;
        } else {
            if (($key = array_search($value, $this->state[$column])) !== false) {
                unset($this->state[$column][$key]);
            }
            $this->state[$column] = array_values($this->state[$column]);
            unset($this->state[$column . '_name'][$value]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function updateTrack(): void
    {

        if ($this->Tracks_->image != $this->state['image']) {
            $rule_image = 'required|integer|exists:files,id';
        } else {
            $rule_image = 'nullable';
        }

        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200',
            'image' => $rule_image,
            'description' => 'nullable|string|max:255',
            'url' => 'required_without:url_id',
            'url_id' => 'required_without:url',
            'podcast_album_id' => 'required|exists:podcast_albums,id',
        ])->validate();

        $image = $validation['image'] ?? null;
        $url_id = $validation['url_id'] ?? null;
        unset($validation['image'], $validation['url_id']);

        $before_data = $this->Tracks_->toArray();

        $this->Tracks_->update($validation);

        $new_data = $this->Tracks_->toArray();

        $this->Tracks_->files()->update([
            'file_id' => $image,
        ]);
        $this->Tracks_->files()->where('model_column', 'url_id')->delete();
        if (isset($url_id)) {
            $this->Tracks_->files()->create([
                'file_id' => $url_id,
                'model_type' => PodcastTrack::class,
                'model_column' => 'url_id',
            ]);
        }
        $this->Tracks_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_track_form', ['message' => __('validation.edit_success')]);
    }

    public function trackDelete(PodcastTrack $track): void
    {
        $this->Tracks_ = $track;
        $this->dispatch('show_track_delete');
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
