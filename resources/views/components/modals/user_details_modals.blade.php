@php use App\Enums\GenderEnum;use App\Enums\DayEnum; @endphp
    <!-- Create/Edit Modal -->
<div class="modal fade" id="addForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{ $showEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ $showEdit ? __('messages.user_details.edit_user_detail') : __('messages.user_details.add_user_detail') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $showEdit ? 'update' : 'create' }}">

                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="image">{{__('messages.user_details.image')}}</label>
                            <input wire:model="state.image" type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   placeholder="{{__('messages.user_details.image')}}">
                            @error('image')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="mb-3">
                        <label for="first_name" class="form-label">{{ __('messages.user_details.first_name') }}</label>
                        <input type="text" id="first_name"
                               class="form-control @error('first_name') is-invalid @enderror"
                               wire:model="state.first_name">
                        @error('first_name')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">{{ __('messages.user_details.last_name') }}</label>
                        <input type="text" id="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               wire:model="state.last_name">
                        @error('last_name')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="user_type">{{ __('messages.user_details.user_type') }}</label>
                            <select class="form-control @error('user_type') is-invalid @enderror"
                                    wire:model="state.user_type"
                                    id="user_type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(\App\Enums\UserDetailsTypeEnum::cases() as $row)
                                    <option value="{{$row->value}}">{{$row->label()}}</option>
                                @endforeach
                            </select>
                            @error('user_type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('messages.user_details.email') }}</label>
                        <input type="text" id="email" class="form-control @error('email') is-invalid @enderror"
                               wire:model="state.email">
                        @error('email')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('messages.user_details.password') }}</label>
                        <input type="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               wire:model="state.password">
                        @error('password')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="form-group col-md-12 mb-3">
                        <label for="password_confirmation">{{__('messages.users.password_confirmation')}}</label>
                        <input wire:model.live="state.password_confirmation" type="password"
                               class="form-control"
                               id="password_confirmation">
                    </div>
                    <div class="form-group col-md-12 mb-3">
                        <label for="gender">{{ __('messages.user_details.gender') }}</label>
                        <select class="form-control @error('gender') is-invalid @enderror"
                                wire:model="state.gender"
                                id="gender">
                            <option>{{ __('messages.choose') }}</option>
                            @foreach(GenderEnum::cases() as $row)
                                <option value="{{$row->value}}">{{$row->label()}}</option>
                            @endforeach
                        </select>
                        @error('gender')
                        <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="birthday" class="form-label">{{ __('messages.user_details.birthday') }}</label>
                        <input type="date" id="birthday" class="form-control @error('birthday') is-invalid @enderror"
                               wire:model="state.birthday" placeholder="{{ __('messages.user_details.birthday') }}">
                        @error('birthday')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">{{ __('messages.user_details.country') }}</label>
                        <input type="text" id="country" class="form-control @error('country') is-invalid @enderror"
                               wire:model="state.country">
                        @error('country')
                        <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <div wire:ignore>
                            <label for="tags">{{ __('messages.user_details.tags') }}</label>
                            <input class="form-control @error('tags') is-invalid @enderror"
                                   id="tags" name="tags"
                                   wire:model.live="state.tags">
                        </div>
                        @error('tags')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="mt-3">{{ __('messages.user_details.categories') }}</label>
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
                        <div wire:ignore>
                            <label for="interests">{{ __('messages.user_details.interests') }}</label>
                            <input class="form-control @error('interests') is-invalid @enderror"
                                   id="interests" name="interests"
                                   wire:model.live="state.interests">
                        </div>
                        @error('interests')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div wire:ignore>
                            <label for="languages">{{ __('messages.user_details.languages') }}</label>
                            <input class="form-control @error('languages') is-invalid @enderror"
                                   id="languages" name="languages"
                                   wire:model.live="state.languages">
                        </div>
                        @error('languages')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="file">{{__('messages.user_details.file')}}</label>
                            <input wire:model="state.file" type="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   id="file"
                                   placeholder="{{__('messages.user_details.file')}}">
                            @error('file')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description"
                               class="form-label">{{ __('messages.user_details.description') }}</label>
                        <textarea rows="5" id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  wire:model="state.description"
                                  placeholder="{{ __('messages.user_details.description') }}"></textarea>
                        @error('description')
                        <div class="text-danger">{{ $message }}</div> @enderror
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
                            <label
                                for="social_media.{{$key}}.social_media_name">{{__('messages.participants.social_media_name')}} </label>
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
                            <label
                                for="social_media.{{$key}}.social_media_link"> {{__('messages.participants.social_media_link')}}</label>
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
                                        #الأيقونة
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
                            @error('icon_id')
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

<div class="modal fade" id="usereditactive" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="deleteModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white"
                    id="deleteModalLabel">{{ __('messages.user_details.edit_pending_user') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.user_details.edit_pending_user_message') }}</p>
                @if($UserDetails_?->is_active == '0')
                <h6>{{__('messages.user_details.user_account_will_be_accepted')}} : {{$UserDetails_->user?->first_name ??
                    __('messages.no_data')}}</h6>
                @elseif($UserDetails_?->is_active == '1')
                    <h6>{{__('messages.user_details.the_user_account_will_be_cancelled')}} : {{$UserDetails_->user?->first_name ??
                    __('messages.no_data')}}</h6>
                @endif
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="userEditPendingConfirm" class="btn btn-warning">{{ __('messages.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="deleteModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"
                    id="deleteModalLabel">{{ __('messages.user_details.delete_user_detail') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.user_details.confirm_delete_user_detail') }}</p>
                <h6>{{__('messages.user_details.delete_user_detail_message')}} : {{$UserDetails_->user->first_name ??
                    __('messages.no_data')}}</h6>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
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
                    id="staticBackdropLabel">{{__('messages.user_details.delete_events')}}   </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.user_details.confirm_delete_events')}}</h4>
                <h6>{{__('messages.user_details.delete_events_message')}}</h6>
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
