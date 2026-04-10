<!--Create/Edit Modal -->
<div class="modal fade" id="roleForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{ $showEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ $showEdit ? __('messages.notification_roles.edit_role') : __('messages.notification_roles.add_role') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $showEdit ? 'updateRole' : 'createRole' }}">
                    <div class="row">

                        <div class="form-group col-md-12 mb-3">
                            <label for="model">{{ __('messages.notification_roles.role_model') }}</label>
                            <select class="form-control @error('model') is-invalid @enderror" wire:model="state.model"
                                id="model">
                                <option> {{ __('messages.choose') }}</option>
                                @foreach ($models as $key => $model)
                                    <option value="{{ $key }}">{{ __($model) }}</option>
                                @endforeach
                            </select>
                            @error('model')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="action">{{ __('messages.notification_roles.role_action') }}</label>
                            <select class="form-control @error('action') is-invalid @enderror" wire:model="state.action"
                                id="action">
                                <option>{{ __('messages.choose') }}</option>
                                <option value="created">{{ __('messages.add') }}</option>
                                <option value="edited">{{ __('messages.edit') }}</option>
                                <option value="deleted">{{ __('messages.delete') }}</option>
                            </select>
                            @error('action')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col-md-12 mb-3">
                        <label for="permission_target_id">{{ __('messages.notification_roles.role_target') }}</label>
                        <select class="form-control @error('permission_target_id') is-invalid @enderror"
                            wire:model="state.permission_target_id" id="permission_target_id">
                            <option>{{ __('messages.choose') }}</option>
                            @foreach ($this->targets as $id => $item)
                                <option value="{{ $id }}">
                                    {{ $item ? __('messages.roles.' . $item) : __('messages.not_available') }}
                                </option>
                            @endforeach
                        </select>
                        @error('permission_target_id')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit"
                            class="btn {{ $showEdit ? 'btn-warning' : 'btn-primary' }}">{{ __('messages.confirm') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="roleDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ __('messages.notification_roles.delete_role') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.notification_roles.confirm_delete_role') }}</h4>
                <h6>{{ __('messages.notification_roles.delete_role_message') }} :
                    {{ $Categories_->role_title ?? __('messages.not_available') }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="roleDeleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>
