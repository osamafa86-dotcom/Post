<?php

namespace App\Livewire\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Enums\VideoTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\MaterialRelation;
use App\Models\Participant;
use App\Models\SortData;
use App\Models\Tag;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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

class  ListMaterials extends Component
{
    use WithPagination, WithFileUploads;
    use WhereIn;

    protected string $paginationTheme = 'bootstrap';
    public bool $selectAllColumns = true;
    public $perPage = 10;
    public ?string $search = "";
    public object $Materials_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public $selected = [];
    public $selectAll = false;
    public $delete_text;
    public array $columnVisibility = [
        'material_id' => true,
        'image' => true,
        'title' => true,
        'description' => true,
        'type' => true,
        'category' => true,
        'actions' => true,
        'material_album_name' => true,
        'tags' => false

    ];
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-materials');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function materials(): LengthAwarePaginator
    {
        return Material::select('*')
            ->when($this->search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->with(['category.relationable', 'categories.relationable', 'tags.relationable', 'presenters.relationable', 'guests.relationable', 'files.file','material_album'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function presenters(): array
    {
        return Participant::where('type', ParticipantTypeEnum::PRESENTERS->value)->select('id', 'name')->get()->toArray();
    }

    #[Computed]
    public function guests(): array
    {
        return Participant::where('type', ParticipantTypeEnum::GUESTS->value)->select('id', 'name')->get()->toArray();
    }

    #[Computed]
    public function categories(): array
    {
        $type = $this->state['type'] ?? null;

        if (!$type) {
            return [];
        }

        $typeMap = [];

        foreach (MaterialTypeEnum::cases() as $materialCase) {
            foreach (CategoryTypeEnum::cases() as $categoryCase) {
                if ($materialCase->name === $categoryCase->name) {
                    $typeMap[$materialCase->value] = $categoryCase->value;
                }
            }
        }

        $categoryType = $typeMap[$type] ?? null;

        if (!$categoryType) {
            return [];
        }

        return Category::query()
            ->select('id', 'category_title')
            ->where('category_type', $categoryType)
            ->get()
            ->toArray();
    }
    public function updatedSelectAllColumns($value)
    {
        foreach ($this->columnVisibility as $column => $visible) {
            $this->columnVisibility[$column] = $value;
        }

        // Save preferences
        if (auth()->check()) {
            auth()->user()->setPreference('materials_columns', $this->columnVisibility);
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
            auth()->user()->setPreference('materials_columns', $this->columnVisibility);
        }
    }
    #[Computed]
    public function tags(): array
    {
        return Tag::where('tag_type', TagTypeEnum::MATERIALS->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }

    #[Computed]
    public function albums(): Collection
    {
        return MaterialAlbum::select('id', 'name')->get();
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
        $this->dispatch('show_form');
    }

    #[On('imageSelected')]
    public function imageSelected($id, $column = null)
    {
        if (isset($column)) {
            $this->state[$column] = $id;
            $this->state[$column . '_name'] = Files::where('id', $id)->value('path');
        }
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

    function extractYoutubeVideoId($url): false|string
    {
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';

        if (preg_match($pattern, $url, $matches)) {
            return $matches[1]; // Video ID
        }
        return false;
    }

    function extractTelegramData($url): false|array
    {
        $pattern = '/(?:https?:\/\/)?(?:t\.me|telegram\.me)\/([\w+]+)\/?([\d\w+-]*)?/';

        if (preg_match($pattern, $url, $matches)) {
            return [
                'channel' => $matches[1] ?? null,
                'message_id' => $matches[2] ?? null
            ];
        }
        return false;
    }

    public function updatedState($value, $key): void
    {
        if ($key == 'video_type' && $value != VideoTypeEnum::LOCAL->value) {
            $this->state['link'] = null;
        }
    }

    public function updatedStateType(): void
    {
        $this->state['category_id'] = null;
        // جهز البيانات التي تريد إرسالها
        $categories = array_map(function ($cat) {
            return [
                'id' => $cat['id'],
                'text' => $cat['category_title']
            ];
        }, $this->categories());

        $this->state['file_name'] = null;
        // أرسل الحدث للواجهة مع التصنيفات المحدثة
        $this->dispatch('updateCategorySelect', categories: $categories);
    }

    public function edit(Material $material): void
    {
        $material->load(['tags.relationable', 'guests.relationable', 'presenters.relationable', 'categories.relationable', 'files.file']);
        $this->reset();
        $this->state = [];
        $this->Materials_ = $material;
        $this->state = $material->toArray();
        $this->state['type'] = $material?->type;
        $tags = $this->Materials_->tags ? implode(',', $this->Materials_->tags?->pluck('relationable')?->pluck('tag_name')?->toArray()) : null;
        $this->state['tags'] = $tags;
        $this->state['guest_id'] = $this->Materials_?->guests?->pluck('relationable')?->pluck('id')->toArray();
        $this->state['presenter_id'] = $this->Materials_?->presenters?->pluck('relationable')?->pluck('id')->toArray();
        $this->state['category_id'] = $this->Materials_?->categories?->pluck('relationable')?->pluck('id')->toArray();
        if (isset($this->Materials_->files)) {
            $this->state['image'] = $this->Materials_->files?->where('model_column', 'image')?->first()?->file_id;
            $this->state['image_name'] = $this->Materials_->files?->where('model_column', 'image')?->first()?->file?->path;

            $this->state['file'] = $this->Materials_->files?->where('model_column', 'file')?->first()?->file_id;
            $this->state['file_name'] = $this->Materials_->files?->where('model_column', 'file')?->first()?->file?->path;
        }
        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    public function getYoutubeEmbedUrl($url): ?string
    {
        $videoId = $this->extractYoutubeVideoId($url);
        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
    }

    public function getTelegramEmbedUrl($url): ?string
    {
        $data = $this->extractTelegramData($url);
        return $data ? "https://t.me/{$data['channel']}/{$data['message_id']}" : null;
    }

    /**
     * @throws ValidationException
     */
    public function create(): void
    {
        $video_type_role = "nullable";
        if (!empty($this->state['type']) && $this->state['type'] == MaterialTypeEnum::VIDEO->value) {
            $video_type_role = "required|in:" . $this->whereIn()['VideoTypeEnum'];
        }
        $link_role = "nullable";
        if (!empty($this->state['type']) && !empty($this->state['video_type'])) {
            if ($this->state['video_type'] == VideoTypeEnum::YOUTUBE->value) {
                $link_role = [
                    'required',
                    'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/)[a-zA-Z0-9_-]{11}/',
                    function ($attribute, $value, $fail) {
                        $videoId = $this->extractYoutubeVideoId($value);
                        if (!$videoId) {
                            $fail('Invalid YouTube URL.');
                        }
                    }
                ];
            } elseif ($this->state['video_type'] == VideoTypeEnum::TELEGRAM->value) {
                $link_role = [
                    'required',
                    'regex:/^(https?:\/\/)?(t\.me|telegram\.me)\/([\w+]+)\/?([\d\w+-]*)?$/',
                    function ($attribute, $value, $fail) {
                        $data = $this->extractTelegramData($value);
                        if (!$data) {
                            $fail('Invalid Telegram URL.');
                        }
                    }
                ];
            }
        }

        if (!empty($this->state['type']) && $this->state['type'] == MaterialTypeEnum::PODCAST->value) {
            $rule_album = 'required|in:' . $this->whereIn()['MaterialAlbum'];
        } else {
            $rule_album = 'nullable|in:' . $this->whereIn()['MaterialAlbum'];
        }


        $validation = Validator::make($this->state, [
            'image' => 'required|integer|exists:files,id',
            'file' => 'nullable|integer|exists:files,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:' . $this->whereIn()['MaterialTypeEnum'],
            'video_type' => $video_type_role,
            'link' => $link_role,
            'tags' => 'required|string',
            'category_id' => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'album_id' => $rule_album,
            'presenter_id' => 'nullable|array',
            'presenter_id.*' => 'nullable|exists:participants,id',
            'guest_id' => 'nullable|array',
            'guest_id.*' => 'nullable|exists:participants,id',
        ])->validate();


        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $file = $validation['file'] ?? null;
        unset($validation['file']);

        $link = $validation['link'] ?? null;

        if (!empty($validation['video_type'])) {
            if ($validation['video_type'] == VideoTypeEnum::YOUTUBE->value) {
                $link = $this->getYoutubeEmbedUrl($link);
            } elseif ($validation['video_type'] == VideoTypeEnum::TELEGRAM->value) {
                $link = $this->getTelegramEmbedUrl($link);
            }
        }

        $material = Material::query()->create([
            'title' => $validation['title'],
            'type' => $validation['type'],
            'video_type' => $validation['video_type'] ?? null,
            'link' => $link ?? null,
            'description' => $validation['description'] ?? null,
            'album_id' => $validation['album_id'] ?? null,
            'publish_status' => PublishEnum::PUBLISHED->value,
        ]);


        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::MATERIALS->value]);
                MaterialRelation::create([
                    'material_id' => $material->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }

        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                MaterialRelation::create([
                    'material_id' => $material->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }

        if (!empty($validation['presenter_id'])) {
            foreach ($validation['presenter_id'] as $key => $row)
                MaterialRelation::create([
                    'material_id' => $material->id,
                    'relationable_id' => $row,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }
        if (!empty($validation['guest_id'])) {
            foreach ($validation['guest_id'] as $key => $row)
                MaterialRelation::create([
                    'material_id' => $material->id,
                    'relationable_id' => $row,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }

        $material->files()->create([
            'file_id' => $image ?? null,
            'model_type' => Material::class,
            'model_column' => 'image',
        ]);
        if ($file)
            $material->files()->create([
                'file_id' => $file ?? null,
                'model_type' => Material::class,
                'model_column' => 'file',
            ]);

        $material->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);

        $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
        $material->sortable()->create([
            'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        $video_type_role = "nullable";
        if (!empty($this->state['type']) && $this->state['type'] == MaterialTypeEnum::VIDEO->value) {
            $video_type_role = "required|in:" . $this->whereIn()['VideoTypeEnum'];
        }
        $link_role = "nullable";
        if (!empty($this->state['type']) && !empty($this->state['video_type'])) {
            if ($this->state['video_type'] == VideoTypeEnum::YOUTUBE->value) {
                $link_role = [
                    'required',
                    'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/)[a-zA-Z0-9_-]{11}/',
                    function ($attribute, $value, $fail) {
                        $videoId = $this->extractYoutubeVideoId($value);
                        if (!$videoId) {
                            $fail('Invalid YouTube URL.');
                        }
                    }
                ];
            } elseif ($this->state['video_type'] == VideoTypeEnum::TELEGRAM->value) {
                $link_role = [
                    'required',
                    'regex:/^(https?:\/\/)?(t\.me|telegram\.me)\/([\w+]+)\/?([\d\w+-]*)?$/',
                    function ($attribute, $value, $fail) {
                        $data = $this->extractTelegramData($value);
                        if (!$data) {
                            $fail(__('invalid_telegram_url'));
                        }
                    }
                ];
            }
        }
        $validation = Validator::make($this->state, [
            'image' => 'required|integer|exists:files,id',
            'file' => 'nullable|integer|exists:files,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:' . $this->whereIn()['MaterialTypeEnum'],
            'video_type' => $video_type_role,
            'link' => $link_role,
            'tags' => 'required|string',
            'category_id' => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'album_id' => 'nullable|in:' . $this->whereIn()['MaterialAlbum'],
            'presenter_id' => 'nullable|array',
            'presenter_id.*' => 'nullable|exists:participants,id',
            'guest_id' => 'nullable|array',
            'guest_id.*' => 'nullable|exists:participants,id',
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $file = $validation['file'] ?? null;
        unset($validation['file']);

        $link = $validation['link'] ?? null;

        if (!empty($validation['video_type'])) {
            if ($validation['video_type'] == VideoTypeEnum::YOUTUBE->value) {
                $link = $this->getYoutubeEmbedUrl($link);
            } elseif ($validation['video_type'] == VideoTypeEnum::TELEGRAM->value) {
                $link = $this->getTelegramEmbedUrl($link);
            }
        }

        $this->Materials_->update([
            'title' => $validation['title'],
            'type' => $validation['type'],
            'video_type' => $validation['video_type'] ?? null,
            'link' => $link ?? null,
            'description' => $validation['description'] ?? null,
            'album_id' => $validation['album_id'] ?? null,
        ]);
        $this->Materials_->tags()->forceDelete();
        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::MATERIALS->value]);
                MaterialRelation::create([
                    'material_id' => $this->Materials_->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        $this->Materials_->categories()->forceDelete();
        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                MaterialRelation::create([
                    'material_id' => $this->Materials_->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }
        $this->Materials_->presenters()->forceDelete();
        if (!empty($validation['presenter_id'])) {
            foreach ($validation['presenter_id'] as $key => $row)
                MaterialRelation::create([
                    'material_id' => $this->Materials_->id,
                    'relationable_id' => $row,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }
        $this->Materials_->guests()->forceDelete();
        if (!empty($validation['guest_id'])) {
            foreach ($validation['guest_id'] as $key => $row)
                MaterialRelation::create([
                    'material_id' => $this->Materials_->id,
                    'relationable_id' => $row,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }

        $this->Materials_->files()->updateOrCreate(
            ['model_column' => 'image'],
            ['file_id' => $image ?? null, 'model_type' => Material::class]
        );
        if ($file)
            $this->Materials_->files()->updateOrCreate(
                ['model_column' => 'file'],
                ['file_id' => $file ?? null, 'model_type' => Material::class]
            );
        $this->Materials_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(Material $material): void
    {
        $this->Materials_ = $material;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        MaterialRelation::where('material_id', $this->Materials_->id)->delete();
        $this->Materials_->delete();
        $this->Materials_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
        $this->resetSelection();
    }

    //    public function updateCategory($data)
    //    {
    //        $this->state['category_id'] = $data['category_id'];
    //    }
    //
    //    public function updateTags($data)
    //    {
    //        $this->state['tags'] = implode(',', $data['tags']);
    //    }
    //
    //// استدعاء عند فتح مودال اختيار الصورة
    //    public function openImageModal()
    //    {
    //        $this->dispatch('image_modal_opened');
    //    }
    //
    //// استدعاء عند إغلاق مودال اختيار الصورة
    //    public function closeImageModal()
    //    {
    //        $this->dispatch('image_modal_closed');
    //    }

    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->materials->items())->pluck('id')->all();
        if ($value) {
            $this->selected = array_unique(array_merge($this->selected, $Ids));
        } else {
            $this->selected = array_diff($this->selected, $Ids);
        }
        $this->checkSelectAll();
    }


    public function updatedSelected(): void
    {
        $this->checkSelectAll();
    }


    public function updatedPage(): void
    {
        $this->selectAll = false;
        $this->checkSelectAll();
    }

    private function checkSelectAll(): void
    {
        $Ids = collect($this->materials->items())->pluck('id')->all();
        $this->selectAll = !array_diff($Ids, $this->selected);
    }

    public function deleteSelected(): void
    {
        if (!empty($this->selected)) {
            $this->dispatch('show_delete_selected_modal');
        } else {
            $this->dispatch('no-data');
        }
    }

    public function confirmDeleteSelected(): void
    {

        if ($this->delete_text) {
            if ($this->delete_text == 'Delete') {

                $items = Material::whereIn('id', $this->selected)->get();

                $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

                foreach ($items as $item) {
                    $item->delete();

                    $item->user_logs()->create([
                        'user_id' => Auth::id(),
                        'action_status' => \App\Enums\ActionsEnum::DELETE->value,
                    ]);
                }


                $this->dispatch('hide_delete_selected_modal');
                $this->resetPage();
                $this->resetSelection();
                  } else {
                session()->flash('error',__('messages.error_delete_text'));
            }
        } else {
            session()->flash('error',__('messages.empty_delete_text') );
        }



    }
    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selected = [];
    }
}
