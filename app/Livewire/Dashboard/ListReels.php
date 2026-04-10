<?php

namespace App\Livewire\Dashboard;

use App\Models\Files;
use App\Models\Quote;
use App\Models\Reel;
use App\Models\SortData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListReels extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $reels_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list_reels');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function reels(): LengthAwarePaginator
    {
        return Reel::when(isset($this->search), function (Builder $query) {
            $query->where('reel_title', 'like', '%' . $this->search . '%')
                ->orWhere('reel_url', 'like', '%' . $this->search . '%')
                ->orWhere('reel_type', 'like', '%' . $this->search . '%');
        })
            ->with(['files.file'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[Computed]
    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'bi-arrow-down-up';
        }

        return $this->sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_reel_form');
    }

    #[On('imageSelected')]
    public function imageSelected($id, $column = null)
    {
        if (isset($column)) {
            $this->state[$column] = $id;
            $this->state[$column . '_name'] = Files::where('id', $id)->value('path');
        }
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

    public function edit(Reel $reel): void
    {
        $this->state = [];
        $this->reels_ = $reel;
        $this->state = $reel->toArray();

        if (isset($this->reels_->files)) {
            $this->state['file'] = $this->reels_->files?->where('model_column', 'file')?->first()?->file_id;
            $this->state['file_name'] = $this->reels_->files?->where('model_column', 'file')?->first()?->file?->path;
            
            $this->state['poster'] = $this->reels_->files?->where('model_column', 'poster')?->first()?->file_id;
            $this->state['poster_name'] = $this->reels_->files?->where('model_column', 'poster')?->first()?->file?->path;
        }

        $this->showEdit = true;
        $this->dispatch('show_reel_form');
    }

    /**
     * @throws ValidationException
     */
    public function createReel(): void
    {
        $url_rule = 'nullable';
        $file_rule = 'nullable';

        if (!empty($this->state['reel_type']) && $this->state['reel_type'] == \App\Enums\ReelTypeEnum::LOCAL->value) {
            $file_rule = 'required|integer|exists:files,id';
        } else {
            $url_rule = 'required|url';
        }

        $validation = Validator::make($this->state, [
            'reel_title' => 'required|string|max:100',
            'reel_url' => $url_rule,
            'reel_type' => 'required|integer',
            'file' => $file_rule,
            'poster' => 'nullable|integer|exists:files,id',
        ])->validate();

        $file = $validation['file'] ?? null;
        $poster = $validation['poster'] ?? null;
        unset($validation['file'], $validation['poster']);

        $reel = Reel::query()->create($validation);

        if ($file) {
            $reel->files()->create([
                'file_id' => $file,
                'model_type' => Reel::class,
                'model_column' => 'file',
            ]);
        }

        if ($poster) {
            $reel->files()->create([
                'file_id' => $poster,
                'model_type' => Reel::class,
                'model_column' => 'poster',
            ]);
        }

        $reel->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);

        $this->dispatch('hide_reel_form', ['message' => __('validation.saved_success')]);
    }


    /**
     * @throws ValidationException
     */
    public function updateReel(): void
    {
        $url_rule = 'nullable';
        $file_rule = 'nullable';

        if (!empty($this->state['reel_type']) && $this->state['reel_type'] == \App\Enums\ReelTypeEnum::LOCAL->value) {
            $file_rule = 'required|integer|exists:files,id';
        } else {
            $url_rule = 'required|url';
        }

        $validation = Validator::make($this->state, [
            'reel_title' => 'required|string|max:100',
            'reel_url' => $url_rule,
            'reel_type' => 'required|integer',
            'file' => $file_rule,
            'poster' => 'nullable|integer|exists:files,id',
        ])->validate();

        $file = $validation['file'] ?? null;
        $poster = $validation['poster'] ?? null;
        unset($validation['file'], $validation['poster']);

        $this->reels_->update($validation);

        if ($file) {
            $this->reels_->files()->updateOrCreate(
                ['model_column' => 'file'],
                ['file_id' => $file, 'model_type' => Reel::class]
            );
        }

        if ($poster) {
            $this->reels_->files()->updateOrCreate(
                ['model_column' => 'poster'],
                ['file_id' => $poster, 'model_type' => Reel::class]
            );
        }

        $this->reels_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_reel_form', ['message' => __('validation.edit_success')]);
    }

    public function reelDelete(Reel $reel): void
    {
        $this->reels_ = $reel;
        $this->dispatch('show_reel_delete');
    }

    public function reelDeleteConfirm(): void
    {
        $this->reels_->delete();
        $this->reels_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_reel_delete');
    }
    }

