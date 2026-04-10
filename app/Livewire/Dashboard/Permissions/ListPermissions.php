<?php

namespace App\Livewire\Dashboard\Permissions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ListPermissions extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Roles_;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.permissions.list-permissions');
    }

    #[Computed]
    public function roles(): LengthAwarePaginator
    {
        return Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
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

    public function roleDelete(Role $role): void
    {
        $this->Roles_ = $role;
        $this->dispatch('show_role_delete');
    }

    public function roleDeleteConfirm(): void
    {
        $this->Roles_->delete();
        $this->dispatch('hide_role_delete');
    }
}
