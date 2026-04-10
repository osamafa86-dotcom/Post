<?php

namespace App\Livewire\Dashboard;


use App\Enums\NotificationTypeEnum;
use App\Models\Files;
use App\Models\Service;
use App\Models\SubscriberNotification;
use App\Traits\WhereIn;
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

class ListSubscriberNotifications extends Component
{
    use WithPagination,WithFileUploads, WhereIn;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $SubscriberNotification_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-subscriber-notifications');
    }

    public function mount(): void
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
    public function subscriber_notifications(): LengthAwarePaginator
    {
        return SubscriberNotification::select('*')
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

    public function edit(SubscriberNotification $subscriberNotification): void
    {
        $this->state = [];
        $this->SubscriberNotification_ = $subscriberNotification;
        $this->state = $subscriberNotification->toArray();
        if (isset($this->SubscriberNotification_->files->file_id)) {
            $this->state['image'] = $this->SubscriberNotification_->files->file_id;
            $this->state['image_name'] = Files::where('id', $this->SubscriberNotification_->files->file_id)->value('path');
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
            'image' => 'required|integer|exists:files,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:455',
            'date' => 'required|date|max:255',
            'url' => 'required|url',
            'type' => 'required|in:'.$this->whereIn()['NotificationType'],
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $subscriberNotification = SubscriberNotification::query()->create($validation);

        if (!empty($image))
            $subscriberNotification?->files()->create([
                'file_id' => $image ?? null,
                'model_type' => SubscriberNotification::class,
                'model_column' => 'image',
            ]);
        $subscriberNotification->user_logs()->create([
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:455',
            'date' => 'required|date|max:255',
            'url' => 'required|url',
            'type' => 'required|in:'.$this->whereIn()['NotificationType'],
        ])->validate();

        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $this->SubscriberNotification_->update($validation);

        $this->SubscriberNotification_->files()->update([
            'file_id' => $image ?? null,
        ]);

        $this->SubscriberNotification_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(SubscriberNotification $subscriberNotification): void
    {
        $this->SubscriberNotification_ = $subscriberNotification;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->SubscriberNotification_->delete();
        $this->SubscriberNotification_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
    }
}
