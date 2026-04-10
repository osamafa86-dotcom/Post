@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{!$showEdit ? __('messages.advertisements.add_advertisement') : __('messages.advertisements.edit_advertisement')}}
@endsection
@section('style')@endsection
<div>
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{!$showEdit ? __('messages.advertisements.add_advertisement') : __('messages.advertisements.edit_advertisement')}}
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
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.advertisements')}}"
                           class="text-muted text-hover-primary">{{__('messages.advertisements.advertisements')}}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{!$showEdit ? __('messages.advertisements.add_advertisement') : __('messages.advertisements.edit_advertisement')}}</li>
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
    
        @include('components.modals.advertisement_modals')
    
    <!--end::Content-->
</div>
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('advertisementCreated', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.advertisement_saved')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/dashboard/advertisements';
                    }
                });

            });
        });
    </script>
@endsection
