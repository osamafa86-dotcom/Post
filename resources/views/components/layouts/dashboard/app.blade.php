<!DOCTYPE html>
<html
    lang="{{ config('app.locale') }}"
    @if(config('app.locale') == 'ar') dir="rtl" @else dir="ltr" @endif
>
<!--begin::Head-->
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <!-- Favicon -->
    @php
        $faviconPath = config('system.favicon') ? file_url(config('system.favicon')) : null;
        $defaultFavicon = asset('assets/dashboard/images/logo-icon.png');
    @endphp
    @if($faviconPath)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconPath }}"/>
        <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconPath }}"/>
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconPath }}"/>
        <link rel="shortcut icon" href="{{ $faviconPath }}"/>
    @elseif(file_exists(public_path('assets/dashboard/images/logo-icon.png')))
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $defaultFavicon }}"/>
        <link rel="icon" type="image/png" sizes="16x16" href="{{ $defaultFavicon }}"/>
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $defaultFavicon }}"/>
        <link rel="shortcut icon" href="{{ $defaultFavicon }}"/>
    @else
        <!-- Fallback SVG Favicon -->
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23667eea'/><text x='50' y='70' font-size='60' text-anchor='middle' fill='white' font-family='Arial, sans-serif' font-weight='bold'>{{ strtoupper(substr(config('app.name', 'C'), 0, 1)) }}</text></svg>">
    @endif

    <!-- 1) Icons / Vendors -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @if(config('app.locale')=='ar')
        <link href="{{ asset('assets/dashboard/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet"/>
        <link href="{{ asset('assets/dashboard/css/style.bundle.rtl.css') }}" rel="stylesheet"/>
    @else
        <link href="{{ asset('assets/dashboard/plugins/global/plugins.bundle.css') }}" rel="stylesheet"/>
        <link href="{{ asset('assets/dashboard/css/style.bundle.css') }}" rel="stylesheet"/>
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <link href="{{ asset('assets/dashboard/css/cropimage.css') }}" rel="stylesheet"/>

    <!-- 2) Brand (قبل الكستم عشان الكستم يكسرها لو لزم) -->
    @php $company = strtolower(trim(env('COMPANY_LUNCH', ''))); @endphp
    @if($company === 'zentra')
        <link href="{{ asset('assets/dashboard/css/brands/zentra.css') }}" rel="stylesheet"/>
        @if(config('app.locale')=='ar')
            <link href="{{ asset('assets/dashboard/css/brands/zentra.rtl.css') }}" rel="stylesheet"/>
        @endif
    @endif

    <!-- 4) Project custom (يقصّ فوق الكل) -->
    <link href="{{ asset('assets/dashboard/css/custom-style.css') }}" rel="stylesheet"/>
    @if(config('app.locale')=='ar')
        <link href="{{ asset('assets/dashboard/css/custom.rtl.css') }}" rel="stylesheet"/>
    @endif

    <!-- 5) Page-level (آخر حاجة علشان تكسر أي حاجة قبلها) -->
    @livewireStyles
    @yield('style')
    @stack('style')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

    <style>
        /* small global fixes */
        .select2-container, .tagify__dropdown { z-index: 999999 !important; }
        @media (min-width: 1200px) { .custom-xl-modal .modal-dialog { max-width: 95vw; } }
        @font-face { font-family: 'Zain'; src: url('{{ asset('assets/dashboard/fonts/Zain-Regular.ttf') }}'); }
        a { cursor: pointer !important; }
        .select2-results__option[aria-selected="true"]::after { content: none !important; }
        .select2-selection__choice__display { padding-right: 20px !important; }
        .swal2-popup .swal2-title { color: #989898 !important; }

        /* Sortable table headers */
        .th-btn {
            cursor: pointer !important;
            transition: color 0.2s ease;
        }
        .th-btn:hover {
            color: var(--bs-primary) !important;
        }
        .sort-icon {
            font-size: 0.8rem;
            opacity: 0.5;
            transition: all 0.2s ease;
        }
        .th-btn:hover .sort-icon {
            opacity: 0.8;
        }
        .sort-active .sort-icon {
            opacity: 1;
            color: var(--bs-primary);
        }
    </style>

    <script>
        // Clickjacking protection
        if (window.top !== window.self) { window.top.location.replace(window.location.href); }
    </script>
</head>

<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
      data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
      data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
      data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
<!--begin::Theme mode setup on page load-->
<script>var defaultThemeMode = "light";
    var themeMode;
    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }</script>
<!--end::Theme mode setup on page load-->
<!--begin::App-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

        <!--begin::Wrapper-->
        <div @if(request()->routeIs('login')) style="margin-top: 50px;"
             @endif class="@if(!request()->routeIs('login')) app-wrapper @endif flex-column flex-row-fluid"
             id="kt_app_wrapper">
            @if(!request()->routeIs('login'))
                @include('components.layouts.dashboard.header')
                @include('components.layouts.dashboard.sidebar')
            @endif
            <!--begin::Main-->
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <!--begin::Content wrapper-->
                <div class="d-flex flex-column flex-column-fluid">
                    {{$slot??''}}
                </div>
                <!--end::Content wrapper-->
                @if(!request()->routeIs('login'))
                    @include('components.layouts.dashboard.footer')
                @endif
            </div>
            <!--end:::Main-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->

</div>
<!--end::App-->

<!--begin::Javascript-->
<script>var hostUrl = "{{ asset('assets/dashboard/') }}";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script src="{{ asset('assets/dashboard/plugins/global/plugins.bundle.js') }}"></script>


@php $editorRouts = ['dashboard.image-editor', 'dashboard.i-editor'];@endphp
@if(in_array(request()->route()->getName(), $editorRouts))
    <script src="{{ asset('assets/dashboard/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/cropimage.js') }}"></script>
@endif


{{--<script src="{{ asset('assets/dashboard/js/script.js') }}"></script>--}}



<script src="{{ asset('assets/dashboard/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/new-script.js') }}"></script>

<script>
    window.addEventListener('show_library_form', event => {
        $('#fileLibraryModal').modal('show');
    })
    window.addEventListener('hide_library_form', event => {
        $('#fileLibraryModal').modal('hide');
    })

    window.addEventListener('show_profile', event => {
    $('#profileForm').modal('show')
    })
    window.addEventListener('hide_profile', event => {
        $('#profileForm').modal('hide');
    })
    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('hide_profile', function () {
            Swal.fire(
                {
                    title: '{{__('messages.alert_message.success')}}',
                    text: '{{__('messages.alert_message.profile_saved')}}',
                    icon: 'success',
                    confirmButtonText: '{{__('messages.alert_message.done')}}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                }
            );
        });
    });

    // const cropper = new Cropper('#imagePreview');

</script>


@yield('script')
@stack('script')
@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>

<!--end::Global Javascript Bundle-->
<!--end::Javascript-->


</body>
<!--end::Body-->
</html>
