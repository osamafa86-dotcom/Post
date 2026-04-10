<?php

namespace App\Livewire\Dashboard;


use App\Enums\UserStatusEnum;
use App\Exports\SubscriptionsExport;
use App\Imports\SubscriptionImport;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListSubscriptions extends Component
{
    use WithPagination, WithFileUploads, WhereIn;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Subscription_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    public $importFile;
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-subscriptions');
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new SubscriptionsExport, 'subscriptions.xlsx');
    }

    #[Computed]
    public function subscriptions(): LengthAwarePaginator
    {
        return Subscription::select('*')
            ->orderBy($this->sortField, $this->sortDirection)
            ->with('subscriber:id,first_name,last_name')
            ->paginate(10);
    }

    #[Computed]
    public function subscribers()
    {
        return User::select('id', 'first_name')->where('user_status', UserStatusEnum::SUBSCRIBER->value)->get()->toArray();
    }

    public function importExcel()
    {
        // Debug: Check if we have a file
        if (!$this->importFile) {
            session()->flash('error', __('validation.required', ['attribute' => 'File']));
            return;
        }

        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        try {
            Log::info('Starting Excel import...');
            Log::info('Original filename: ' . $this->importFile->getClientOriginalName());

            // Get the file path
            $filePath = $this->importFile->getRealPath();

            if (!file_exists($filePath)) {
                throw new \Exception("Uploaded file not found. Please try uploading again.");
            }

            Log::info('File exists at: ' . $filePath);

            // Import using the file path
            Excel::import(new SubscriptionImport, $filePath);

            Log::info('Excel import completed');

            // Success - reset and close
            $this->reset('importFile');
            $this->dispatch('import_completed');

        } catch (\Throwable $e) {
            Log::error('Import Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Show user-friendly error
            $errorMessage = $e->getMessage();

            // Make error more user-friendly
            if (str_contains($errorMessage, 'Subscriber not found')) {
                $errorMessage = 'Could not find matching subscribers. Please check the subscriber IDs/emails/names in your file.';
            } elseif (str_contains($errorMessage, 'Invalid subscription type')) {
                $errorMessage = 'Invalid subscription type values. Check your file.';
            }

            session()->flash('error', 'Import Error: ' . $errorMessage);
        }
    }

    public function downloadSamples()
    {
        $file = public_path('assets/main/maktoob/files/subscriptions_samples.xlsx');
        return response()->download($file, 'subscriptions_samples.xlsx');
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
        $this->dispatch('show_form');
    }

    public function edit(Subscription $subscription): void
    {
        $subscription->load('subscriber');
        $this->state = [];
        $this->Subscription_ = $subscription;
        $this->state = $subscription->toArray();
        $this->state['subscription_start'] = $subscription->subscription_start?->format('Y-m-d');
        $this->state['subscription_end'] = $subscription->subscription_end?->format('Y-m-d');
        $this->state['subscriber_id'] = $this->Subscription_?->subscriber?->id ?? null;
        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    /**
     * @throws ValidationException
     */
    public function create(): void
    {
        $validation = Validator::make($this->state, [
            'subscription_start' => 'required|date',
            'subscription_end' => 'required|date',
            'type' => 'required|in:' . $this->whereIn()['SubscriptionTypeEnum'],
            'status' => 'required|in:' . $this->whereIn()['SubscriptionStatusEnum'],
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:' . $this->whereIn()['SubscriptionPaymentMethodEnum'],
            'subscriber_id' => 'required|exists:users,id',
        ])->validate();
        $subscription = Subscription::query()->create($validation);
        $subscription->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        $validation = Validator::make($this->state, [
            'subscription_start' => 'required|date',
            'subscription_end' => 'required|date',
            'type' => 'required|in:' . $this->whereIn()['SubscriptionTypeEnum'],
            'status' => 'required|in:' . $this->whereIn()['SubscriptionStatusEnum'],
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:' . $this->whereIn()['SubscriptionPaymentMethodEnum'],
            'subscriber_id' => 'required|exists:users,id',
        ])->validate();
        $this->Subscription_->update($validation);
        $this->Subscription_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(Subscription $subscription): void
    {
        $this->Subscription_ = $subscription;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->Subscription_->delete();
        $this->Subscription_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
    }
}
