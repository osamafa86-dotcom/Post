<?php

namespace App\Livewire\Dashboard;

use App\Models\SendNews;
use App\Models\Sends;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListSends extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Sends_;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-sends');
    }


    #[Computed]
    public function sends(): LengthAwarePaginator
    {
        return SendNews::query()
            ->when(isset($this->search), function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
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

    public function sendsDelete(SendNews $sends): void
    {
        $this->Sends_ = $sends;
        $this->dispatch('show_sends_delete');
    }

    public function sendsDeleteConfirm(): void
    {
        $this->Sends_->delete();
        $this->Sends_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_sends_delete');
    }
}
