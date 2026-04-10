<!--Status Modal -->
<div class="modal fade" id="alertsStatus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.alerts.status_alert') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.alerts.confirm_status_alert') }} </h4>
                <h6>{{ __('messages.alerts.status_alert_message')}} {{$Alerts_->title ?? 'لا يوجد'}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="alertsStatusConfirm" class="btn btn-success">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="alertsDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.alerts.delete_alert') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.alerts.confirm_delete_alert') }} </h4>
                <h6>{{ __('messages.alerts.delete_alert_message')}} {{$Alerts_->title ?? __('messages.no_data')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="alertsDeleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>
