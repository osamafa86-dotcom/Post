@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.contact_us.contact_us') }}
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
                    {{ __('messages.contact_us.contact_us') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.main') }}" class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{ __('messages.contact_us.contact_us') }}</li>
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
                                       placeholder="{{ __('messages.contact_us.search_contact_us') }}"/>
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
                                wire:click="sortBy('id')">{{ __('messages.contact_us.contact_us_id') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('full_name')">{{ __('messages.contact_us.name') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('email')">{{ __('messages.contact_us.email') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('subject')">{{ __('messages.contact_us.subject') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('message')">{{ __('messages.contact_us.message') }}
                            </th>
                            @can('contact_us_delete')
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->contact_us as $key => $contact_us)
                            <tr>
                                <td class="text-start ">
                                    {{$contact_us->id}}
                                </td>
                                <td class="text-start ">
                                    {{$contact_us->full_name ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$contact_us->email ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$contact_us->subject ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$contact_us->message ?? __('messages.not_available')}}
                                </td>
                                @can('contact_us_delete')
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                <li><a class="dropdown-item text-danger"
                                                       wire:click="contact_usDelete({{$contact_us}})">
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
                        {{$this->contact_us->links()}}
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
    
        @include('components.modals.contact_us_modals')
    

</div>

@section('script')
    <script>
        window.addEventListener('show_contact_us_delete', event => {
            $('#contact_usDelete').modal('show');
        })
        window.addEventListener('hide_contact_us_delete', event => {
            $('#contact_usDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_contact_us_delete', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.contact_us_deleted') }}',
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
