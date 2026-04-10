<!--Create/Edit Modal -->
@php use App\Enums\DataPageEnum; @endphp
<div class="modal fade" id="addform" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{ $showEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ $showEdit ? __('messages.data_page.edit_data_page') : __('messages.data_page.add_data_page') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $showEdit ? 'update' : 'create' }}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="author_image">{{ __('messages.albums.image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <!-- الصورة -->
                                <img
                                    src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.material_album.main_image') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.albums.image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close" wire:click.prevent="clearColumn('image')"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                            </div>
                            @error('image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="name">{{ __('messages.data_page.name') }}</label>
                            <input wire:model="state.name" type="text"
                                   class="form-control @error('name') is-invalid @enderror" id="name">
                            @error('name')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="description">{{ __('messages.data_page.description') }}</label>
                            <input wire:model="state.description" type="text"
                                   class="form-control @error('description') is-invalid @enderror" id="description">
                            @error('description')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="type">{{ __('messages.data_page.type') }}</label>
                            <select class="form-control @error('type') is-invalid @enderror"
                                    wire:model.live="state.type" id="type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach (DataPageEnum::cases() as $row)
                                    @if($row !== DataPageEnum::SCHOLARSHIP || config('features.general.scholarships'))
                                        <option value="{{ $row->value }}">{{ $row->label() }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        @if (!empty($state['type']) && $state['type'] == DataPageEnum::FILE->value)
                            <div class="form-group col-md-12 mb-3">
                                <label for="author_image">{{ __('messages.data_page.image') }}</label>
                                <br>
                                <div class="position-relative mb-3"
                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                    <!-- الصورة -->
                                    <img
                                        src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.data_page.author_image') . '/dddddd/000000' }}"
                                        alt="{{ __('messages.data_page.image') }}" id="imagePreview"
                                        class="img-fluid border" style="cursor: pointer; border-radius: 8px;"
                                        onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                    <!-- زر الإغلاق أعلى الصورة -->
                                    <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                            aria-label="Close"
                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"
                                            wire:click.prevent="clearColumn('image')"></button>
                                </div>
                                @error('image')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="file">{{ __('messages.data_page.file') }}</label>
                                <input wire:model="state.file" type="file"
                                       class="form-control @error('file') is-invalid @enderror" id="file">
                                @error('file')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        @elseif(!empty($state['type']) && $state['type'] == DataPageEnum::CODE->value)
                            <div class="form-group col-md-12 mb-3">
                                <label for="code">{{ __('messages.data_page.code') }}</label>
                                <textarea wire:model="state.code" type="text"
                                          class="form-control @error('code') is-invalid @enderror"
                                          id="code"></textarea>
                                @error('code')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        @elseif(!empty($state['type']) && $state['type'] == DataPageEnum::OPPORTUNITY->value)
                            <div class="form-group col-md-12 mb-3">
                                <label for="opportunity">{{ __('messages.data_page.opportunity') }}</label>
                                <div wire:ignore>
                                    <textarea id="opportunityEditor"
                                              class="form-control @error('opportunity') is-invalid @enderror"
                                              wire:loading.class="opacity-50" wire:loading.attr="disabled"></textarea>
                                </div>
                                @error('opportunity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @elseif(!empty($state['type']) && $state['type'] == DataPageEnum::SCHOLARSHIP->value)
                            <div class="form-group col-md-12 mb-3">
                                <label for="scholarship">{{ __('messages.data_page.scholarship') }}</label>
                                <div wire:ignore>
                                    <textarea wire:model="state.code" type="text"
                                              class="form-control @error('scholarship') is-invalid @enderror"
                                              id="code"></textarea>
                                </div>
                                @error('scholarship')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit"
                                class="btn {{ $showEdit ? 'btn-warning' : 'btn-primary' }}">{{ __('messages.confirm') }}</button>
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ __('messages.data_page.delete_data_page') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.data_page.confirm_delete_data_page') }}</h4>
                <h6>{{ __('messages.data_page.delete_data_page_message') }} </h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>
