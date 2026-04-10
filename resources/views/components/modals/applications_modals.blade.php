<!--Delete Modal -->
<div class="modal fade" id="delete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.applications.delete_application')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.applications.confirm_delete_application')}}</h4>
                <h6>{{__('messages.applications.delete_application_message')}} : {{$Application_->name ?? __('messages.no_data')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!--Change Status Modal -->
<div class="modal fade" id="changeStatus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="staticBackdropLabel">{{__('messages.applications.change_status')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{  $Application_?->is_accepted == 1  ? __('messages.applications.confirm_change_not_accepted') : __('messages.applications.confirm_change_accepted') }}</h4>
                <h6>{{ $Application_?->is_accepted == 1  ? __('messages.applications.change_not_accepted_message') : __('messages.applications.change_accepted_message') }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="confirmChangeStatus" data-bs-dismiss="modal"
                            class="btn btn-primary">{{__('messages.confirm')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
