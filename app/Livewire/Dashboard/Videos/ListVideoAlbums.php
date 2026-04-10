<?php

namespace App\Livewire\Dashboard\Videos;

use App\Models\Files;
use App\Models\VideoAlbum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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

class ListVideoAlbums extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Albums_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.videos.list-video-albums');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function albums(): LengthAwarePaginator
    {
        return VideoAlbum::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->with('files')->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[On('imageSelected')]
    public function imageSelected($id, $column = null)
    {
        if (isset($column)) {
            $this->state[$column] = $id;
            $this->state[$column . '_name'] = Files::where('id', $id)->value('path');
        }
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
        $this->dispatch('show_album_form');
    }

    public function edit(VideoAlbum $album): void
    {
        $this->state = [];
        $this->Albums_ = $album;
        $this->state = $album->toArray();
        if (isset($this->Albums_->files)) {
            foreach ($this->Albums_->files as $file) {
                $this->state[$file->model_column] = $file->file_id;
                $this->state[$file->model_column . '_name'] = Files::where('id', $file->file_id)->value('path');
            }
        }
        $this->showEdit = true;
        $this->dispatch('show_album_form');
    }

    /**
     * @throws ValidationException
     */
    public function createAlbum(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200|unique:video_albums',
            'small_image' => 'required_without:background_image|integer|exists:files,id',
            'background_image' => 'required_without:small_image|integer|exists:files,id',
            'description' => 'nullable|string|max:255',
        ])->validate();

        $small_image = $validation['small_image'] ?? null;
        $background_image = $validation['background_image'] ?? null;
        unset($validation['small_image'], $validation['background_image']);
        $VideoAlbum = VideoAlbum::query()->create($validation);
        $VideoAlbum->files()->createMany([
            [
                'file_id' => $small_image ?? null,
                'model_type' => VideoAlbum::class,
                'model_column' => 'small_image',
            ],
            [
                'file_id' => $background_image ?? null,
                'model_type' => VideoAlbum::class,
                'model_column' => 'background_image'
            ]
        ]);
        $VideoAlbum->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_album_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateAlbum(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200|unique:video_albums,title,' . $this->Albums_->id,
            'small_image' => 'required_without:background_image|integer|exists:files,id',
            'background_image' => 'required_without:small_image|integer|exists:files,id',
            'description' => 'nullable|string|max:255',
        ])->validate();

        $small_image = $validation['small_image'] ?? null;
        $background_image = $validation['background_image'] ?? null;
        unset($validation['small_image'], $validation['background_image']);

        $before_data = $this->Albums_->toArray();

        $this->Albums_->update($validation);
        $this->Albums_->files()->where('model_column', 'small_image')->update([
            'file_id' => $small_image ?? null,
        ]);
        $this->Albums_->files()->where('model_column', 'background_image')->update([
            'file_id' => $background_image ?? null,
        ]);

        $new_data = $this->Albums_->toArray();

        $this->Albums_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_album_form', ['message' => __('validation.edit_success')]);
    }

    public function albumDelete(VideoAlbum $album): void
    {
        $this->Albums_ = $album;
        $this->dispatch('show_album_delete');
    }

    public function albumDeleteConfirm(): void
    {
        $this->Albums_->delete();
        $this->Albums_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_album_delete');
    }
}
