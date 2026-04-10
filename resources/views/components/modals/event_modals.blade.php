@php use App\Enums\DateTypeEnum;use App\Enums\DayEnum; @endphp
    <!-- Create/Edit Modal -->
<div class="modal fade" id="eventsForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{ $showEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ $showEdit ? __('messages.events.edit_event') : __('messages.events.add_event') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $showEdit ? 'updateEvent' : 'createEvent' }}">
                    <div class="mb-3">
                        <label for="title" class="form-label">{{ __('messages.events.title') }}</label>
                        <input type="text" id="title" class="form-control @error('title') is-invalid @enderror"
                               wire:model="state.title" placeholder="{{ __('messages.events.title') }}">
                        @error('title')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-12 mb-3">
                        <label for="author_image">{{ __('messages.events.image') }}</label>
                        <br>
                        <div class="position-relative mb-3"
                             style="display: inline-block; width: 100%; max-width: 150px;">
                            <!-- الصورة -->
                            <img
                                src="{{ isset($state['event_image_name']) ? file_url($state['event_image_name']) : 'https://dummyimage.com/' . config('features.image_sizes.event_image') . '/dddddd/000000' }}"
                                alt="{{ __('messages.events.image') }}"
                                id="imagePreview" class="img-fluid border"
                                style="cursor: pointer; border-radius: 8px;"
                                onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                            <!-- زر الإغلاق أعلى الصورة -->
                            <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                    aria-label="Close" wire:click.prevent="clearColumn('event_image')"
                                    style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                        </div>
                        @error('event_image')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div wire:ignore>
                            <label for="tags">{{ __('messages.events.tags') }}</label>
                            <input class="form-control @error('tags') is-invalid @enderror"
                                   id="tags" name="tags"
                                   wire:model.live="state.tags">
                        </div>
                        @error('tags')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="mt-3">{{ __('messages.events.category_section') }}</label>
                        <div class="d-flex align-items-center" wire:ignore>
                            <select class="form-select"
                                    wire:model.live="state.category_id"
                                    id="category_id"
                                    multiple
                                    data-control="select2"
                                    data-placeholder="{{ __('messages.choose') }}">
                                @foreach($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_id')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="presenter_id" class="mt-3">{{ __('messages.events.presenter_section') }}</label>
                        <div class="d-flex align-items-center" wire:ignore>
                            <select class="form-select"
                                    wire:model.live="state.presenter_id"
                                    id="presenter_id"
                                    multiple
                                    data-control="select2"
                                    data-placeholder="{{ __('messages.choose') }}">
                                @foreach($this->presenters as $presenter)
                                    <option value="{{ $presenter->id }}">{{ $presenter->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('presenter_id')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">{{ __('messages.events.url') }}</label>
                        <input type="url" id="url" class="form-control @error('url') is-invalid @enderror"
                               wire:model="state.url" placeholder="{{ __('messages.events.url') }}">
                        @error('url')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('messages.events.description') }}</label>
                        <textarea rows="5" id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  wire:model="state.description"
                                  placeholder="{{ __('messages.events.description') }}"></textarea>
                        @error('description')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date_type">{{ __('messages.events.date_type') }}</label>
                        <select class="form-control @error('date_type') is-invalid @enderror"
                                wire:model.live="state.date_type"
                                id="date_type">
                            <option>{{ __('messages.choose') }}...</option>
                            @foreach(DateTypeEnum::cases() as $row)
                                <option value="{{$row->value}}">{{$row->label()}}</option>
                            @endforeach
                        </select>
                        @error('date_type')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        @if(!empty($state['date_type']) && $state['date_type'] == DateTypeEnum::TIME->value)
                            @error("event_times.event_times")
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                            <a wire:click="addEventTime" class="btn btn-primary mt-2 mb-4">{{ __('messages.add') }}</a>
                            @foreach($event_times as $key => $event_time)
                                <div class="row" wire:key="{{$key}}">
                                    <div class="col-md-4 mb-3">
                                        <label for="day" class="mb-2">اليوم</label>
                                        <select class="form-control @error("event_times.$key.day") is-invalid @enderror"
                                                wire:model.live="event_times.{{$key}}.day"
                                                id="day">
                                            <option>{{ __('messages.choose') }}...</option>
                                            @foreach(DayEnum::cases() as $row)
                                                <option value="{{$row->value}}">{{$row->label()}}</option>
                                            @endforeach
                                        </select>
                                        @error("event_times.$key.day")
                                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="start_time"
                                               class="form-label">{{ __('messages.events.start_time') }}</label>
                                        <input type="time" id="start_time"
                                               class="form-control @error("event_times.$key.start_time") is-invalid @enderror"
                                               wire:model.live="event_times.{{$key}}.start_time"
                                               placeholder="{{ __('messages.events.start_time') }}">
                                        @error("event_times.$key.start_time")
                                        <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="end_time"
                                               class="form-label">{{ __('messages.events.end_time') }}</label>
                                        <input type="time" id="end_time"
                                               class="form-control @error("event_times.$key.end_time") is-invalid @enderror"
                                               wire:model.live="event_times.{{$key}}.end_time"
                                               placeholder="{{ __('messages.events.end_time') }}">
                                        @error("event_times.$key.end_time")
                                        <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a wire:click="removeEventTime({{$key}})"
                                           class="btn btn-sm btn-danger">{{ __('messages.delete') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        @elseif(!empty($state['date_type']) && $state['date_type'] == DateTypeEnum::DATE_TIME->value)
                            @error("event_date_times.event_date_times")
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                            <a wire:click="addEventDateTime"
                               class="btn btn-primary mt-2 mb-4">{{ __('messages.add') }}</a>
                            @foreach($event_date_times as $key => $event_time)
                                <div class="row" wire:key="{{$key}}">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date_time"
                                               class="form-label">{{ __('messages.events.start_date_time') }}</label>
                                        <input type="datetime-local" id="start_date_time"
                                               class="form-control @error("event_date_times.$key.start_date_time") is-invalid @enderror"
                                               wire:model.live="event_date_times.{{$key}}.start_date_time"
                                               placeholder="{{ __('messages.events.start_date_time') }}">
                                        @error("event_date_times.$key.start_date_time")
                                        <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="end_date_time"
                                               class="form-label">{{ __('messages.events.end_date_time') }}</label>
                                        <input type="datetime-local" id="end_date_time"
                                               class="form-control @error("event_date_times.$key.end_date_time") is-invalid @enderror"
                                               wire:model.live="event_date_times.{{$key}}.end_date_time"
                                               placeholder="{{ __('messages.events.end_date_time') }}">
                                        @error("event_date_times.$key.end_date_time")
                                        <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <a wire:click="removeEventDateTime({{$key}})"
                                           class="btn btn-sm btn-danger">{{ __('messages.delete') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn {{ $showEdit ? 'btn-warning' : 'btn-primary' }}">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="eventsDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="deleteModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="deleteModalLabel">{{ __('messages.events.delete_event') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.events.confirm_delete_event') }}</p>
                <h6>{{__('messages.events.delete_event_message', ['title' => $Events_->title ?? __('messages.no_data')])}}</h6>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="eventDeleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteSelected" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{__('messages.events.delete_events')}}   </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.events.confirm_delete_events')}}</h4>
                <h6>{{__('messages.events.delete_events_message')}}</h6>
                <div class="form-group col-md-12 mb-3">
                    <input wire:model="delete_text"
                           class="form-control"
                           id="category_description"
                    >
                </div>
                <!-- Error Message -->
                @if (session()->has('error'))
                    <div style="color: red; padding: 10px;">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="confirmDeleteSelected"
                            class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
