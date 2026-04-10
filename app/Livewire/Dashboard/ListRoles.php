<?php

namespace App\Livewire\Dashboard;

use App\Models\Role;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role as RoleModel;

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
use Livewire\Component;
use Livewire\WithPagination;

class ListRoles extends Component
{
    use WithPagination, WhereIn;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Roles_;
    public array $state = [];
    public array $targets = [];
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";

    public array $models = [
        'Post' => 'messages.models.Post',
        'BreakingNews' => 'messages.models.BreakingNews',
        'Category' => 'messages.models.Category',
        'Type' => 'messages.models.Type',
        'SpecialFile' => 'messages.models.SpecialFile',
        'Tag' => 'messages.models.Tag',
        'Quote' => 'messages.models.Quote',
        'SpecialPage' => 'messages.models.SpecialPage',
        'Advertisement' => 'messages.models.Advertisement',
        'Material' => 'messages.models.Material',
        'Participant' => 'messages.models.Participant',
        'LiveStream' => 'messages.models.LiveStream',
        'User' => 'messages.models.User',
        'Alert' => 'messages.models.Alert',
        'Setting' => 'messages.models.Setting',
        'ContactUs' => 'messages.models.ContactUs',
        'SendNews' => 'messages.models.SendNews',
    ];
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-roles');
    }

    public function mount($add = null): void
    {
        $this->targets = RoleModel::pluck('name', 'id')->toArray();

        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function roles(): LengthAwarePaginator
    {
        return Role::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('model', 'like', '%' . $this->search . '%')
                    ->orWhere('action', 'like', '%' . $this->search . '%');
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
        $this->dispatch('show_role_form');
    }

    public function edit(Role $role): void
    {
        $this->state = [];
        $this->Roles_ = $role;
        $this->state = $role->toArray();
        $this->showEdit = true;
        $this->dispatch('show_role_form');
    }

    /**
     * @throws ValidationException
     */
    public function createRole(): void
    {
        $validation = Validator::make($this->state, [
            'model' => 'required|string|max:50',
            'action' => 'required|string|max:10',
            'permission_target_id' =>  'required|in:' . $this->whereIn()['RoleModel'],
        ])->validate();

        Role::create($validation);

        $this->dispatch('hide_role_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateRole(): void
    {
        $validation = Validator::make($this->state, [
            'model' => 'required|string|max:50',
            'action' => 'required|string|max:10',
            'permission_target_id' =>  'required|in:' . $this->whereIn()['RoleModel'],
        ])->validate();

        $this->Roles_->update($validation);

        $this->dispatch('hide_role_form', ['message' => __('validation.edit_success')]);
    }

    public function roleDelete(Role $role): void
    {
        $this->Roles_ = $role;
        $this->dispatch('show_role_delete');
    }

    public function roleDeleteConfirm(): void
    {
        $this->Roles_->delete();
        $this->Roles_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_role_delete');
    }

}
