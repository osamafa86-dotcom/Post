<?php

namespace App\Livewire\Dashboard;

use App\Models\BreakingNews;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListBreakingNews extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public ?string $search = "";
    public object $BreakingNews_;
    public array $state = [];
    public $selected = [];
    public $selectAll = false;
    public bool $selectAllColumns = true;
    public $delete_text;
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    public array $columnVisibility = [
        'news_id' => true,
        'title' => true,
        'url' => true,
        'actions' => true,
        'publish_status' => true,
    ];
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-breaking-news');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }

        if (auth()->check()) {
            $savedColumns = auth()->user()->getPreference('breaking_news_columns', []);
            if (!empty($savedColumns)) {
                $allowedColumns = array_keys($this->columnVisibility);
                $filteredSaved = array_intersect_key(
                    $savedColumns,
                    array_flip($allowedColumns)
                );
                $this->columnVisibility = array_merge($this->columnVisibility, $filteredSaved);
            }
        }

    }

    #[Computed]
    public function breaking_news(): LengthAwarePaginator
    {
        return BreakingNews::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function sortBy($field): void
    {
        $this->sortDirection = isset($this->sortDirection) && $this->sortDirection == 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->state['hide_date'] = Carbon::now()->format('Y-m-d H:i');
        $this->dispatch('show_breaking_news_form');
    }

    public function edit(BreakingNews $breaking_news): void
    {
        $this->state = [];
        $this->BreakingNews_ = $breaking_news;
        $this->state = $breaking_news->toArray();
        $this->showEdit = true;
        $this->dispatch('show_breaking_news_form');
    }
    public function updatedSelectAllColumns($value)
    {
        foreach ($this->columnVisibility as $column => $visible) {
            $this->columnVisibility[$column] = $value;
        }

        // Save preferences
        if (auth()->check()) {
            auth()->user()->setPreference('post_columns', $this->columnVisibility);
        }
    }
    public function updatedColumnVisibility($value, $key)
    {
        if (!in_array(false, $this->columnVisibility)) {
            $this->selectAllColumns = true;
        } else {
            $this->selectAllColumns = false;
        }

        if (auth()->check()) {
            auth()->user()->setPreference('post_columns', $this->columnVisibility);
        }
    }
    /**
     * @throws ValidationException
     */
    public function createBreakingNews(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:255',
            'hide_date' => 'nullable|date',
            'url' => 'nullable|url',
        ])->validate();

        $BreakingNews = BreakingNews::query()->create($validation);
        $BreakingNews->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_breaking_news_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateBreakingNews(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:255',
            'hide_date' => 'nullable|date',
            'url' => 'nullable|url',
        ])->validate();

        $before_data = $this->BreakingNews_->toArray();

        $this->BreakingNews_->update($validation);

        $new_data = $this->BreakingNews_->toArray();

        $this->BreakingNews_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_breaking_news_form', ['message' => __('validation.edit_success')]);
    }

    public function breaking_newsDelete(BreakingNews $breaking_news): void
    {
        $this->BreakingNews_ = $breaking_news;
        $this->dispatch('show_breaking_news_delete');
    }

    public function breaking_newsDeleteConfirm(): void
    {
        $this->BreakingNews_->delete();
        $this->BreakingNews_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_breaking_news_delete');
        $this->resetSelection();

    }

    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->breaking_news->items())->pluck('id')->all();
        if ($value) {
            $this->selected = array_unique(array_merge($this->selected, $Ids));
        } else {
            $this->selected = array_diff($this->selected, $Ids);
        }
        $this->checkSelectAll();
    }


    public function updatedSelected(): void
    {
        $this->checkSelectAll();
    }


    public function updatedPage(): void
    {
        $this->selectAll = false;
        $this->checkSelectAll();
    }

    private function checkSelectAll(): void
    {
        $Ids = collect($this->breaking_news->items())->pluck('id')->all();
        $this->selectAll = !array_diff($Ids, $this->selected);
    }

    public function deleteSelected(): void
    {
        if (!empty($this->selected)) {
            $this->dispatch('show_delete_selected_modal');
        } else {
            $this->dispatch('no-data');
        }
    }

    public function confirmDeleteSelected(): void
    {
        if ($this->delete_text) {
            if ($this->delete_text == 'Delete') {
                $items = BreakingNews::whereIn('id', $this->selected)->get();

                $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

                foreach ($items as $item) {
                    $item->delete();

                    $item->user_logs()->create([
                        'user_id' => Auth::id(),
                        'action_status' => \App\Enums\ActionsEnum::DELETE->value,
                    ]);
                }


                $this->dispatch('hide_delete_selected_modal');
                $this->resetPage();
                $this->resetSelection();

                  } else {
                session()->flash('error',__('messages.error_delete_text'));
            }
        } else {
            session()->flash('error',__('messages.empty_delete_text') );
        }


    }
    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selected = [];
    }
}
