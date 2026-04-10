@php use App\Enums\TagTypeEnum; @endphp
<!--Create/Edit Modal -->
<div class="modal fade" id="tagForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.tags.edit_tag') : __('messages.tags.add_tag')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateTag' : 'createTag'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="tag_name">{{__('messages.tags.tag_title')}}</label>
                            <input wire:model="state.tag_name" type="text"
                                   class="form-control @error('tag_name') is-invalid @enderror"
                                   id="tag_name"
                                   placeholder="{{__('messages.tags.tag_title')}}">
                            @error('tag_name')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="tag_type">{{ __('messages.tags.tag_type') }}</label>
                            <select class="form-control @error('tag_type') is-invalid @enderror"
                                    wire:model="state.tag_type"
                                    id="tag_type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(TagTypeEnum::available() as $row)
                                    <option value="{{$row->value}}">{{$row->label()}}</option>
                                @endforeach
                            </select>
                            @error('tag_type')
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
<div class="modal fade" id="tagDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.tags.delete_tag')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.tags.confirm_delete_tag')}}</h4>
                <h6>{{__('messages.tags.delete_tag_message', ['tagName' => $Tags_->tag_name ?? __('messages.no_data')])}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="tagDeleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
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
                    id="staticBackdropLabel">{{__('messages.tags.delete_tags')}}   </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.tags.confirm_delete_tags')}}</h4>
                <h6>{{__('messages.tags.delete_tags_message')}}</h6>
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
