<?php

namespace App\Livewire\Dashboard;

use App\Models\Tag;
use App\Models\Type;
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

class ListTypes extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Types_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public $selected = [];
    public $selectAll = false;
    public $delete_text;
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-types');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function types(): LengthAwarePaginator
    {
        return Type::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('types.type_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy( $this->sortField, $this->sortDirection)
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
        $this->dispatch('show_type_form');
    }

    public function edit(Type $type): void
    {
        $this->state = [];
        $this->Types_ = $type;
        $this->state = $type->toArray();
        $this->showEdit = true;
        $this->dispatch('show_type_form');
    }

    /**
     * @throws ValidationException
     */
    public function createType(): void
    {
        $validation = Validator::make($this->state, [
            'type_name' => 'required|string|max:100|unique:types',
        ])->validate();

        $Type = Type::query()->create($validation);
        $Type->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_type_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateType(): void
    {
        $validation = Validator::make($this->state, [
            'type_name' => 'required|string|max:100|unique:types,type_name,' . $this->Types_->id,
        ])->validate();

        $before_data = $this->Types_->toArray();

        $this->Types_->update($validation);

        $new_data = $this->Types_->toArray();

        $this->Types_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_type_form', ['message' => __('validation.edit_success')]);
    }

    public function typeDelete(Type $type): void
    {
        $this->Types_ = $type;
        $this->dispatch('show_type_delete');
    }

    public function typeDeleteConfirm(): void
    {
        $this->Types_->delete();
        $this->Types_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_type_delete');
        $this->resetSelection();

    }

    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->types->items())->pluck('id')->all();
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
        $Ids = collect($this->types->items())->pluck('id')->all();
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
                $items = Type::whereIn('id', $this->selected)->get();

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
