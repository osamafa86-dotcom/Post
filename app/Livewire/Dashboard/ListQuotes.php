<?php

namespace App\Livewire\Dashboard;

use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Files;
use App\Models\Participant;
use App\Models\Quote;
use App\Models\SortData;
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
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListQuotes extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Quotes_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public $selected = [];
    public $selectAll = false;
    public $delete_text;

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-quotes');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function quotes(): LengthAwarePaginator
    {
        return Quote::when(isset($this->search), function (Builder $query) {
            $query->where('quote_from', 'like', '%' . $this->search . '%')
                ->orWhere('quote_text', 'like', '%' . $this->search . '%');
        })
            ->with(['files.file','author'])->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function authors()
    {
        return Participant::select('id', 'name')->where('type', ParticipantTypeEnum::AUTHORS)->get();
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
        $this->dispatch('show_quote_form');
    }

   public function edit(Quote $quote): void
{
    $this->reset();

    $this->Quotes_ = $quote;
    $this->state = $quote->toArray();

    $this->state['author_id'] = $quote->author_id;
    $this->state['author_name'] = $quote->author->name ?? '';

    // if (isset($this->Quotes_->files->file_id)) {
    //     $this->state['quote_photo'] = $this->Quotes_->files->file_id;
    //     $this->state['quote_photo_name'] = Files::where('id', $this->Quotes_->files->file_id)->value('path');
    // }

    $this->showEdit = true;
    $this->dispatch('show_quote_form');
}

    /**
     * @throws ValidationException
     */
    public function createQuote(): void
    {
        $validation = Validator::make($this->state, [
            'author_id' => 'required|exists:participants,id',
            'quote_from' => 'nullable|string|max:255',
            'quote_text' => 'required|string|max:500',
        ])->validate();
        $Quote = Quote::query()->create([
            'quote_from' => $validation['quote_from'] ?? null,
            'quote_text' => $validation['quote_text'] ?? null,
            'author_id' => $validation['author_id'] ?? null,
            'publish_status' => PublishEnum::PUBLISHED->value,
        ]);

        // If you want to store multiple authors, use a pivot table
        // $Quote->authors()->sync($validation['author_id']);

        $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
        $Quote->sortable()->create([
            'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
        ]);

        $Quote->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);

        $this->dispatch('hide_quote_form', ['message' => __('validation.saved_success')]);
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['quote_photo'] = $id;
        $this->state['quote_photo_name'] = Files::where('id', $id)->value('path');
    }

    /**
     * @throws ValidationException
     */
    public function updateQuote(): void
    {
        $validation = Validator::make($this->state, [
            'author_id' => 'required|exists:participants,id',
            'quote_from' => 'nullable|string|max:255',
            'quote_text' => 'required|string|max:500',
        ])->validate();

        $authorId = $validation['author_id'];

        $quote_photo = $validation['quote_photo'] ?? null;
        unset($validation['quote_photo']);

        $before_data = $this->Quotes_->toArray();

        $this->Quotes_->update([
            'author_id' => $authorId,
            'quote_from' => $validation['quote_from'] ?? null,
            'quote_text' => $validation['quote_text'],
        ]);

        /*
        $this->Quotes_->files()->update([
            'file_id' => $quote_photo ?? null,
        ]);
        */

        $new_data = $this->Quotes_->fresh()->toArray();

        $this->Quotes_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data),
        ]);

        $this->dispatch('hide_quote_form', ['message' => __('validation.edit_success')]);
    }
    public function quoteDelete(Quote $quote): void
    {
        $this->Quotes_ = $quote;
        $this->dispatch('show_quote_delete');
    }

    public function quoteDeleteConfirm(): void
    {
        $this->Quotes_->sortable()->delete();
        $this->Quotes_->delete();
        $this->Quotes_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_quote_delete');
        $this->resetSelection();

    }

    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->quotes->items())->pluck('id')->all();
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
        $Ids = collect($this->quotes->items())->pluck('id')->all();
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

                $items = Quote::whereIn('id', $this->selected)->get();

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
