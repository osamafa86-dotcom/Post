@php use App\Enums\TagTypeEnum; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="addForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.services.edit_service') : __('messages.services.add_service')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'update' : 'create'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="author_image">{{ __('messages.services.image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <!-- الصورة -->
                                <img
                                    src="{{ isset($state['service_image_name']) ? file_url($state['service_image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.service_image') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.services.image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"  wire:click.prevent="clearColumn('service_image')"></button>
                            </div>
                            @error('service_image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="title">{{__('messages.services.title')}}</label>
                            <input wire:model="state.title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   placeholder="{{__('messages.services.title')}}">
                            @error('title')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="description">{{__('messages.services.description')}}</label>
                            <input wire:model="state.description" type="text"
                                   class="form-control @error('description') is-invalid @enderror"
                                   id="description"
                                   placeholder="{{__('messages.services.description')}}">
                            @error('description')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="url">{{__('messages.services.url')}}</label>
                            <input wire:model="state.url" type="text"
                                   class="form-control @error('url') is-invalid @enderror"
                                   id="url"
                                   placeholder="{{__('messages.services.url')}}">
                            @error('url')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="button_title">{{__('messages.services.button_title')}}</label>
                            <input wire:model="state.button_title" type="text"
                                   class="form-control @error('button_title') is-invalid @enderror"
                                   id="button_title"
                                   placeholder="{{__('messages.services.button_title')}}">
                            @error('button_title')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group ">
                            <label for="is_show">{{__('messages.services.is_show')}}</label>
                            <input  wire:model="state.is_show"  class="form-check-input" type="checkbox"
                                    id="is_show">
                            @error('is_show')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button type="submit" class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{__('messages.confirm')}}</button>
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.services.delete_service')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.services.confirm_delete_service')}}</h4>
                <h6>{{__('messages.services.delete_service_message')}} : {{$Service_->title ?? __('messages.no_data')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>

