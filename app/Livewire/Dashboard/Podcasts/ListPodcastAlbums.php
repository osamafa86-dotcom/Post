<?php

namespace App\Livewire\Dashboard\Podcasts;

use App\Models\Files;
use App\Models\PodcastAlbum;
use App\Models\Quote;
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
use App\Models\Icon;

class ListPodcastAlbums extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Albums_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";

    public array $album_links = [];
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.podcasts.list-podcast-albums');
    }

    #[On('imageSelected')]
    public function imageSelected($id, $column = null): void
    {
        if (isset($column)) {
            $this->state[$column] = $id;
            $this->state[$column . '_name'] = Files::where('id', $id)->value('path');
        }
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
        return PodcastAlbum::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->with('files')->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
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
        $this->album_links =[];
        $this->dispatch('show_album_form');
    }

    public function edit(PodcastAlbum $album): void
    {
        $this->state = [];
        $this->Albums_ = $album;
        $this->state = $album->toArray();
        $this->album_links = $album->podcast_album_links->toArray();
        $this->state[$this->Albums_->files->model_column] = $this->Albums_->files?->file_id;
        $this->state[$this->Albums_->files->model_column . '_name'] = $this->Albums_->files->file->path;
        $this->showEdit = true;
        $this->dispatch('show_album_form');
    }

    /**
     * @throws ValidationException
     */
    public function createAlbum(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200',
            'image' => 'required|integer|exists:files,id',
            'description' => 'nullable|string|max:255',
            'sound_cloud' => 'nullable|url',
            'google_cast' => 'nullable|url',
            'spotify' => 'nullable|url',
            'apple_cast' => 'nullable|url',
        ])->validate();

        $validation_album_links = Validator::make($this->album_links, [
            '*.title' => 'required|string|max:50',
            '*.url' => 'required|url|max:255',
            '*.icon_id' => 'nullable|integer|exists:icons,id',
        ],[
            '*.title.required' => __('validation.title_required'),
            '*.url.required' => __('validation.url_required'),
            '*.icon_id.required' => __('validation.icon_id_required'),
        ])->validate();

        $image = $validation['image'];
        unset($validation['image']);
        $PodcastAlbum = PodcastAlbum::query()->create($validation);

        $PodcastAlbum->podcast_album_links()->createMany($validation_album_links);

        $PodcastAlbum->files()->create([
                'file_id' => $image ?? null,
                'model_type' => PodcastAlbum::class,
                'model_column' => 'image',
        ]);
        $PodcastAlbum->user_logs()->create([
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
            'title' => 'required|string|max:200',
            'image' => 'required|integer|exists:files,id',
            'description' => 'nullable|string|max:255',
            'sound_cloud' => 'nullable|url',
            'google_cast' => 'nullable|url',
            'spotify' => 'nullable|url',
            'apple_cast' => 'nullable|url',
        ])->validate();

        $validation_album_links = Validator::make($this->album_links, [
            '*.title' => 'required|string|max:50',
            '*.url' => 'required|url|max:255',
            '*.icon_id' => 'nullable|integer|exists:icons,id',
        ],[
            '*.title.required' => __('validation.title_required'),
            '*.url.required' => __('validation.url_required'),
            '*.icon_id.required' => __('validation.icon_id_required'),
        ])->validate();


        $image = $validation['image'];

        $before_data = $this->Albums_->toArray();

        $this->Albums_->update($validation);

        $this->Albums_->podcast_album_links()->forceDelete();
        $this->Albums_->podcast_album_links()->createMany($validation_album_links);

        $this->Albums_->files()->where('model_column', 'image')->update([
            'file_id' => $image ?? null,
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

    public function albumDelete(PodcastAlbum $album): void
    {
        $this->Albums_ = $album;
        $this->dispatch('show_album_delete');
    }

    public function albumDeleteConfirm(): void
    {
        $this->Albums_->delete();
        $this->Albums_->podcast_album_links()->forceDelete();
        $this->Albums_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_album_delete');
    }

    public function addAlbumLinks():void
    {
        $this->album_links []=[
            'title' => '',
            'url' => '',
            'icon_id' => ''
        ];
    }

    public function removeAlbumLinks($index):void
    {
        unset($this->album_links[$index]);
    }

    #[Computed]
    public function icons()
    {
        return  Icon::get();
    }

    public function iconSelect($key , Icon $icon): void
    {
        $this->album_links[$key]['icon_id'] = $icon->id;

    }
}
