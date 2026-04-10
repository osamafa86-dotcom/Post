<?php

namespace App\Livewire\Dashboard;


use App\Models\Files;
use App\Models\Service;
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

class ListServices extends Component
{
    use WithPagination,WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Service_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-services');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }

    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['service_image'] = $id;
        $this->state['service_image_name'] = Files::where('id', $id)->value('path');
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

    #[Computed]
    public function services(): LengthAwarePaginator
    {
        return Service::select('*')
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

    public function edit(Service $service): void
    {
        $this->state = [];
        $this->Service_ = $service;
        $this->state = $service->toArray();
        if (isset($this->Service_->files->file_id)) {
            $this->state['service_image'] = $this->Service_->files->file_id;
            $this->state['service_image_name'] = Files::where('id', $this->Service_->files->file_id)->value('path');
        }
        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    /**
     * @throws ValidationException
     */
    public function create(): void
    {


        $validation = Validator::make($this->state, [
            'service_image' => 'required|integer|exists:files,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:455',
            'button_title' => 'required|string|max:255',
            'url' => 'required|url',
            'is_show' => 'nullable|boolean',
        ])->validate();

        $image = $validation['service_image'] ?? null;
        unset($validation['service_image']);

        $service = Service::query()->create($validation);

        if (!empty($image))
            $service?->files()->create([
                'file_id' => $image ?? null,
                'model_type' => Service::class,
                'model_column' => 'service_image',
            ]);
        $service->user_logs()->create([
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
            'service_image' => 'required|integer|exists:files,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:455',
            'button_title' => 'required|string|max:255',
            'url' => 'required|url',
            'is_show' => 'nullable|boolean',
        ])->validate();

        $image = $validation['service_image'] ?? null;
        unset($validation['service_image']);

        $this->Service_->update($validation);

        $this->Service_->files()->update([
            'file_id' => $image ?? null,
        ]);

        $this->Service_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(Service $service): void
    {
        $this->Service_ = $service;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->Service_->delete();
        $this->Service_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
    }
}
