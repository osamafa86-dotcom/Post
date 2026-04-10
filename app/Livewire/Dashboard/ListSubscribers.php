<?php

namespace App\Livewire\Dashboard;


use App\Models\Files;
use App\Models\Service;
use App\Models\Subscriber;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListSubscribers extends Component
{
    use WithPagination,WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Subscriber_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-subscribers');
    }

    public function mount($add = null): void
    {

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

    #[Computed]
    public function subscribers(): LengthAwarePaginator
    {
        return Subscriber::select('*')
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

    public function edit(Subscriber $subscriber): void
    {
        $this->state = [];
        $this->Subscriber_ = $subscriber;
        $this->state = $subscriber->toArray();
        if (isset($this->Subscriber_->files->file_id)) {
            $this->state['image'] = $this->Subscriber_->files->file_id;
            $this->state['image_name'] = Files::where('id', $this->Subscriber_->files->file_id)->value('path');
        }
        $this->state['password'] = '';
        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    /**
     * @throws ValidationException
     */
    public function create(): void
    {

        $validation = Validator::make($this->state, [
            'image' => 'nullable|integer|exists:files,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:subscribers',
            'password' => 'required|max:255',
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $validation['password'] = Hash::make($validation['password']);

        $subscriber = Subscriber::query()->create($validation);

        if (!empty($image))
            $subscriber?->files()->create([
                'file_id' => $image ?? null,
                'model_type' => Subscriber::class,
                'model_column' => 'image',
            ]);
        $subscriber->user_logs()->create([
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('subscribers', 'email')->ignore($this->state['id']),
            ],
            'password' => 'nullable|max:255',
        ])->validate();

        if(!Empty($validation['password']))
        {
            $validation['password'] = Hash::make($validation['password']);
        }else{
            unset($validation['password']);
        }



        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $this->Subscriber_->update($validation);

        $this->Subscriber_->files()->update([
            'file_id' => $image ?? null,
        ]);

        $this->Subscriber_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(Subscriber $subscriber): void
    {
        $this->Subscriber_ = $subscriber;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->Subscriber_->delete();
        $this->Subscriber_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
    }
}
