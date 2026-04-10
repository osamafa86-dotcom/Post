@php use App\Enums\CategoryTypeEnum; @endphp


<div class="modal fade" id="publishRoleForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header {{ $showEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ $showEdit ? __('messages.publish_role.edit_publish_role') : __('messages.publish_role.add_publish_role') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $showEdit ? 'updatePublishRole' : 'createPublishRole' }}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="publish_from">{{ __('messages.publish_role.from') }}</label>
                            <select class="form-select form-select-solid @error('fromId') is-invalid @enderror"
                                wire:model="state.fromId">
                                <option value="#" selected>{{ __('messages.choose') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name ? __('messages.roles.' . $role->name) : __('messages.not_available') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fromId')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="publish_to">{{ __('messages.publish_role.to') }}</label>

                            <select class="form-select form-select-solid @error('toId') is-invalid @enderror"
                                wire:model="state.toId">
                                <option value="#" selected>{{ __('messages.choose') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name ? __('messages.roles.' . $role->name) : __('messages.not_available') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('toId')
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


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePublishRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="deletePublishRoleModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="deletePublishRoleModalLabel">
                    {{ __('messages.publish_role.delete_publish_role') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.publish_role.confirm_delete_publish_role') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    wire:click="publishRoleDeleteConfirm">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>
</div>
