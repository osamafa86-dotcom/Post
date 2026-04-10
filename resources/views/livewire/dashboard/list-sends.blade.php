@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.send_news.send_news')}}
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
                    {{__('messages.send_news.send_news')}}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}"
                           class="text-muted text-hover-primary">{{__('messages.dashboard')}}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{__('messages.send_news.send_news')}}</li>
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
                                       placeholder="{{__('messages.send_news.search_send_news')}}"/>
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
                                wire:click="sortBy('id')">{{__('messages.send_news.send_news_id')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('full_name')">{{__('messages.send_news.name')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('email')">{{__('messages.send_news.email')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('subject')">{{__('messages.send_news.subject')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('message')">{{__('messages.send_news.message')}}
                            </th>
                            @can('send_news_delete')
                                <th class="text-start min-w-70px">{{__('messages.actions')}}</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->sends as $key => $sends)
                            <tr>
                                <td class="text-start ">
                                    {{$sends->id}}
                                </td>
                                <td class="text-start ">
                                    {{$sends->full_name ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$sends->email ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$sends->subject ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$sends->message ?? __('messages.not_available')}}
                                </td>
                                @can('send_news_delete')
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{__('messages.options')}}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                <li><a class="dropdown-item text-danger"
                                                       wire:click="sendsDelete({{$sends}})">
                                                        {{__('messages.delete')}}
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
                        {{$this->sends->links()}}
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
    
        @include('components.modals.sends_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_sends_delete', event => {
            $('#sendsDelete').modal('show');
        })
        window.addEventListener('hide_sends_delete', event => {
            $('#sendsDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_sends_delete', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.send_news_deleted')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );

            });
        });
    </script>
@endsection
