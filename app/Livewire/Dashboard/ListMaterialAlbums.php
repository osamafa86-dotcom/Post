<?php

namespace App\Livewire\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Icon;
use App\Models\MaterialAlbum;
use App\Models\MaterialAlbumRelation;
use App\Models\Tag;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListMaterialAlbums extends Component
{
    use WithPagination,WithFileUploads;
    use WhereIn;
    protected string $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public ?string $search = "";
    public object $MaterialAlbums_;
    public array $state = [];
    public bool $selectAllColumns = true;
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    public array $columnVisibility = [
        'album_id' => true,
        'image' => true,
        'name' => true,
        'description' => true,
        'type' => true,
        'category' => true,
        'actions' => true,
        'tags' => false
    ];
    public array $links = [];

    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-material-albums');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }

        if (auth()->check()) {
            $savedColumns = auth()->user()->getPreference('material_album_columns', []);
            if (!empty($savedColumns)) {
                $allowedColumns = array_keys($this->columnVisibility);
                $filteredSaved = array_intersect_key(
                    $savedColumns,
                    array_flip($allowedColumns)
                );
                $this->columnVisibility = array_merge($this->columnVisibility, $filteredSaved);
            }
        }
    }
    public function updatedSelectAllColumns($value)
    {
        foreach ($this->columnVisibility as $column => $visible) {
            $this->columnVisibility[$column] = $value;
        }

        // Save preferences
        if (auth()->check()) {
            auth()->user()->setPreference('materials_albums_columns', $this->columnVisibility);
        }
    }
    public function updatedColumnVisibility($value, $key)
    {
        if (!in_array(false, $this->columnVisibility)) {
            $this->selectAllColumns = true;
        } else {
            $this->selectAllColumns = false;
        }

        if (auth()->check()) {
            auth()->user()->setPreference('materials_albums_columns', $this->columnVisibility);
        }
    }


    #[Computed]
    public function materialAlbums(): LengthAwarePaginator
    {
        return MaterialAlbum::select('*')
            ->orderBy($this->sortField, $this->sortDirection)
            ->with(['files.file', 'categories', 'tags'])
            ->paginate($this->perPage);
    }


    #[Computed]
    public function categories()
    {
        return Category::select('id', 'category_title')->whereIn('category_type', [CategoryTypeEnum::PODCAST->value,CategoryTypeEnum::VIDEO->value ,CategoryTypeEnum::IMAGE->value ])->get();
    }
    #[Computed]
    public function tags(): array
    {
        return Tag::where('tag_type', TagTypeEnum::MATERIALS->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }



    public function sortBy($field): void
    {
        $this->sortDirection = isset($this->sortDirection) && $this->sortDirection == 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->links =[];
        $this->dispatch('show_form');
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['image'] = $id;
        $this->state['image_name'] = Files::where('id', $id)->value('path');
    }

    public function clearColumn($column, $value = null): void
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
    public function edit(MaterialAlbum $materialAlbum): void
    {
        $materialAlbum->load(['tags', 'categories', 'files.file', 'album_links']);
        $this->state = [];
        $this->MaterialAlbums_ = $materialAlbum;
        $this->state = $materialAlbum->toArray();
        $tags = $this->MaterialAlbums_->tags ? implode(',', $this->MaterialAlbums_->tags->pluck('tag_name')->toArray()) : null;
        $this->state['tags'] = $tags;
        $this->state['category_id'] = $this->MaterialAlbums_->categories->pluck('id')->toArray();

        if (isset($this->MaterialAlbums_->files->file_id)){
            $this->state['image'] = $this->MaterialAlbums_->files->file_id;
            $this->state['image_name'] = Files::where('id', $this->MaterialAlbums_->files->file_id)->value('path');
        }

        $this->links = $materialAlbum->album_links->toArray();
        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    /**
     * @throws ValidationException
     */
    public function create(): void
    {
        $validation = Validator::make($this->state, [
            'image' => 'required|integer|exists:files,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'tags' => 'required|string',
            'category_id' => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'type' => 'required|in:' . $this->whereIn()['MaterialTypeEnum'],
        ])->validate();


        $validation_links = Validator::make($this->links, [
            '*.title' => 'required|string|max:100',
            '*.url' => 'required|url',
            '*.icon_id' => 'nullable|integer|exists:icons,id',
        ],[
            '*.title.required' => __('validation.attributes.links_title'),
            '*.url.required' =>  __('validation.attributes.links_url'),
            '*.icon_id.required' => __('validation.attributes.links_icon'),
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);


        $materialAlbum = MaterialAlbum::query()->create([
            'name' => $validation['name'],
            'description' => $validation['description'] ?? null,
            'type' => $validation['type'],
        ]);

        $materialAlbum->album_links()->createMany($validation_links);

        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::MATERIALS->value]);
                MaterialAlbumRelation::create([
                    'material_album_id' => $materialAlbum->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }

        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                MaterialAlbumRelation::create([
                    'material_album_id' => $materialAlbum->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }


        $materialAlbum->files()->create([
            'file_id' => $image ?? null,
            'model_type' => MaterialAlbum::class,
            'model_column' => 'image',
        ]);
        $materialAlbum->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {

        $validation = Validator::make($this->state, [
            'image' => 'required|integer|exists:files,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'tags' => 'required|string',
            'category_id' => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'type' => 'required|in:' . $this->whereIn()['MaterialTypeEnum'],
        ])->validate();


        $validation_links = Validator::make($this->links, [
            '*.title' => 'required|string|max:100',
            '*.url' => 'required|url',
            '*.icon_id' => 'nullable|integer|exists:icons,id',
        ],[
            '*.title.required' => __('title_required'),
            '*.url.required' => __('url_required'),
            '*.icon_id.required' => __('icon_id_required'),
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);


        $this->MaterialAlbums_->update([
            'name' => $validation['name'],
            'description' => $validation['description'] ?? null,
            'type' => $validation['type'],
        ]);

        $this->MaterialAlbums_->album_links()->forceDelete();
        $this->MaterialAlbums_->album_links()->createMany($validation_links);

        $this->MaterialAlbums_->tags()->forceDelete();
        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::MATERIALS->value]);
                MaterialAlbumRelation::create([
                    'material_album_id' => $this->MaterialAlbums_->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        $this->MaterialAlbums_->categories()->forceDelete();
        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                MaterialAlbumRelation::create([
                    'material_album_id' => $this->MaterialAlbums_->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }

        $this->MaterialAlbums_->files()->update([
            'file_id' => $image ?? null,
        ]);
        $this->MaterialAlbums_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(MaterialAlbum $materialAlbum): void
    {
        $this->MaterialAlbums_ = $materialAlbum;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->MaterialAlbums_->delete();
        $this->MaterialAlbums_->album_links()->forceDelete();
        $this->MaterialAlbums_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
    }

    #[Computed]
    public function icons()
    {
        return  Icon::get();
    }
    public function iconSelect($key , Icon $icon): void
    {
        $this->links[$key]['icon_id'] = $icon->id;

    }
    public function addSocialMedia():void
    {
        $this->links []=[
            'title' => '',
            'url' => '',
            'icon_id' => ''
        ];
    }

    public function removeLink($index):void
    {
        unset($this->links[$index]);
    }
}
