<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserStatusEnum;
use App\Models\Team;
use App\Models\User;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ListUser extends Component
{
    use WithPagination, WhereIn;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Users_;
    public array $state = [];
    public string $user_status = "الكل";
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-user');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        } else {
            $this->search = $add;
        }
    }

    #[Computed]
    public function teams()
    {
        return Team::all();
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
//            ->where('user_status','<>', UserStatusEnum::VOICEOVER->value)
//            ->when($this->user_status != "الكل", function ($query) {
//                $query->where('user_status', $this->user_status);
//            })
            ->where(function ($query) {
                $query->where('email', 'like', '%' . $this->search . '%')
                    ->orWhere(function ($q) {
                        $name = explode(' ', $this->search);
                        foreach ($name as $n) {
                            $q->orWhere('first_name', 'like', '%' . $n . '%')
                                ->orWhere('last_name', 'like', '%' . $n . '%');
                        }
                    });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->with('roles')
            ->paginate(10);
    }

    #[Computed]
    public function roles(): Collection
    {
        return Role::all();
    }

    #[Computed]
    public function allUsers(): Collection
    {
        return User::select('id', 'first_name', 'last_name')->get();
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
        $this->dispatch('show_user_form');
    }

    public function edit(User $user): void
    {
        $this->state = [];
        $this->Users_ = $user;
        $this->state = $user->toArray();
        $this->state['role_id'] = $user?->roles?->first()?->id;
        $this->showEdit = true;
        $this->dispatch('show_user_form');
    }

    /**
     * @throws ValidationException
     */
    public function createUser(): void
    {
        $validation = Validator::make($this->state, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => 'required|exists:roles,id',
//            'team_id' => 'required|integer',
        ])->validate();

        $validation['password'] = Hash::make($validation['password']);

        $user = User::query()->create([
            'first_name' => $validation['first_name'],
            'last_name' => $validation['last_name'],
            'email' => $validation['email'],
            'password' => $validation['password'],
            'manager_id' => null,
            'user_status' => UserStatusEnum::ADMIN->value,
//            'team_id' => $validation['team_id'],
        ]);

        $role = Role::where('id', $validation['role_id'])->first();
        $user->assignRole($role);
        $user->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_user_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateUser(): void
    {
        $validation = Validator::make($this->state, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->state['id']),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'manager_id' => 'nullable|exists:roles,id',
        ])->validate();

        $before_data = $this->Users_->toArray();

        if (isset($validation['password'])) {
            $validation['password'] = Hash::make($validation['password']);
            $this->Users_->update([
                'first_name' => $validation['first_name'],
                'last_name' => $validation['last_name'],
                'email' => $validation['email'],
                'password' => $validation['password'],
            ]);
        } else {
            $this->Users_->update([
                'first_name' => $validation['first_name'],
                'last_name' => $validation['last_name'],
                'email' => $validation['email'],
            ]);
        }
        $role = Role::find($validation['role_id']);
        $this->Users_->syncRoles([$role]);

        $new_data = $this->Users_->toArray();

        $this->Users_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data),
        ]);
        $this->dispatch('hide_user_form', ['message' => __('validation.edit_success')]);
    }

    public function userDelete(User $user): void
    {
        $this->Users_ = $user;
        $this->dispatch('show_user_delete');
    }

    public function userDeleteConfirm(): void
    {
        $this->Users_->delete();
        $this->Users_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_user_delete');
    }
}
