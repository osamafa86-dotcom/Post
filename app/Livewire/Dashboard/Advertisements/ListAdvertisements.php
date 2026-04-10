<?php

namespace App\Livewire\Dashboard\Advertisements;

use App\Models\Advertisement;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListAdvertisements extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?object $Advertisement;
    public ?string $search = "";
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.advertisements.list-advertisements');
    }

    #[Computed]
    public function advertisements(): LengthAwarePaginator
    {
        return Advertisement::select('advertisements.*')
            ->join('users', 'users.id', '=', 'advertisements.user_id')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $search_name = explode(' ', $this->search);
                    foreach ($search_name as $name) {
                        $q->orWhere('users.first_name', 'like', '%' . $name . '%')
                            ->orWhere('users.last_name', 'like', '%' . $name . '%');
                    }
                })
                    ->orWhere('advertisements.title', 'like', '%' . $this->search . '%')
                    ->orWhere('advertisements.type', 'like', '%' . $this->search . '%')
                    ->orWhere('advertisements.place', 'like', '%' . $this->search . '%');
            })
            ->orderBy('advertisements.' . $this->sortField, $this->sortDirection)
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

    public function delete(Advertisement $advertisement): void
    {
        $this->Advertisement = $advertisement;
        $this->dispatch('show_delete_modal');
    }

    public function confirmDelete(): void
    {
        $this->Advertisement->delete();

        $this->Advertisement->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete_modal');
    }
}

