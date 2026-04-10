<?php

namespace App\Livewire\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\DateTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventRelation;
use App\Models\Files;
use App\Models\Participant;
use App\Models\PostRelation;
use App\Models\Tag;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListEvents extends Component
{
    use WithPagination, WhereIn;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Events_;
    public array $state = [];
    public array $event_times = [];
    public array $event_date_times = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";
    public $selected = [];
    public $selectAll = false;
    public $delete_text;

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-events');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    public function addEventTime(): void
    {
        $this->event_times[] = [
            'day' => '',
            'start_time' => '',
            'end_time' => '',
        ];
    }

    public function addEventDateTime(): void
    {
        $this->event_date_times[] = [
            'start_date_time' => '',
            'end_date_time' => '',
        ];
    }

    public function removeEventTime($key): void
    {
        unset($this->event_times[$key]);
    }

    public function removeEventDateTime($key): void
    {
        unset($this->event_date_times[$key]);
    }

    public function updatedState($value, $key): void
    {
        if ($key == 'date_type') {
            $this->event_date_times = [];
            $this->event_times = [];
        }
    }

    #[Computed]
    public function events(): LengthAwarePaginator
    {
        return Event::select('*')
            ->when(isset($this->search), function (Builder $query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->with(['files.file', 'event_dates'])
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return Category::select('id', 'category_title')->whereIn('category_type', [CategoryTypeEnum::EVENTS->value, CategoryTypeEnum::COURSES->value, CategoryTypeEnum::CONFERENCE->value, CategoryTypeEnum::VOLUNTEERING->value,])->get();
    }

    #[Computed]
    public function presenters()
    {
        return Participant::select('id', 'name')->where('type', ParticipantTypeEnum::PRESENTERS->value)->get();
    }

    #[Computed]
    public function tags(): array
    {
        return Tag::where('tag_type', TagTypeEnum::EVENTS->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
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
        $this->resetErrorBag(); // يمسح كل الأخطاء
        $this->dispatch('show_event_form');
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['event_image'] = $id;
        $this->state['event_image_name'] = Files::where('id', $id)->value('path');
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

    public function edit(Event $event): void
    {
        $event->load(['event_dates', 'tags.relationable', 'categories.relationable', 'presenters.relationable', 'files.file']);
        $this->state = [];
        $this->Events_ = $event;
        $event_dates = $event->event_dates->toArray();
        $this->state = $event->toArray();

        $tags = $this->Events_->tags
            ? $this->Events_->tags->map(function ($tag) {
                return $tag->relationable?->tag_name;
            })->filter()->implode(',')
            : null;
        $this->state['tags'] = $tags;
        $this->state['category_id'] = $this->Events_->categories
            ? $this->Events_->categories->map(fn($cat) => $cat->relationable?->id)->filter()->toArray()
            : [];

        $this->state['presenter_id'] = $this->Events_->presenters
            ? $this->Events_->presenters->map(fn($pre) => $pre->relationable?->id)->filter()->toArray()
            : [];

        if (isset($this->Events_->files->file_id)) {
            $this->state['event_image'] = $this->Events_?->files?->file_id;
            $this->state['event_image_name'] = $this->Events_?->files?->file?->path;
        }

        if ($event->date_type == DateTypeEnum::TIME->value) {
            $this->event_times = $event_dates;
        } elseif ($event->date_type == DateTypeEnum::DATE_TIME->value) {
            $this->event_date_times = $event_dates;
        }
        $this->showEdit = true;
        $this->resetErrorBag(); // يمسح كل الأخطاء
        $this->dispatch('show_event_form');
    }

    /**
     * @throws ValidationException
     */
    public function createEvent(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:100|unique:events,title',
            'tags' => 'required|string',
            'category_id' => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'presenter_id' => 'required|array',
            'presenter_id.*' => 'required|exists:participants,id',
            'url' => 'nullable|url',
            'description' => 'nullable|string|max:700',
            'date_type' => 'required|in:' . $this->whereIn()['DateTypeEnum'],
            'event_image' => 'required|integer|exists:files,id',
        ])->validate();

        $event_image = $validation['event_image'] ?? null;
        unset($validation['event_image']);


        if ($validation['date_type'] == DateTypeEnum::TIME->value) {

            $validationEventTimes = Validator::make(['event_times' => $this->event_times,],
                [
                    'event_times' => 'required|array', // Validate the root array
                    'event_times.*' => 'required|array', // Validate each item in the array
                    'event_times.*.day' => 'required|in:' . $this->whereIn()['DayEnum'],
                    'event_times.*.start_time' => 'required|date_format:H:i',
                    'event_times.*.end_time' => 'required|date_format:H:i',
                ],
                [
                    'event_times.required' => __('event_times_required'),
                    'event_times.*.day.required' => __('event_times_day_required'),
                    'event_times.*.day.in' => __('event_times_day_in'),
                    'event_times.*.start_time.required' => __('event_times_start_time_required'),
                    'event_times.*.start_time.date_format' => __('event_times_start_time_date_format'),
                    'event_times.*.end_time.required' => __('event_times_end_time_required'),
                    'event_times.*.end_time.date_format' => __('event_times_end_time_date_format'),
                ]
            )->validate();
            foreach ($this->event_times as $index => $event) {
                if (strtotime($event['start_time']) >= strtotime($event['end_time'])) {
                    throw ValidationException::withMessages([
                        "event_times.$index.end_time" => __('event_times_end_time'),
                    ]);
                }
            }
        }
        if ($validation['date_type'] == DateTypeEnum::DATE_TIME->value) {
            foreach ($this->event_date_times as &$item) {
                if (!empty($item['start_date_time']) && !empty($item['end_date_time'])) {
                    $item['start_date_time'] = Carbon::parse($item['start_date_time'])->format('Y-m-d H:i:s');
                    $item['end_date_time'] = Carbon::parse($item['end_date_time'])->format('Y-m-d H:i:s');
                }
            }
            $validationEventDateTime = Validator::make(['event_date_times' => $this->event_date_times],
                [
                    'event_date_times' => 'required|array', // Ensure the root is an array
                    'event_date_times.*.start_date_time' => 'required|date_format:Y-m-d H:i:s', // Validate start_date_time for each item
                    'event_date_times.*.end_date_time' => 'required|date_format:Y-m-d H:i:s', // Validate end_date_time for each item
                ],
                [
                    'event_date_times.required' => __('event_date_times_required'),
                    'event_date_times.*.start_date_time.required' => __('event_date_times_start_date_time_required'),
                    'event_date_times.*.start_date_time.date_format' => __('event_date_times_start_date_time_date_format'),
                    'event_date_times.*.end_date_time.required' => __('event_date_times_end_date_time_required'),
                    'event_date_times.*.end_date_time.date_format' => __('event_date_times_end_date_time_date_format'),
                ]
            )->validate();
            foreach ($this->event_times as $index => $event) {
                if (strtotime($event['start_date_time']) >= strtotime($event['end_date_time'])) {
                    throw ValidationException::withMessages([
                        "event_date_times.$index.end_date_time" => __('event_date_times_end_date_time'),
                    ]);
                }
            }
        }

        $Event = Event::query()->create([
            'title' => $validation['title'],
            'url' => $validation['url'],
            'description' => $validation['description'] ?? null,
            'date_type' => $validation['date_type'],
        ]);
        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::EVENTS->value]);
                EventRelation::create([
                    'event_id' => $Event->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }

        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                EventRelation::create([
                    'event_id' => $Event->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }

        if (!empty($validation['presenter_id'])) {
            foreach ($validation['presenter_id'] as $key => $row)
                EventRelation::create([
                    'event_id' => $Event->id,
                    'relationable_id' => $row,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }

        if ($validation['date_type'] == DateTypeEnum::TIME->value && !empty($validationEventTimes)) {
            foreach ($validationEventTimes as $item) {
                foreach ($item as $time) {
                    $Event->event_dates()->create([
                        'day' => $time['day'],
                        'start_time' => $time['start_time'],
                        'end_time' => $time['end_time'],
                    ]);
                }
            }
        }
        if ($validation['date_type'] == DateTypeEnum::DATE_TIME->value && !empty($validationEventDateTime)) {
            foreach ($validationEventDateTime as $item) {
                foreach ($item as $time) {
                    $Event->event_dates()->create([
                        'start_date_time' => $time['start_date_time'],
                        'end_date_time' => $time['end_date_time'],
                    ]);
                }
            }
        }
        $Event->files()->create([
            'file_id' => $event_image ?? null,
            'model_type' => Event::class,
            'model_column' => 'event_image',
        ]);
        $Event->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_event_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function updateEvent(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:100|unique:events,title,' . $this->Events_->id,
            'tags' => 'required|string',
            'category_id' => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'presenter_id' => 'required|array',
            'presenter_id.*' => 'required|exists:participants,id',
            'url' => 'nullable|url',
            'description' => 'nullable|string|max:700',
            'date_type' => 'required|in:' . $this->whereIn()['DateTypeEnum'],
            'event_image' => 'required|integer|exists:files,id',
        ])->validate();

        $event_image = $validation['event_image'] ?? null;
        unset($validation['event_image']);
        if ($validation['date_type'] == DateTypeEnum::TIME->value) {
            foreach ($this->event_times as $key => &$times) {
                $times['start_time'] = Carbon::parse($times['start_time'])->format('H:i:s');
                $times['end_time'] = Carbon::parse($times['end_time'])->format('H:i:s');
            }
            $validationEventTimes = Validator::make(['event_times' => $this->event_times,],
                [
                    'event_times' => 'required|array', // Validate the root array
                    'event_times.*' => 'required|array', // Validate each item in the array
                    'event_times.*.day' => 'required|in:' . $this->whereIn()['DayEnum'],
                    'event_times.*.start_time' => 'required|date_format:H:i:s',
                    'event_times.*.end_time' => 'required|date_format:H:i:s',
                ],
                [
                    'event_times.required' => __('event_times_required'),
                    'event_times.*.day.required' => __('event_times_day_required'),
                    'event_times.*.day.in' => __('event_times_day_in'),
                    'event_times.*.start_time.required' => __('event_times_start_time_required'),
                    'event_times.*.start_time.date_format' => __('event_times_start_time_date_format'),
                    'event_times.*.end_time.required' => __('event_times_end_time_required'),
                    'event_times.*.end_time.date_format' => __('event_times_end_time_date_format'),
                ]
            )->validate();
            foreach ($this->event_times as $index => $event) {
                if (strtotime($event['start_time']) >= strtotime($event['end_time'])) {
                    throw ValidationException::withMessages([
                        "event_times.$index.end_time" => __('event_times_end_time'),
                    ]);
                }
            }
        }

        if ($validation['date_type'] == DateTypeEnum::DATE_TIME->value) {
            foreach ($this->event_date_times as &$item) {
                if (!empty($item['start_date_time']) && !empty($item['end_date_time'])) {
                    $item['start_date_time'] = Carbon::parse($item['start_date_time'])->format('Y-m-d H:i:s');
                    $item['end_date_time'] = Carbon::parse($item['end_date_time'])->format('Y-m-d H:i:s');
                }
            }
            $validationEventDateTime = Validator::make(['event_date_times' => $this->event_date_times],
                [
                    'event_date_times' => 'required|array', // Ensure the root is an array
                    'event_date_times.*.start_date_time' => 'required|date_format:Y-m-d H:i:s', // Validate start_date_time for each item
                    'event_date_times.*.end_date_time' => 'required|date_format:Y-m-d H:i:s', // Validate end_date_time for each item
                ],
                [
                    'event_date_times.required' => __('event_date_times_required'),
                    'event_date_times.*.start_date_time.required' => __('event_date_times_start_date_time_required'),
                    'event_date_times.*.start_date_time.date_format' => __('event_date_times_start_date_time_date_format'),
                    'event_date_times.*.end_date_time.required' => __('event_date_times_end_date_time_required'),
                    'event_date_times.*.end_date_time.date_format' => __('event_date_times_end_date_time_date_format'),

                ]
            )->validate();
            foreach ($this->event_times as $index => $event) {
                if (strtotime($event['start_date_time']) >= strtotime($event['end_date_time'])) {
                    throw ValidationException::withMessages([
                        "event_date_times.$index.end_date_time" => 'يجب أن يكون وقت الانتهاء بعد وقت البدء',
                    ]);
                }
            }
        }


        $this->Events_->update([
            'title' => $validation['title'],
            'url' => $validation['url'],
            'description' => $validation['description'] ?? null,
            'date_type' => $validation['date_type'],
        ]);
        EventRelation::where('event_id', $this->Events_->id)
            ->where('relationable_type', Tag::class)
            ->delete();

        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::EVENTS->value]);
                EventRelation::create([
                    'event_id' => $this->Events_->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }

        EventRelation::where('event_id', $this->Events_->id)
            ->where('relationable_type', Category::class)
            ->delete();
//        $this->Events_->categories()->forceDelete();
        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                EventRelation::create([
                    'event_id' => $this->Events_->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }
        EventRelation::where('event_id', $this->Events_->id)
            ->where('relationable_type', Participant::class)
            ->delete();
//        $this->Events_->presenters()->forceDelete();
        if (!empty($validation['presenter_id'])) {
            foreach ($validation['presenter_id'] as $key => $row)
                EventRelation::create([
                    'event_id' => $this->Events_->id,
                    'relationable_id' => $row,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }


        $this->Events_->event_dates()->forceDelete();

        if ($validation['date_type'] == DateTypeEnum::TIME->value && !empty($validationEventTimes))
            foreach ($validationEventTimes as $item)
                foreach ($item as $time)
                    $this->Events_->event_dates()->create([
                        'day' => $time['day'],
                        'start_time' => $time['start_time'],
                        'end_time' => $time['end_time'],
                    ]);


        if ($validation['date_type'] == DateTypeEnum::DATE_TIME->value && !empty($validationEventDateTime))
            foreach ($validationEventDateTime as $item)
                foreach ($item as $time)
                    $this->Events_->event_dates()->create([
                        'start_date_time' => $time['start_date_time'],
                        'end_date_time' => $time['end_date_time'],
                    ]);

        $this->Events_->files()->update([
            'file_id' => $event_image ?? null,
        ]);


        $this->Events_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);

        $this->dispatch('hide_event_form', ['message' => __('validation.edit_success')]);
    }

    public function eventDelete(Event $event): void
    {
        $this->Events_ = $event;
        $this->dispatch('show_event_delete');
    }

    public function eventDeleteConfirm(): void
    {
        $this->Events_->delete();
        $this->Events_->event_dates()->delete();
        $this->Events_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_event_delete');
    }


    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->events->items())->pluck('id')->all();
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
        $Ids = collect($this->events->items())->pluck('id')->all();
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
                $items = Event::whereIn('id', $this->selected)->get();

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
                  } else {
                session()->flash('error',__('messages.error_delete_text'));
            }
        } else {
            session()->flash('error',__('messages.empty_delete_text') );
        }


    }
}
