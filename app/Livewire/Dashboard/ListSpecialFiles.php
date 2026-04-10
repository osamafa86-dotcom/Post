<?php

namespace App\Livewire\Dashboard;

use App\Models\Files;
use App\Models\SpecialFile;
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
use Livewire\WithPagination;

class ListSpecialFiles extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $SpecialFiles_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-special-files');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function special_files(): LengthAwarePaginator
    {
        return SpecialFile::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('file_name', 'like', '%' . $this->search . '%');
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
        $this->dispatch('show_special_file_form');
    }

    public function edit(SpecialFile $special_file): void
    {
        $this->state = [];
        $this->SpecialFiles_ = $special_file;
        $this->state = $special_file->toArray();
        if (isset($this->SpecialFiles_->files->file_id)) {
            $this->state['file_image'] = $this->SpecialFiles_->files->file_id;
            $this->state['file_image_name'] = Files::where('id', $this->SpecialFiles_->files->file_id)->value('path');
        }
        $this->showEdit = true;
        $this->dispatch('show_special_file_form');
    }

    /**
     * @throws ValidationException
     */
    public function createSpecialFile(): void
    {
        $validation = Validator::make($this->state, [
            'file_name' => 'required|string|max:100|unique:special_files,file_name,null,id,deleted_at,NULL',
            'file_description' => 'required|string|max:255',
            'file_image' => 'required|integer|exists:files,id',
        ])->validate();

        $file_image = $validation['file_image'] ?? null;
        unset($validation['file_image']);
        $SpecialFile = SpecialFile::query()->create($validation);
        $SpecialFile->files()->create([
            'file_id' => $file_image ?? null,
            'model_type' => SpecialFile::class,
            'model_column' => 'file_image',
        ]);

        $SpecialFile->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => 'اضافة',
        ]);
        $this->dispatch('hide_special_file_form', ['message' => __('validation.saved_success')]);
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['file_image'] = $id;
        $this->state['file_image_name'] = Files::where('id', $id)->value('path');
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
    public function updateSpecialFile(): void
    {
        $validation = Validator::make($this->state, [
            'file_name' => 'required|string|max:100|unique:special_files,file_name,' . $this->SpecialFiles_->id . ',id,deleted_at,NULL',
            'file_description' => 'required|string|max:255',
            'file_image' => 'required|integer|exists:files,id',
        ])->validate();

        $before_data = $this->SpecialFiles_->toArray();

        $file_image = $validation['file_image'] ?? null;
        unset($validation['file_image']);
        $this->SpecialFiles_->update($validation);
        $this->SpecialFiles_->files()->updateOrCreate([
            'file_id' => $file_image ?? null,
        ], ['model_type' => SpecialFile::class, 'model_column' => 'file_image']);

        $new_data = $this->SpecialFiles_->toArray();

        $this->SpecialFiles_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_special_file_form', ['message' => __('validation.edit_success')]);
    }

    public function special_fileDelete(SpecialFile $special_file): void
    {
        $this->SpecialFiles_ = $special_file;
        $this->dispatch('show_special_file_delete');
    }

    public function special_fileDeleteConfirm(): void
    {
        $this->SpecialFiles_->delete();
        $this->SpecialFiles_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_special_file_delete');
    }
}
