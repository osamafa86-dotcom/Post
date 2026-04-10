@php use App\Enums\TagTypeEnum; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="addForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.courses.edit_course') : __('messages.courses.add_course')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'update' : 'create'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="title">{{__('messages.courses.title')}}</label>
                            <input wire:model="state.title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   placeholder="{{__('messages.courses.title')}}">
                            @error('title')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="url">{{__('messages.courses.url')}}</label>
                            <input wire:model="state.url" type="text"
                                   class="form-control @error('url') is-invalid @enderror"
                                   id="url"
                                   placeholder="{{__('messages.courses.url')}}">
                            @error('url')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group ">
                            <label for="is_active">{{__('messages.courses.is_active')}}</label>
                            <input  wire:model="state.is_active"  class="form-check-input" type="checkbox"

                                   id="is_active"
                                 >
                            @error('is_active')
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.courses.delete_course')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.courses.confirm_delete_course')}}</h4>
                <h6>{{__('messages.courses.delete_course_message')}} : {{$Course_->title ?? __('messages.no_data')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>

