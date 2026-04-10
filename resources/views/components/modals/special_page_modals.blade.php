<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-fluid">
        <form wire:submit.prevent="{{$showEdit ? 'updateSpecialPage' : 'createSpecialPage'}}">
            <div class="row">
                <div class="col-md-9 mx-auto">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="page_title">{{ __('messages.special_pages.title') }}</label>
                            <input wire:model="state.page_title" type="text"
                                   class="form-control @error('page_title') is-invalid @enderror"
                                   id="page_title">
                            @error('page_title')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3" wire:ignore>
                            <label for="page_content">{{ __('messages.special_pages.details') }}</label>
                            <textarea id="ckeditor-content"
                                      class="form-control  @error('page_content') is-invalid @enderror"
                                      wire:model="state.page_content"></textarea>
                        </div>
                        @error('page_content')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                        <div class="form-group col-md-12 mb-3">
                            <label for="page_image">{{ __('messages.special_pages.image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <img
                                    src="{{ isset($state['page_image_name']) ? file_url($state['page_image_name']) : 'https://dummyimage.com/600x400/dddddd/000000' }}"
                                    alt="{{ __('messages.special_pages.image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close" wire:click.prevent="clearColumn('page_image')"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                @error('page_image')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @can($showEdit ? 'special_pages_edit' : 'special_pages_create')
                        <div class="my-5">
                            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                        </div>
                    @endcan

                </div>
            </div>
        </form>
    </div>
    <!--end::Content container-->
</div>
