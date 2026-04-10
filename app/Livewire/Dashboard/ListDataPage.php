<?php

namespace App\Livewire\Dashboard;

use App\Enums\DataPageEnum;
use App\Models\Files;
use App\Models\Icon;
use App\Models\ImmigrantData;
use App\Models\Participant;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListDataPage extends Component
{
    use WithPagination, WhereIn, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Data_;
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
        return view('livewire.dashboard.list-data-page');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function datas(): LengthAwarePaginator
    {
        return ImmigrantData::when(isset($this->search), function (Builder $query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->when(!config('features.general.scholarships'), function (Builder $query) {
                $query->where('type', '!=', DataPageEnum::SCHOLARSHIP->value);
            })
            ->orderBy($this->sortField, $this->sortDirection)
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
        $this->dispatch('show_form');
    }

    public function edit(ImmigrantData $data): void
    {
        $this->state = [];
        $this->Data_ = $data;
        $this->state = $data->toArray();

        if (isset($this->Data_->files->file_id)) {
            $this->state['image'] = $this->Data_->files->file_id;
            $this->state['image_name'] = Files::where('id', $this->Data_->files->file_id)->value('path');
        }
        $this->showEdit = true;
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

    /**
     * @throws ValidationException
     */
    public function create(): void
    {
        $validation = Validator::make($this->state, [
            'image' => 'required|integer|exists:files,id',
            'name' => 'required|string|max:100',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:2048',
            'code' => 'nullable|string',
            'opportunity' => 'nullable|string',
            'scholarship' => 'nullable|string',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:' . $this->whereIn()['DataPageEnum'],
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);

        if (!empty($this->state['file'])) {
            $validation['file'] = $this->state['file']->store('dataPage/files', config('filesystems.default'));
        }

        $data = ImmigrantData::create($validation);

        if (!empty($image)) {
            $data->files()->create([
                'file_id' => $image,
                'model_type' => ImmigrantData::class,
                'model_column' => 'image',
            ]);
        }

        $data->user_logs()->create([
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
            'image' => 'nullable|integer|exists:files,id',
            'name' => 'required|string|max:100',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:2048',
            'code' => 'nullable|string',
            'opportunity' => 'nullable|string',
            'scholarship' => 'nullable|string',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:' . $this->whereIn()['DataPageEnum'],
        ])->validate();

        $imageId = $validation['image'] ?? null;
        unset($validation['image']);

        $before_data = $this->Data_->toArray();
        if (!empty($this->state['file'])) {
            if ($this->Data_->file) {
                $newFileHash = md5_file($this->state['file']->getRealPath());
                $oldFileHash = md5_file(storage_path('app/' . $this->Data_->file));

                if ($newFileHash !== $oldFileHash) {
                    Storage::delete($this->Data_->file);
                    $validation['file'] = $this->state['file']->store('dataPage/files', config('filesystems.default'));
                } else {
                    $validation['file'] = $this->Data_->file;
                }
            } else {
                $validation['file'] = $this->state['file']->store('dataPage/files', config('filesystems.default'));
            }
        } else {
            $validation['file'] = $this->Data_->file;
        }
        $this->Data_->update($validation);

        $this->Data_->files()->where('model_column', 'image')->delete();
        if (!empty($imageId)) {
            $this->Data_->files()->create([
                'file_id' => $imageId,
                'model_type' => ImmigrantData::class,
                'model_column' => 'image',
            ]);
        }


        $new_data = $this->Data_->toArray();

        $this->Data_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data),
        ]);

        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(ImmigrantData $data): void
    {
        $this->Data_ = $data;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->Data_->delete();
        $this->Data_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
        $this->resetSelection();

    }


    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->participants->items())->pluck('id')->all();
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
        $Ids = collect($this->participants->items())->pluck('id')->all();
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
                $items = Participant::whereIn('id', $this->selected)->get();

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
                session()->flash('error', __('messages.error_delete_text'));
            }
        } else {
            session()->flash('error', __('messages.empty_delete_text'));
        }


    }

    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selected = [];
    }
}
