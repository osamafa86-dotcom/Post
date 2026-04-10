<!--Create/Edit Modal -->
<div class="modal fade" id="iconForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{ $showEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ $showEdit ? __('messages.icons.edit_icon') : __('messages.icons.add_icon') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $showEdit ? 'updateIcon' : 'createIcon' }}">
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="icon_path">{{ __('messages.icons.icon') }}</label>
                            <input wire:model="state.icon_path" type="file"
                                class="form-control @error('icon_path') is-invalid @enderror" id="icon_path"
                                placeholder="{{ __('messages.icons.icon') }}">
                            @error('icon_path')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
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
<div class="modal fade" id="iconDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.icons.delete_icon') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.icons.confirm_delete_icon') }}</h4>
                <h6>{{ __('messages.icons.delete_icon_message') }} </h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="iconDeleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.icons.delete_icons') }}
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.icons.confirm_delete_icons') }}</h4>
                <h6>{{ __('messages.icons.delete_icons_message') }}</h6>
                <div class="form-group col-md-12 mb-3">
                    <input wire:model="delete_text" class="form-control" id="category_description">
                </div>
                <!-- Error Message -->
                @if (session()->has('error'))
                    <div style="color: red; padding: 10px;">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="confirmDeleteSelected"
                        class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
