<?php

namespace App\Livewire\Dashboard;

use App\Models\Category;
use App\Models\Files;
use App\Models\MaterialRelation;
use App\Models\PendingAction;
use App\Models\PostRelation;
use App\Models\Tag;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListCategories extends Component
{
    use WithPagination, WhereIn, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';
    public $selected = [];
    public $selectAll = false;
    public $delete_text;
    public ?string $search = "";
    public object $Categories_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-categories');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function categories(): LengthAwarePaginator
    {
        return Category::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('category_title', 'like', '%' . $this->search . '%')
                    ->orWhere('category_description', 'like', '%' . $this->search . '%')
                    ->orWhere('category_type', 'like', '%' . $this->search . '%');
            })->with(['files.file','parent'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function parents()
    {
        return Category::select('category_title', 'id')->get();
    }

    public function sortBy($field): void
    {
        $this->sortDirection = isset($this->sortDirection) && $this->sortDirection == 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->selected = [];
        $this->selectAll = false;
        $this->delete_text = '';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_category_form');
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['category_image'] = $id;
        $this->state['category_image_name'] = Files::where('id', $id)->value('path');
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

    public function edit(Category $category): void
    {
        $this->state = [];
        $this->Categories_ = $category;
        $this->state = $category->toArray();
        if (isset($this->Categories_->files->file_id)) {
            $this->state['category_image'] = $this->Categories_->files->file_id;
            $this->state['category_image_name'] = Files::where('id', $this->Categories_->files->file_id)->value('path');
        }
        $this->showEdit = true;
        $this->dispatch('show_category_form');
    }

    /**
     * @throws ValidationException
     */
    public function createCategory(): void
    {
        $validation = Validator::make($this->state, [
            'category_title' => 'required|string|max:100|unique:categories,category_title,NULL,id,deleted_at,NULL',
            'category_image' => 'nullable|integer|exists:files,id',
            'category_description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|in:' . $this->whereIn()['Category'],
            'category_type' => 'required|in:' . $this->whereIn()['CategoryTypeEnum'],
            'category_style' => 'nullable|string',
            'category_color' => 'nullable|string|max:20',
        ])->validate();

        $category_image = $validation['category_image'] ?? null;
        unset($validation['category_image']);

        $category = Category::query()->create($validation);
        if (!empty($category_image))
            $category?->files()->create([
                'file_id' => $category_image ?? null,
                'model_type' => Category::class,
                'model_column' => 'category_image',
            ]);


        $this->dispatch('hide_category_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateCategory(): void
    {

        $validation = Validator::make($this->state, [
            'category_title' => 'required|string|max:100|unique:categories,category_title,' . $this->Categories_->id . ',id,deleted_at,NULL',
            'category_image' => 'nullable|integer|exists:files,id',
            'category_description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|in:' . $this->whereIn()['Category'],
            'category_type' => 'required|in:' . $this->whereIn()['CategoryTypeEnum'],
            'category_color' => 'nullable|string|max:20',
        ])->validate();

        $category_image = $validation['category_image'] ?? null;
        unset($validation['category_image']);

        $before_data = $this->Categories_->toArray();


        $this->Categories_->update($validation);
        $this->Categories_->files()->update([
            'file_id' => $category_image ?? null,
        ]);


        $new_data = $this->Categories_->toArray();


        $this->dispatch('hide_category_form', ['message' => __('validation.edit_success')]);
    }

    public function categoryDelete(Category $category): void
    {
        $this->Categories_ = $category;
        $this->dispatch('show_category_delete');
    }

    public function categoryDeleteConfirm(): void
    {
        PostRelation::where([['relationable_id',$this->Categories_->id],['relationable_type',Category::class]])->delete();
        MaterialRelation::where([['relationable_id',$this->Categories_->id],['relationable_type',Category::class]])->delete();
        $this->Categories_->delete();

        $this->dispatch('hide_category_delete');
        $this->resetSelection();

    }

    public function clearParent(): void
    {
        $this->state['parent_id'] = null;
    }

    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->categories->items())->pluck('id')->all();
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
        $Ids = collect($this->categories->items())->pluck('id')->all();
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
                $items = Category::whereIn('id', $this->selected)->get();

                $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

                foreach ($items as $item) {
                    PostRelation::where([['relationable_id',$item->id],['relationable_type',Category::class]])->delete();
                    MaterialRelation::where([['relationable_id',$item->id],['relationable_type',Category::class]])->delete();
                    $item->delete();


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
