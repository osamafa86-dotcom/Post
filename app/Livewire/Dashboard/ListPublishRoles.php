<?php

namespace App\Livewire\Dashboard;

use App\Models\Category;
use App\Models\PublishRole;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;

class ListPublishRoles extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';
    public $state = [];
    public $showEdit = false;

    public ?string $search = "";
    public string $sortField = "id";
    public string $sortDirection = "asc";
    public object $PublishRole_;
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $roles = Role::all();
        $users = PublishRole::with('from_role', 'to_role')->get();

        return view('livewire.dashboard.list-publish-roles', compact('users', 'roles'));
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_publish_role_form');
    }

    public function createPublishRole(): void
    {
        // Validate the incoming data

        $validation = Validator::make($this->state, [
            'fromId' => 'required|exists:roles,id',  // Ensure 'fromId' exists in the 'roles' table
            'toId' => 'required|exists:roles,id',    // Ensure 'toId' exists in the 'roles' table
        ])->validate();

        // Create the PublishRole entry in the database
        PublishRole::create([
            'from_role_id' => $validation['fromId'],  // Using validated 'fromId'
            'to_role_id' => $validation['toId'],      // Using validated 'toId'
        ]);

        // Optionally, you can dispatch a success message
        session()->flash('message', __('validation.publish_role_created'));

        // Dispatch an event to hide the modal after creating the Publish Role
        $this->dispatch('hide_publish_role_form');
    }


    public function edit(PublishRole $publishRole): void
    {
        $this->showEdit = true;
        $this->PublishRole_ = $publishRole;

        // Bind the correct role IDs to the form
        $this->state = [
            'fromId' => $publishRole->from_role_id,
            'toId' => $publishRole->to_role_id,
        ];

        $this->dispatch('show_publish_role_form'); // Trigger modal opening
    }


    public function updatePublishRole(): void
    {
        $validatedData = $this->validate([
            'state.fromId' => 'required|exists:roles,id', // Ensure the selected 'from' role exists
            'state.toId' => 'required|exists:roles,id',   // Ensure the selected 'to' role exists
        ]);

        // Update the PublishRole model
        $this->PublishRole_->update([
            'from_role_id' => $validatedData['state']['fromId'],
            'to_role_id' => $validatedData['state']['toId'],
        ]);

        // Optional: Close the modal and refresh the table
        $this->dispatch('hide_publish_role_form'); // Trigger modal closing
        session()->flash('message', __('messages.publish_role_updated'));
    }




    public function delete(PublishRole $publishRole): void
    {
        // Delete the specified PublishRole
        $publishRole->delete();

        // Optional: Flash a success message
        session()->flash('message', __('messages.publish_role_deleted'));

        // Optional: Trigger a modal close or refresh logic, if applicable
        $this->dispatch('hide_publish_role_delete_modal');
    }

    public function publishRoleDelete(PublishRole $publishRole): void
    {
        $this->PublishRole_ = $publishRole;
        $this->dispatch('show_publish_role_delete'); // Trigger the modal
    }

    public function publishRoleDeleteConfirm(): void
    {
        // Delete the selected PublishRole
        $this->PublishRole_->delete();

        // Flash success message
        session()->flash('message', __('messages.publish_role_deleted'));

        // Close the modal
        $this->dispatch('hide_publish_role_delete');
    }



}


//
//    #[Computed]
//    public function alerts(): LengthAwarePaginator
//    {
//        return Alert::where('status', false)
//            ->where(function ($q) {
//                $q->when(isset($this->search), function ($query) {
//                    $query->where('title', 'like', '%' . $this->search . '%')
//                        ->orWhere('type', 'like', '%' . $this->search . '%');
//                });
//            })
//            ->orderBy($this->sortField, $this->sortDirection)
//            ->paginate(10);
//    }
//
//    public function sortBy($field): void
//    {
//        $this->sortDirection = isset($this->sortDirection) && $this->sortDirection == 'asc' ? 'desc' : 'asc';
//        $this->sortField = $field;
//    }
//
//    public function updatedSearch(): void
//    {
//        $this->resetPage();
//    }
//
//    public function alertsStatus(Alert $alerts): void
//    {
//        $this->Alerts_ = $alerts;
//        $this->dispatch('show_alerts_status');
//    }
//
//    public function alertsStatusConfirm(): void
//    {
//        $this->Alerts_->update(['status' => true]);
//        $this->Alerts_->user_logs()->create([
//            'user_id' => Auth::id(),
//            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
//        ]);
//        $this->dispatch('hide_alerts_status');
//    }
//
//    public function alertsDelete(Alert $alerts): void
//    {
//        $this->Alerts_ = $alerts;
//        $this->dispatch('show_alerts_delete');
//    }
//
//    public function alertsDeleteConfirm(): void
//    {
//        $this->Alerts_->delete();
//        $this->Alerts_->user_logs()->create([
//            'user_id' => Auth::id(),
//            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
//        ]);
//        $this->dispatch('hide_alerts_delete');
