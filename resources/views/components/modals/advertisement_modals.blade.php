@php use App\Enums\AdvertisementPlaceEnum; use App\Enums\AdvertisementTypeEnum; use App\Enums\AdvertisementUrlTargetEnum; @endphp
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <form wire:submit.prevent="{{$showEdit ? 'updateAdvertisement' : 'createAdvertisement'}}">
            <div class="row g-4">

                <!-- Main Form Column -->
                <div class="col-md-9">

                    <!-- Advertisement Details Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">{{ __('messages.advertisements.advertisement_details') }}</h5>

                            <!-- Title Field -->
                            <div class="mb-3">
                                <label for="title" class="form-label">{{ __('messages.advertisements.title') }}</label>
                                <input wire:model="state.title" type="text"
                                       class="form-control @error('title') is-invalid @enderror" id="title"
                                       placeholder="e.g. Ramadan Promo Ad">
                                @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Place Dropdown -->
                            <div class="mb-3">
                                <label for="place" class="form-label">{{ __('messages.advertisements.place') }}</label>
                                <select class="form-select @error('place') is-invalid @enderror"
                                        wire:model="state.place" id="place">
                                    @if(!$showEdit)
                                        <option selected>{{ __('messages.choose') }}</option>
                                    @endif
                                    @foreach(AdvertisementPlaceEnum::available() as $row)
                                        <option @selected(isset($state['place']) && $state['place'] == $row->value)
                                                value="{{ $row->value }}">{{ $row->label() }}</option>
                                    @endforeach
                                </select>
                                @error('place')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Type Dropdown -->
                            <div class="mb-3">
                                <label for="type" class="form-label">{{ __('messages.advertisements.type') }}</label>
                                <select class="form-select @error('type') is-invalid @enderror" wire:model.live="state.type"
                                        id="type">
                                    @if(!$showEdit)
                                        <option selected>{{ __('messages.choose') }}</option>
                                    @endif
                                    @foreach(AdvertisementTypeEnum::cases() as $row)
                                        <option @selected(isset($state['type']) && $state['type'] == $row->value)
                                                value="{{ $row->value }}">{{ $row->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Time Inputs -->
                        {{--    <div class="mb-3">
                                <label class="form-label">{{ __('messages.advertisements.end_time') }}</label>
                                <div class="row g-2 align-items-center">
                                    <div class="col">
                                        <input wire:model="state.end_hour_time" type="number"
                                               class="form-control @error('end_hour_time') is-invalid @enderror"
                                               placeholder="Hour">
                                        @error('end_hour_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-auto">{{ __('messages.advertisements.hours') }}</div>
                                    <div class="col">
                                        <input wire:model="state.end_min_time" type="number"
                                               class="form-control @error('end_min_time') is-invalid @enderror"
                                               placeholder="Minutes">
                                        @error('end_min_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-auto">{{ __('messages.advertisements.minutes') }}</div>
                                </div>
                            </div>--}}

                            <!-- Dynamic Fields Depending on Type -->
                            @if(isset($state['type']) && $state['type'] == AdvertisementTypeEnum::CODE->value)
                                <!-- Code Textarea -->
                                <div class="mb-3">
                                    <label for="code" class="form-label">{{ __('messages.advertisements.code') }}</label>
                                    <textarea wire:model="state.code" rows="5"
                                              class="form-control @error('code') is-invalid @enderror" id="code"
                                              placeholder="Insert advertisement code here..."></textarea>
                                    @error('code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <!-- URL Field -->
                                <div class="mb-3">
                                    <label for="url" class="form-label">{{ __('messages.advertisements.url') }}</label>
                                    <input wire:model="state.url" type="url"
                                           class="form-control @error('url') is-invalid @enderror" id="url"
                                           placeholder="https://example.com">
                                    @error('url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- URL Target Dropdown -->
                                <div class="mb-3">
                                    <label for="url_target" class="form-label">{{ __('messages.advertisements.url_target') }}</label>
                                    <select class="form-select @error('url_target') is-invalid @enderror"
                                            wire:model="state.url_target" id="url_target">
                                        @if(!$showEdit)
                                            <option selected>{{ __('messages.choose') }}</option>
                                        @endif
                                        @foreach(AdvertisementUrlTargetEnum::cases() as $row)
                                            <option @selected(isset($state['url_target']) && $state['url_target'] == $row->value)
                                                    value="{{ $row->value }}">{{ $row->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('url_target')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Advertisement Image Upload Preview -->
                                <div class="mb-4">
                                    <label for="image" class="form-label d-block mb-2">
                                        {{ __('messages.advertisements.image') }}
                                    </label>

                                    <div class="position-relative mx-auto text-center" style="max-width: 260px;">
                                        <img
                                            src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/600x400/dddddd/000000' }}"
                                            alt="Ad Image Preview"
                                            id="imagePreview"
                                            class="img-fluid rounded border shadow-sm"
                                            style="cursor: pointer;"
                                            onclick="Livewire.dispatch('isMultiple', { active: false }); Livewire.dispatch('typeDefined', { type: 'images' }); $('#fileLibraryModal').modal('show')"
                                        >

                                        <!-- Remove Image Button -->
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light position-absolute top-0 start-100 translate-middle p-1 rounded-circle shadow-sm"
                                            style="z-index: 5;"
                                            aria-label="Remove"
                                            wire:click.prevent="clearColumn('image')"
                                        >
                                            <i class="bi bi-x-lg"></i>
                                        </button>

                                        @error('image')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            @endif

                            <!-- Submit Button -->
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    {{ __('messages.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End of Card -->

                </div>
            </div>
        </form>
    </div>
</div>
