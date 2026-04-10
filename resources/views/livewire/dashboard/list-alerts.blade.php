@php use App\Enums\AlertTypeEnum; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.alerts.alerts') }}
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
                    {{ __('messages.alerts.alerts') }}
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.alerts.alerts') }}</li>
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
                                       placeholder="{{ __('messages.alerts.search_alert') }}"/>
                            </div>

                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('id')">{{ __('messages.alerts.alert_id') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('title')">{{ __('messages.alerts.title') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('type')">{{ __('messages.alerts.status') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('content')">{{ __('messages.alerts.data') }}
                            </th>
                            @can('alerts_delete')
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->alerts as $key => $alert)
                            <tr>
                                <td class="text-start ">
                                    {{$alert->id}}
                                </td>
                                <td class="text-start ">
                                    {{$alert->title ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    @php
                                        $status = $alert?->type > 0 && $alert?->type < 2 ? \Illuminate\Support\Str::lower(AlertTypeEnum::from($alert->type)->status()) : __('messages.no_data');
                                    @endphp
                                    @if($status)
                                        <span class="badge badge-{{$status}}
                                     fs-base">
                                    {{ $alert->type ? AlertTypeEnum::from($alert->type)->label() : __('messages.no_data') }}
                                    </span>
                                    @endif
                                </td>
                                <td class="text-start ">
                                    @foreach(json_decode($alert->content) as $key_ => $data)
                                        <span class="text-danger">{{$key_.': '}}</span>
                                        @if($key_ == "email")
                                            <a href="{{route('dashboard.users', $data)}}">{{$data}}</a>
                                        @else
                                            <span>{{$data}}</span>
                                            <br>
                                        @endif
                                    @endforeach
                                </td>
                                @can('alerts_delete')
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                <li><a class="dropdown-item text-success"
                                                       wire:click="alertsStatus({{$alert}})">
                                                        {{ __('messages.safe') }}
                                                        <i class="bi bi-check-circle text-success"></i>
                                                    </a>
                                                </li>
                                                <li><a class="dropdown-item text-danger"
                                                       wire:click="alertsDelete({{$alert}})">
                                                        {{ __('messages.delete') }}
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{$this->alerts->links()}}
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
    
        @include('components.modals.alerts_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_alerts_status', event => {
            $('#alertsStatus').modal('show');
        })
        window.addEventListener('hide_alerts_status', event => {
            $('#alertsStatus').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_alerts_status', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.alert_safe') }}',
                        icon: 'success',
                        confirmButtonText: '{{ __('messages.alert_message.done') }}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/dashboard/alerts';
                    }
                });

            });
        });

        window.addEventListener('show_alerts_delete', event => {
            $('#alertsDelete').modal('show');
        })
        window.addEventListener('hide_alerts_delete', event => {
            $('#alertsDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_alerts_delete', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.alert_deleted') }}',
                        icon: 'success',
                        confirmButtonText: '{{ __('messages.alert_message.done') }}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );

            });
        });
    </script>
@endsection
