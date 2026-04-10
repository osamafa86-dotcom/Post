<?php

namespace App\Livewire\Dashboard;


use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListApplications extends Component
{
    use WithPagination, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Application_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public $category , $is_accepted;
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-applications');
    }

    public function mount($add = null): void
    {

    }


    #[Computed]
    public function applications()
    {

        $applications = Application::select('*')
                ->when(!empty($this->category) && $this->category != 'all', function ($query) {
                    $query->where('course_type', $this->category);
            })
            ->when( ($this->is_accepted === '0' || $this->is_accepted === '1') && $this->category != 'all', function ($query) {
                $query->where('is_accepted', $this->is_accepted);
            });

        $applications = (clone $applications)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);


        $volunteering = (clone $applications)
            ->where('course_type', CategoryTypeEnum::VOLUNTEERING->value)
            ->count();
        $courses = (clone $applications)
            ->where('course_type', CategoryTypeEnum::TRAINING->value)
            ->count();
        $conference = (clone $applications)
            ->where('course_type', CategoryTypeEnum::CONFERENCE->value)
            ->count();
        $accepted = (clone $applications)
            ->where('is_accepted', 1)
            ->count();
        $not_accepted = (clone $applications)
            ->where('is_accepted', 0)
            ->count();

        return [
            'applications' => $applications,
            'volunteering' => $volunteering,
            'courses' => $courses,
            'conference' => $conference,
            'accepted' => $accepted,
            'not_accepted' => $not_accepted,
            ];
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

//    public function addNew(): void
//    {
//        $this->showEdit = false;
//        $this->state = [];
//        $this->dispatch('show_form');
//    }

    public function filterCategory($category)
    {

        $this->category = $category;
    }

    public function filterAccepted($is_accepted)
    {
        $this->category = null;
        $this->is_accepted = $is_accepted;
    }

    public function download($file)
    {

        return Storage::download($file);
    }

    public function edit(Application $application): void
    {
        $this->state = [];
        $this->Application_ = $application;
        $this->state = $application->toArray();

        $this->showEdit = true;
        $this->dispatch('show_change_status_form');
    }

    /**
     * @throws ValidationException
     */
//    public function create(): void
//    {
//
//
//        $validation = Validator::make($this->state, [
//            'service_image' => 'required|integer|exists:files,id',
//            'title' => 'required|string|max:255',
//            'description' => 'required|string|max:455',
//            'button_title' => 'required|string|max:255',
//            'url' => 'required|url|max:255',
//            'is_show' => 'nullable',
//        ])->validate();
//
//        $image = $validation['service_image'] ?? null;
//        unset($validation['service_image']);
//
//        $service = Service::query()->create($validation);
//
//        if (!empty($image))
//            $service?->files()->create([
//                'file_id' => $image ?? null,
//                'model_type' => Service::class,
//                'model_column' => 'service_image',
//            ]);
//        $service->user_logs()->create([
//            'user_id' => Auth::id(),
//            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
//        ]);
//        $this->dispatch('hide_form', ['message' => __('validation.saved_success')]);
//    }
//
//    /**
//     * @throws ValidationException
//     */
//    public function update(): void
//    {
//
//        $validation = Validator::make($this->state, [
//            'service_image' => 'required|integer|exists:files,id',
//            'title' => 'required|string|max:255',
//            'description' => 'required|string|max:455',
//            'button_title' => 'required|string|max:255',
//            'url' => 'required|url|max:255',
//            'is_show' => 'nullable',
//        ])->validate();
//
//        $image = $validation['service_image'] ?? null;
//        unset($validation['service_image']);
//
//        $this->Service_->update($validation);
//
//        $this->Service_->files()->update([
//            'file_id' => $image ?? null,
//        ]);
//
//        $this->Service_->user_logs()->create([
//            'user_id' => Auth::id(),
//            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
//        ]);
//        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
//    }

    public function delete(Application $application): void
    {
        $this->Application_ = $application;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->Application_->delete();
        $this->Application_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
    }

    public function confirmChangeStatus(): void
    {
        if ($this->Application_->is_accepted == 1) {
            $this->Application_->update(['is_accepted' => 0]);
        } else {
            $this->Application_->update(['is_accepted' => 1]);
        }

        $this->Application_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => __('change_application_status'),
        ]);

        $this->dispatch('hide_change_status_form');
    }
}
