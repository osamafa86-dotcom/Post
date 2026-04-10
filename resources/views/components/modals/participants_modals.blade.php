@php use App\Enums\ParticipantTypeEnum; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="addform" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.participants.edit_participant') : __('messages.participants.add_participant')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'update' : 'create'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="name">{{ __('messages.participants.name') }}</label>
                            <input wire:model="state.name" type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   placeholder="{{ __('messages.participants.name') }}">
                            @error('name')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="type">{{ __('messages.participants.type') }}</label>
                            <select class="form-control @error('type') is-invalid @enderror"
                                    wire:model.live="state.type"
                                    id="type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(ParticipantTypeEnum::available() as $row)
                                    <option value="{{$row->value}}">{{$row->label()}}</option>
                                @endforeach
                            </select>
                            @error('type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="author_image">{{ __('messages.participants.image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <!-- الصورة -->
                                <img
                                    src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.participant_image') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"  wire:click.prevent="clearColumn('image')"></button>
                            </div>
                            @error('image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        @if(!in_array($state['type'] ?? '', [
        \App\Enums\ParticipantTypeEnum::PUBLISHERS->value,
        \App\Enums\ParticipantTypeEnum::RESOURCES->value
    ]))
                            <div class="form-group col-md-12 mb-3">
                                <label for="work">{{ __('messages.participants.work') }}</label>
                                <input wire:model="state.work" type="text"
                                       class="form-control @error('work') is-invalid @enderror"
                                       id="work"
                                       placeholder="{{ __('messages.participants.work') }}">
                                @error('work')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">

                                <label for="description">{{ __('messages.participants.description') }}</label>
                                <input wire:model="state.description" type="text"
                                       class="form-control @error('description') is-invalid @enderror"
                                       id="description"
                                       placeholder="{{ __('messages.participants.description') }}">
                                @error('description')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                            <div class="form-group col-md-12 mb-3">
                                <label for="url">{{ __('messages.participants.url') }}</label>
                                <input wire:model="state.url"
                                       type="url"
                                       id="url"
                                       class="form-control @error('url') is-invalid @enderror"
                                       placeholder="{{ __('messages.participants.url') }}">
                                @error('url')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="form-group col-md-12 my-3">
                            <a class="btn btn-sm btn-success" wire:click="addSocialMedia">
                                <span>{{__('messages.participants.add_social_media')}}</span>
                                <i class="bi bi-plus-circle fs-6"></i>
                            </a>
                        </div>
                        @foreach($social_media as $key => $row)
                            <h3 class="mb-2 text-primary">{{ __('messages.participants.social_media').'('.$key+1 .')' }}</h3>
                            <div class="form-group col-md-6 mb-3"
                                 wire:key="social_media.{{$key}}.social_media_name">
                                <label for="social_media.{{$key}}.social_media_name">{{__('messages.participants.social_media_name')}} </label>
                                <input wire:model="social_media.{{$key}}.social_media_name" type="text"
                                       class="form-control @error("$key.social_media_name") is-invalid @enderror"
                                       id="social_media.{{$key}}.social_media_name"
                                       placeholder="{{__('messages.participants.social_media_name')}} ">
                                @error("$key.social_media_name")
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-3"
                                 wire:key="social_media.{{$key}}.social_media_link">
                                <label for="social_media.{{$key}}.social_media_link"> {{__('messages.participants.social_media_link')}}</label>
                                <input wire:model="social_media.{{$key}}.social_media_link" type="url"
                                       class="form-control @error("$key.social_media_link") is-invalid @enderror"
                                       id="social_media.{{$key}}.social_media_link">
                                @error("$key.social_media_link")
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="false"
                                                aria-controls="collapseOne">
                                            # {{__('messages.participants.social_media_icon')}}
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse my-3"
                                         aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @if($this->icons)
                                                    @foreach($this->icons as $row)
                                                        <div
                                                            class="col-md-3 text-center gap-2 mb-3 @if(!empty($social_media[$key]['social_media_icon']) && $social_media[$key]['social_media_icon'] == $row->id) border border-primary rounded @endif"
                                                            style="cursor: pointer;padding: 5px;"
                                                            wire:click="iconSelect({{$key}} ,{{$row}})">
                                                            <img width="50"
                                                                 src="{{file_url($row->icon_path)}}"
                                                                 alt="icon">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error("$key.social_media_icon")
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6" style="margin-top: 25px;">
                                <a class="btn btn-sm btn-danger" wire:click="removeSocialMedia('{{$key}}')">
                                    <span>{{__('messages.remove')}}</span>
                                    <i class="bi bi-dash-circle fs-6"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{ __('messages.confirm') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="delete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.participants.delete_participant') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.participants.confirm_delete_participant') }}</h4>
                <h6>{{ __('messages.participants.delete_participant_message')}}: {{ $Participant_->name ?? __('messages.no_data') }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
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
                    id="staticBackdropLabel">{{__('messages.participants.delete_participants')}}   </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.participants.confirm_delete_participants')}}</h4>
                <h6>{{__('messages.participants.delete_participants_message')}}</h6>
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
