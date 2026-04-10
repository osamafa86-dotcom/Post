<?php

namespace App\Livewire\Dashboard;

use App\Enums\LinkPosition;
use App\Models\Icon;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListIcons extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Icons_;
    public array $state = [];
    public $selected = [];
    public $selectAll = false;
    public $delete_text;
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-icons');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function icons(): LengthAwarePaginator
    {
        return Icon::select('*')
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
        $this->dispatch('show_icon_form');
    }


    public function edit(Icon $icon): void
    {
        $this->state = [];
        $this->Icons_ = $icon;
        $this->state = $icon->toArray();
        $this->showEdit = true;
        $this->dispatch('show_icon_form');
    }

    /**
     * @throws ValidationException
     */
    public function createIcon(): void
    {
        $validation = Validator::make($this->state, [
            'icon_path' => 'required|image|max:2048',
        ])->validate();

        $disk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
        $baseFolder = config('app.aws_base_folder');
        
        // تحديد المسار بناءً على وجود base folder
        if ($disk === 's3' && !empty($baseFolder)) {
            $baseFolder = trim($baseFolder, '/');
            $dir = $baseFolder . '/icons';
        } else {
            $dir = 'icons';
        }
        
        $storedPath = $this->state['icon_path']->store($dir, config('filesystems.default'));
        
        // حفظ المسار الكامل في قاعدة البيانات
        $icon = Icon::query()->create([
            'icon_path' => $storedPath,
        ]);

        $icon->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_icon_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateIcon(): void
    {
        $rules = [
            'position' => 'required|string',
        ];

        if (!empty($this->state['icon_path'])) {
            $rules['icon_path'] = 'required|image|max:2048';
        }

        $validation = Validator::make($this->state, $rules)->validate();

        $updateData = [];
        
        if (!empty($validation['icon_path'])) {
            $disk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
            $baseFolder = config('app.aws_base_folder');
            
            // تحديد المسار بناءً على وجود base folder
            if ($disk === 's3' && !empty($baseFolder)) {
                $baseFolder = trim($baseFolder, '/');
                $dir = $baseFolder . '/icons';
            } else {
                $dir = 'icons';
            }
            
            $storedPath = $this->state['icon_path']->store($dir, config('filesystems.default'));
            
            // حفظ المسار الكامل في قاعدة البيانات
            $updateData['icon_path'] = $storedPath;
        }
        
        if (!empty($validation['position'])) {
            $updateData['position'] = $validation['position'];
        }

        $this->Icons_->update($updateData);
        $this->Icons_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_icon_form');
    }
    public function iconDelete(Icon $icon): void
    {
        $this->Icons_ = $icon;
        $this->dispatch('show_icon_delete');
    }

    public function iconDeleteConfirm(): void
    {
        $this->Icons_->delete();
        $this->Icons_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_icon_delete');
        $this->resetSelection();

    }

    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->icons->items())->pluck('id')->all();
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
        $Ids = collect($this->icons->items())->pluck('id')->all();
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
                $items = Icon::whereIn('id', $this->selected)->get();

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
