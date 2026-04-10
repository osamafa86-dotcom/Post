@php use App\Enums\TagTypeEnum; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="addForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.subscriptions.edit') : __('messages.subscriptions.add')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form wire:submit.prevent="{{$showEdit ? 'update' : 'create'}}">
                    <div class="row">
                        <label for="subscriber_id" class="mt-3">{{ __('messages.subscriptions.subscriber') }}</label>
                        <div class="d-flex align-items-center" wire:ignore>
                            <select class="form-select" wire:model.live="state.subscriber_id"
                                    id="subscriber_id"
                                    data-control="select2"
                                    data-placeholder="{{ __('messages.choose') }}">
                                <option value="">{{ __('messages.choose') }}</option> <!-- Placeholder -->
                                @foreach($this->subscribers as $file)
                                    <option value="{{ $file['id'] }}">{{ $file['first_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('subscriber_id')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror

                        <div class="form-group col-md-12 mb-3">
                            <label for="subscription_start">{{__('messages.subscriptions.subscription_start')}}</label>
                            <input wire:model="state.subscription_start" type="date"
                                   class="form-control @error('subscription_start') is-invalid @enderror"
                                   id="subscription_start"
                                   placeholder="{{__('messages.subscriptions.subscription_start')}}">
                            @error('subscription_start')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="subscription_end">{{__('messages.subscriptions.subscription_end')}}</label>
                            <input wire:model="state.subscription_end" type="date"
                                   class="form-control @error('subscription_end') is-invalid @enderror"
                                   id="subscription_end"
                                   placeholder="{{__('messages.subscriptions.subscription_end')}}">
                            @error('subscription_end')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="type">{{ __('messages.subscriptions.type') }}</label>
                            <select class="form-control @error('type') is-invalid @enderror"
                                    wire:model="state.type"
                                    id="type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(App\Enums\SubscriptionTypeEnum::cases() as $row)
                                    <option value="{{$row->value}}">{{$row->label()}}</option>
                                @endforeach
                            </select>
                            @error('type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="status">{{ __('messages.subscriptions.status') }}</label>
                            <select class="form-control @error('status') is-invalid @enderror"
                                    wire:model="state.status"
                                    id="status">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(App\Enums\SubscriptionStatusEnum::cases() as $row)
                                    <option value="{{$row->value}}">{{\App\Enums\SubscriptionStatusEnum::fromValue($row->value)}}</option>
                                @endforeach
                            </select>
                            @error('status')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="payment_method">{{ __('messages.subscriptions.payment_method') }}</label>
                            <select class="form-control @error('payment_method') is-invalid @enderror"
                                    wire:model="state.payment_method"
                                    id="payment_method">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(App\Enums\SubscriptionPaymentMethodEnum::cases() as $row)
                                    <option value="{{$row->value}}">{{\App\Enums\SubscriptionPaymentMethodEnum::fromValue($row->value)}}</option>
                                @endforeach
                            </select>
                            @error('payment_method')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="amount">{{__('messages.subscriptions.amount')}}</label>
                            <input wire:model="state.amount" type="text"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   id="amount"
                                   placeholder="{{__('messages.subscriptions.amount')}}">
                            @error('amount')
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.subscriptions.delete_subscription')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.subscriptions.confirm_delete_subscription')}}</h4>
                <h6>{{__('messages.subscriptions.delete_subscription_message')}} : {{$Subscription_->title ?? __('messages.no_data')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>

