<?php

namespace App\Livewire\Dashboard;

use App\Models\Alert;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListAlerts extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Alerts_;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-alerts');
    }

    #[Computed]
    public function alerts(): LengthAwarePaginator
    {
        return Alert::where('status', false)
            ->where(function ($q) {
                $q->when(isset($this->search), function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('type', 'like', '%' . $this->search . '%');
                });
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

    public function alertsStatus(Alert $alerts): void
    {
        $this->Alerts_ = $alerts;
        $this->dispatch('show_alerts_status');
    }

    public function alertsStatusConfirm(): void
    {
        $before_data = $this->Alerts_->toArray();

        $this->Alerts_->update(['status' => true]);

        $new_data = $this->Alerts_->toArray();

        $this->Alerts_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_alerts_status');
    }

    public function alertsDelete(Alert $alerts): void
    {
        $this->Alerts_ = $alerts;
        $this->dispatch('show_alerts_delete');
    }

    public function alertsDeleteConfirm(): void
    {
        $this->Alerts_->delete();
        $this->Alerts_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_alerts_delete');
    }
}
