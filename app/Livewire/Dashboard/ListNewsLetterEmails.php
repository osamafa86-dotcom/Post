<?php

namespace App\Livewire\Dashboard;

use App\Exports\NewsLetterEmailsExport;
use App\Models\LiveStream;
use App\Models\NewsLetterEmails;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListNewsLetterEmails extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $NewsLetterEmails_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-news-letter-emails');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function news_letter_emails(): LengthAwarePaginator
    {
        return NewsLetterEmails::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('email', 'like', '%' . $this->search . '%');
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

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_news_letter_email_form');
    }

    public function edit(NewsLetterEmails $news_letter_email): void
    {
        $this->state = [];
        $this->NewsLetterEmails_ = $news_letter_email;
        $this->state = $news_letter_email->toArray();
        $this->showEdit = true;
        $this->dispatch('show_news_letter_email_form');
    }

    /**
     * @throws ValidationException
     */
    public function create(): void
    {
        $validation = Validator::make($this->state, [
            'email' => 'required|string|max:255|unique:news_letter_emails',
        ])->validate();

        $NewsLetterEmail = NewsLetterEmails::query()->create($validation);

        $NewsLetterEmail->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_news_letter_email_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        $validation = Validator::make($this->state, [
            'email' => 'required|string|max:255|unique:news_letter_emails',
        ])->validate();


        $before_data = $this->NewsLetterEmails_->toArray();

        $this->NewsLetterEmails_->update($validation);

        $new_data = $this->NewsLetterEmails_->toArray();

        $this->NewsLetterEmails_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->dispatch('hide_news_letter_email_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(NewsLetterEmails $news_letter_email): void
    {
        $this->NewsLetterEmails_ = $news_letter_email;
        $this->dispatch('show_news_letter_email_delete');
    }

    public function deleteConfirm(): void
    {
        $this->NewsLetterEmails_->delete();
        $this->NewsLetterEmails_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_news_letter_email_delete');
    }

    public function export()
    {
        $name = Carbon::parse(now())->format('Y-m-d H-i') . ' Records.xlsx';


        $export = Excel::download(new NewsLetterEmailsExport($this->news_letter_emails()), $name);

        $this->dispatch('hide_export_with_sort');
        return $export;
    }

}
