@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.advertisements.advertisements') }}
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
                    {{ __('messages.advertisements.advertisements') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}" class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{ __('messages.advertisements.advertisements') }}</li>
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
                                       placeholder="{{ __('messages.advertisements.search_advertisement') }}"/>
                            </div>

                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <!--begin::Add product-->
                        <a href="{{route('dashboard.advertisements.create_update_advertisement')}}" class="btn btn-primary">{{ __('messages.advertisements.add_advertisement') }}</a>
                        <!--end::Add product-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('id')">
                                {{ __('messages.advertisements.advertisement_id') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('title')">
                                {{ __('messages.advertisements.title') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('type')">
                                {{ __('messages.advertisements.type') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('place')">
                                {{ __('messages.advertisements.place') }}
                            </th>
                            <th class="text-start min-w-70px">
                                {{ __('messages.advertisements.url') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('user_id')">
                                {{ __('messages.advertisements.admin') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('created_at')">
                                {{ __('messages.advertisements.publish_time') }}
                            </th>
{{--                            <th class="text-start min-w-70px th-btn"--}}
{{--                                wire:click="sortBy('end_data_time')">--}}
{{--                                {{ __('messages.advertisements.end_time') }}--}}
{{--                            </th>--}}
                            <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->advertisements as $key => $advertisement)
                            <tr>
                                <td class="text-start ">
                                    {{$advertisement->id ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start ">
                                    {{$advertisement->title ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start ">

                                    {{ $advertisement?->type ? \App\Enums\AdvertisementTypeEnum::from($advertisement?->type)->label() : __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $advertisement?->place ? \App\Enums\AdvertisementPlaceEnum::from($advertisement?->place)->label() : __('messages.no_data') }}
                                </td>

                                <td class="text-start ">
                                    @if(!empty($advertisement->url))
                                        <a href="{{$advertisement->url}}" target="_blank" class="btn btn-link">{{ __('messages.advertisements.view_link') }}</a>
                                        {{$advertisement->url_target ? '('.$advertisement->url_target.')' : ''}}
                                    @elseif(!empty($advertisement->code))
                                        <p>{{'<> code'}}</p>
                                    @endif
                                </td>
                                <td class="text-start ">
                                    {{$advertisement?->user?->full_name ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start ">
                                    {{Carbon::parse($advertisement->created_at)->format('H:i Y-m-d') ?? __('messages.no_data')}}
                                </td>
{{--                                <td class="text-start ">--}}
{{--                                    @if(!empty($advertisement?->end_hour_time))--}}
{{--                                        {{$advertisement?->end_hour_time .' '. __('messages.advertisements.hours') }}--}}
{{--                                    @endif--}}
{{--                                    @if(!empty($advertisement?->end_min_time) && !empty($advertisement?->end_hour_time))--}}
{{--                                        {{ __('messages.advertisements.and') }}--}}
{{--                                    @endif--}}
{{--                                    @if(!empty($advertisement?->end_min_time))--}}
{{--                                        {{$advertisement?->end_min_time .' '. __('messages.advertisements.minutes') }}--}}
{{--                                    @endif--}}
{{--                                </td>--}}
                                <td class="text-start ">

                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            {{ __('messages.options') }}
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                            style="z-index: 10;">
                                            <li><a class="dropdown-item text-warning"
                                                   href="{{route('dashboard.advertisements.create_update_advertisement',$advertisement->id)}}">
                                                    {{ __('messages.edit') }}
                                                    <i class="bi bi-pencil text-warning"></i>
                                                </a>
                                            </li>
                                            <li><a class="dropdown-item text-danger" wire:click="delete({{$advertisement}})">
                                                    {{ __('messages.delete') }}
                                                    <i class="bi bi-trash text-danger"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <!--begin::Menu-->
                                    <!--end::Menu-->
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{$this->advertisements->links()}}
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

    <!--Delete Modal -->
    <div class="modal fade" id="advertisementDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.advertisements.delete_advertisement') }}</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{ __('messages.advertisements.confirm_delete_advertisement') }}</h4>
                    <h6>{{ __('messages.advertisements.delete_advertisement_message') }}: {{$Advertisement->title ?? __('messages.no_data')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button wire:click="confirmDelete" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script>
        window.addEventListener('show_delete_modal', event => {
            $('#advertisementDelete').modal('show');
        })
        window.addEventListener('hide_delete_modal', event => {
            $('#advertisementDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_user_delete', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.advertisements_deleted') }}',
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
