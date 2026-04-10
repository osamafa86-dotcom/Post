@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.settings.settings')}}
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
                    {{__('messages.settings.settings')}}
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

                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{__('messages.settings.settings')}}</li>
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
    
        @include('components.modals.setting_modals')
    
    <!--end::Content-->
</div>
@section('script')
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>

        document.addEventListener('livewire:init', function () {
            var input = document.querySelector('#tags');
            var tagify = new Tagify(input, {
                whitelist: @json($this->tags), // Pass the tags array to Tagify
                dropdown: {
                    maxItems: 5,           // Show 20 suggestions at most
                    classname: "tags-look", // Custom dropdown class
                    enabled: 0,             // Show suggestions immediately
                    closeOnSelect: false    // Keep the dropdown open after selecting a suggestion
                }
            });

            tagify.addTags(@json($state['tags'] ?? []));

            tagify.on('add', function (e) {
            @this.set('state.tags', tagify.value.map(tag => tag.value).join(','));
            });

            tagify.on('remove', function (e) {
            @this.set('state.tags', tagify.value.map(tag => tag.value).join(','));
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('settingUpdated', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.settings_saved')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });

            });
        });
    </script>
@endsection
