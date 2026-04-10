<!--Create/Edit Modal -->
<div class="modal fade" id="special_fileForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __("messages.special_files.edit_special_file") : __("messages.special_files.add_special_file")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateSpecialFile' : 'createSpecialFile'}}">
                    <div class="form-group col-md-12 mb-3">
                        <label for="image" class="d-block">{{__('messages.special_files.special_file_image')}}</label>
                        <div class="position-relative"
                             style="display: inline-block; width: 100%; max-width: 200px;">
                            <!-- الصورة -->
                            <img
                                src="{{ isset($state['file_image_name']) ? file_url($state['file_image_name']) : 'https://dummyimage.com/' . config('features.image_sizes.file_image') . '/dddddd/000000' }}"
                                alt="{{__('messages.special_files.special_file_image')}}"
                                id="imagePreview" class="img-fluid border"
                                style="cursor: pointer; border-radius: 8px;"
                                onclick="$('#fileLibraryModal').modal('show')">

                            <!-- زر الإغلاق أعلى الصورة -->
                            <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                    aria-label="Close"
                                    style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                        </div>
                        @error('file_image')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-12 mb-3">
                        <label for="file_name">{{__('messages.special_files.special_file_title')}}</label>
                        <input wire:model="state.file_name" type="text"
                               class="form-control @error('file_name') is-invalid @enderror"
                               id="file_name"
                               placeholder="{{__('messages.special_files.special_file_title')}}">
                        @error('file_name')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-12 mb-3">
                        <label for="file_description">{{__('messages.special_files.special_file_description')}}</label>
                        <textarea wire:model="state.file_description" type="text"
                               class="form-control @error('file_description') is-invalid @enderror"
                               id="file_description"
                                  placeholder="{{__('messages.special_files.special_file_description')}}"></textarea>
                        @error('file_description')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
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
<div class="modal fade" id="special_fileDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.special_files.delete_special_file')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.special_files.confirm_delete_special_file')}}</h4>
                <h6>{{__('messages.special_files.delete_special_file_message', ['fileName' => $SpecialFiles_->file_name ?? __('messages.no_data')])}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="special_fileDeleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>
