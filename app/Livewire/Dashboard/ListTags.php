<?php

namespace App\Livewire\Dashboard;

use App\Models\Category;
use App\Models\MaterialRelation;
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
use Livewire\Component;
use Livewire\WithPagination;
use App\Enums\TagTypeEnum;

class ListTags extends Component
{
    use WithPagination , WhereIn;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public $selected = [];
    public $selectAll = false;
    public $delete_text;
    public object $Tags_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-tags');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function tags(): LengthAwarePaginator
    {
        return Tag::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('tag_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[Computed]
    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'bi-arrow-down-up';
        }

        return $this->sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_tag_form');
    }

    public function edit(Tag $tag): void
    {
        $this->state = [];
        $this->Tags_ = $tag;
        $this->state = $tag->toArray();
        $this->showEdit = true;
        $this->dispatch('show_tag_form');
    }

    /**
     * @throws ValidationException
     */
    public function createTag(): void
    {
        $validation = Validator::make($this->state, [
            'tag_name' => 'required|string|max:100|unique:tags,tag_name',
            'tag_type' => 'required|in:' . $this->whereIn()['TagTypeEnum'],
        ])->validate();

        $Tag = Tag::query()->create($validation);

        $this->dispatch('hide_tag_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateTag(): void
    {
        $validation = Validator::make($this->state, [
            'tag_name' => 'required|string|max:100',
            'tag_type' => 'required|in:' . $this->whereIn()['TagTypeEnum'],
        ])->validate();

        $before_data = $this->Tags_->toArray();

        $this->Tags_->update($validation);

        $new_data = $this->Tags_->toArray();


        $this->dispatch('hide_tag_form', ['message' => __('validation.edit_success')]);
    }

    public function tagDelete(Tag $tag): void
    {
        $this->Tags_ = $tag;
        $this->dispatch('show_tag_delete');
    }

    public function tagDeleteConfirm(): void
    {
        PostRelation::where([['relationable_id',$this->Tags_->id],['relationable_type',Tag::class]])->delete();
        MaterialRelation::where([['relationable_id',$this->Tags_->id],['relationable_type',Tag::class]])->delete();
        $this->Tags_->delete();

        $this->dispatch('hide_tag_delete');
        $this->resetSelection();

    }

    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->tags->items())->pluck('id')->all();
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
        $Ids = collect($this->tags->items())->pluck('id')->all();
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
                $items = Tag::whereIn('id', $this->selected)->get();

                $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

                foreach ($items as $item) {
                    PostRelation::where([['relationable_id',$item->id],['relationable_type',Tag::class]])->delete();
                    MaterialRelation::where([['relationable_id',$item->id],['relationable_type',Tag::class]])->delete();
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
