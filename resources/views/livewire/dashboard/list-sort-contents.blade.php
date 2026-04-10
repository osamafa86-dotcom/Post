@php use Illuminate\Support\Carbon;use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.content_ordering.content_ordering')}}
@endsection
@section('style')
    <style>
        .th-btn {
            cursor: pointer !important;
        }

        /* Hide arrows in Chrome, Safari, Edge, and Opera */
        .no-arrows::-webkit-outer-spin-button,
        .no-arrows::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hide arrows in Firefox */
        .no-arrows {
            -moz-appearance: textfield;
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
                    {{__('messages.content_ordering.content_ordering')}}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}" class="text-muted text-hover-primary">{{__('messages.dashboard')}}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{__('messages.content_ordering.content_ordering')}}</li>
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
                                       placeholder="{{__('messages.content_ordering.search_content_placeholder')}}"/>
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
                            <th class="text-start min-w-70px">#</th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('order_number')">{{__('messages.content_ordering.ordering')}}</th>
                            <th class="text-start min-w-70px">{{__('messages.content_ordering.name')}}</th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('sortable_type')">{{__('messages.content_ordering.type')}}</th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('created_at')">{{__('messages.content_ordering.publish_date')}}</th>
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600" wire:sortable="updateUserOrder">
                        @foreach($this->sort_data as $sort)
                            <tr wire:sortable.item="{{$sort['id']}}" wire:key="sort-{{$sort['id']}}">
                                <td wire:sortable.handle style="cursor: move; width:10px;"><i
                                        class="bi bi-arrows-move text-muted"></i></td>
                                <td class="text-start ">
                                    <div>
                                        <input type="number"
                                               class="form-control d-inline border-0 p-1 text-center no-arrows"
                                               wire:model.live="state.{{$sort['id']}}"
                                               id="{{$sort['id']}}"
                                               wire:key="{{$sort['id']}}"
                                               max="100" min="1" style="width: 40px;">
                                    </div>
                                </td>
                                <td class="text-start ">
                                    {{ $sort['display_text'] ?? __('messages.not_available') }}
                                </td>
                                <td class="text-start ">
                                    {{ __($sort['type']) }}
                                </td>
                                <td class="text-start " style="direction: ltr">
                                    {{ $sort['created_at'] }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Products-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('navbar_links_updated', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.data_saved_successfully')}}',
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
</div>
