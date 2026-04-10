@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{ config('system.site_name') . ' - ' }}{{ !$showEdit ? __('messages.special_pages.add_page') : __('messages.special_pages.edit_page') }}
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
                    {{ !$showEdit ? __('messages.special_pages.add_page') : __('messages.special_pages.edit_page') }}
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
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.special_pages') }}"
                            class="text-muted text-hover-primary">{{ __('messages.special_pages.special_pages') }}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        {{ !$showEdit ? __('messages.special_pages.add_page') : __('messages.special_pages.edit_page') }}
                    </li>
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
    
        @include('components.modals.special_page_modals')
    
    <!--end::Content-->
</div>
@section('script')
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        // Get current locale from HTML lang attribute or Laravel
        const currentLocale = document.documentElement.lang || '{{ app()->getLocale() }}';

        // Map Laravel locales to CKEditor language codes
        const ckeditorLanguages = {
            'en': 'en',
            'ar': 'ar',
            'fr': 'fr',
            // Add more as needed
        };

        // Fallback to English if locale not supported by CKEditor
        const editorLanguage = ckeditorLanguages[currentLocale] || 'en';

        // Initialize CKEditor with dynamic language
        const editor = CKEDITOR.replace('ckeditor-content', {
            extraPlugins: 'iframe',
            removePlugins: 'autoupdate',
            versionCheck: false,
            language: editorLanguage,
            contentsLangDirection: currentLocale === 'ar' ? 'rtl' : 'ltr'
        });

        document.addEventListener('livewire:init', function() {
            editor.on('change', function() {
                @this.set('state.page_content', editor.getData());
            });

            // Update editor when Livewire updates content
            Livewire.hook('commit', ({
                component,
                commit,
                respond,
                succeed,
                fail
            }) => {
                succeed(() => {
                    if (component.id === @this.__instance.id && @this.state.page_content) {
                        editor.setData(@this.state.page_content);
                    }
                });
            });
        });

        // Handle form submission success
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('special_pageCreated', function() {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.special_page_saved') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/dashboard/special_pages';
                    }
                });
            });
        });
    </script>
@endsection
