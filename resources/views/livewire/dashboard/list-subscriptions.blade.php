@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.subscriptions.subscriptions') }}
@endsection
@section('style')
    <style>
        .th-btn {
            cursor: pointer !important;
        }
    </style>
@endsection
<div>
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('messages.subscriptions.subscriptions') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.main') }}"
                           class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{ __('messages.subscriptions.subscriptions') }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-fluid">
            <!--begin::Products-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1 gap-2 flex-wrap">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <i class="bi bi-search fs-3  input-group-text"></i>
                                </div>
                                <input type="text" wire:model.live="search"
                                       class="form-control form-control-solid w-250px"
                                       placeholder="{{ __('messages.subscriptions.search') }}"/>
                            </div>

                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->

                    @can('subscriptions_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">{{ __('messages.subscriptions.add') }}</a>
{{--                            <button wire:click="exportExcel" class="btn btn-success">--}}
{{--                                {{__('messages.export')}}--}}
{{--                                --}}{{--                                <i class="bi bi-cloud-arrow-up"></i>--}}
{{--                            </button>--}}

                            <button class="btn btn-success"
                                    data-bs-toggle="modal" data-bs-target="#importForm">
                                {{__('messages.import.import')}}
                                {{--                                <i class="bi bi-cloud-arrow-down"></i>--}}
                            </button>
                            <!--end::Add product-->
                        </div>
                    @endcan

                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                {{ __('messages.subscriptions.id') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.subscriptions.subscriber') }}
                            </th>
                            <th class="text-start min-w-70px th-btn">
                                {{ __('messages.subscriptions.subscription_start') }}</th>
                            <th class="text-start min-w-70px">{{ __('messages.subscriptions.subscription_end') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.subscriptions.type') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.subscriptions.status') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.subscriptions.amount') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.subscriptions.payment_method') }}
                            </th>
                            @canany(['subscriptions_edit', 'subscriptions_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach ($this->subscriptions as $key => $item)
                            <tr>
                                <td class="text-start ">
                                    {{ $item->id }}
                                </td>
                                <td class="text-start ">
                                    {{ ($item->subscriber?->first_name . ' ' . $item->subscriber?->last_name) ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $item->subscription_start ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $item->subscription_end ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $item->type ? \App\Enums\SubscriptionTypeEnum::from($item->type)->label() : __('messages.no_data') }}
                                </td>

                                <td class="text-start ">
                                    {{ $item->status ? \App\Enums\SubscriptionStatusEnum::from($item->status)->label() : __('messages.no_data')}}
                                </td>
                                <td class="text-start ">
                                    {{ $item->amount ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{$item?->payment_method ?  \App\Enums\SubscriptionPaymentMethodEnum::from($item->payment_method)->label() : __('messages.no_data')}}
                                </td>

                                @canany(['subscriptions_edit', 'subscriptions_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('subscriptions_edit')
                                                    <li><a class="dropdown-item text-warning"
                                                           wire:click="edit({{ $item }})">
                                                            {{ __('messages.edit') }}
                                                            <i class="bi bi-pencil text-warning"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('subscriptions_delete')
                                                    <li><a class="dropdown-item text-danger"
                                                           wire:click="delete({{ $item }})">
                                                            {{ __('messages.delete') }}
                                                            <i class="bi bi-trash text-danger"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $this->subscriptions->links() }}
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Products-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
    
        @include('components.modals.subscription_modals')
        @include('components.modals.import_modals')
    

</div>
@section('script')
    <script>
        document.addEventListener('livewire:init', function () {
            const select2Elements = [{
                selector: '#subscriber_id',
                wireModel: 'state.subscriber_id'
            },];

            function initializeSelect2(selector, selectedValue) {
                let options = {
                    placeholder: $(selector).data('placeholder'),
                    val: selectedValue || '',
                };

                // // If it's the 'type_id' selector, ensure it's single select
                // if (selector === '#type_id' || selector === '#subscriber') {
                //     options.multiple = false;
                // }

                $(selector).select2(options);
            }

            function setupChangeEvent(selector, wireModel) {
                $(selector).on('change', function (e) {
                    var data = $(this).val();
                    @this.
                    set(wireModel, data);
                });
            }

            // Initialize Select2 for all elements
            select2Elements.forEach(function (element) {
                setTimeout(function () {
                    initializeSelect2(element.selector, @this.get(element
                        .wireModel)
                )
                    ; // Pass the initial value to select2
                    setupChangeEvent(element.selector, element.wireModel);
                }, 100)
            });

            // Reinitialize Select2 after Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                select2Elements.forEach(function (element) {
                    initializeSelect2(element.selector, @this.get(element
                        .wireModel)
                )
                    ; // Update with live data
                });
            });
        });


        window.addEventListener('show_form', event => {
            $('#addForm').modal('show');
        })
        window.addEventListener('hide_form', event => {
            $('#addForm').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_form', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.subscription_saved') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });

            });
        });

        window.addEventListener('show_delete', event => {
            $('#delete').modal('show');
        })
        window.addEventListener('hide_delete', event => {
            $('#delete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_delete', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.subscription_deleted') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });

            });
        });
    </script>
@endsection
