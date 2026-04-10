<?php

namespace App\Livewire\Dashboard\SpecialPages;

use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListSpecialPages extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?object $SpecialPage;
    public ?string $search = "";
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.special-pages.list-special-pages');
    }

    #[Computed]
    public function special_pages(): LengthAwarePaginator
    {
        return SpecialPage::select('special_pages.*')
            ->join('users', 'users.id', '=', 'special_pages.user_id')
            ->orderBy('special_pages.' . $this->sortField, $this->sortDirection)
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

    public function delete(SpecialPage $page): void
    {
        $this->SpecialPage = $page;
        $this->dispatch('show_delete_modal');
    }

    public function confirmDelete(): void
    {
        $this->SpecialPage->delete();

        $this->SpecialPage->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete_modal');
    }
}

