@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.data_page.data_page') }}
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
                    {{ __('messages.data_page.data_page') }}
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.data_page.data_page') }}</li>
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
                                       placeholder="{{ __('messages.data_page.search') }}" />
                            </div>

                            @can('data_page_delete')
                                @if (!empty($selected))
                                        <button wire:click="deleteSelected" class="btn btn-danger"
                                            wire:key="{{ uniqid() }}">
                                            {{ __('messages.bulk_deleted') }} {{ count($selected) }}
                                        </button>
                                @endif
                            @endcan
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    @can('data_page_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">{{ __('messages.data_page.add_data_page') }}</a>
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
                                @can('data_page_delete')
                                    <th><input type="checkbox" wire:model.live="selectAll"></th>
                                @endcan
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                    {{ __('messages.data_page.id') }}
                                </th>

                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('name')">
                                    {{ __('messages.data_page.name') }}
                                </th>
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('type')">
                                    {{ __('messages.data_page.type') }}
                                </th>

                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('description')">
                                    {{ __('messages.data_page.description') }}
                                </th>
                                @canany(['data_page_edit', 'data_page_delete'])
                                    <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($this->datas as $key => $item)
                                <tr>
                                    <td class="text-start " wire:key="{{ $item->id }}_"
                                        id="{{ $item->id }}_">
                                        <input type="checkbox" id="{{ $item->id }}" wire:model.live="selected"
                                            value="{{ $item->id }}">
                                    </td>
                                    <td class="text-start ">
                                        {{ $item->id }}
                                    </td>

                                    <td class="text-start ">
                                        {{ $item->name ?? __('messages.no_data') }}
                                    </td>
                                    <td class="text-start ">
                                        {{ $item->type ? \App\Enums\DataPageEnum::from($item->type)->label() : __('messages.no_data') }}
                                    </td>

                                    <td class="text-start ">
                                        {{ $item->description ?? __('messages.no_data') }}
                                    </td>
                                    @canany(['data_page_edit', 'data_page_delete'])
                                        <td class="text-start ">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {{ __('messages.options') }}
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                    style="z-index: 10;">
                                                    @can('data_page_edit')
                                                        <li><a class="dropdown-item text-warning btn"
                                                                wire:click="edit({{ $item }})">
                                                                <i class="bi bi-pencil text-warning"></i>
                                                                {{ __('messages.edit') }}

                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('data_page_delete')
                                                        <li><a class="dropdown-item text-danger btn"
                                                                wire:click="delete({{ $item }})">
                                                                <i class="bi bi-trash text-danger"></i>
                                                                {{ __('messages.delete') }}

                                                            </a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $this->datas->links() }}
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
    
        @include('components.modals.data_page_modals')
    
</div>
@section('script')
    <script>
        window.addEventListener('show_delete_selected_modal', event => {
            $('#deleteSelected').modal('show');
        })
        window.addEventListener('hide_delete_selected_modal', event => {
            $('#deleteSelected').modal('hide');
        })
        window.addEventListener('show_form', async event => {
            $('#addform').modal('show');
            destroyOpportunityEditor();
            await new Promise(resolve => setTimeout(resolve, 800));
            initOpportunityEditor();
        })
        window.addEventListener('hide_form', event => {
            $('#addform').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('hide_form', function() {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.data_page_saved') }}',
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
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('hide_delete', function() {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.data_page_deleted') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });

            });
        });
    </script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        let opportunityEditorInstance = null;

        // When modal is shown
        {{--$('#addform').on('shown.bs.modal', function () {--}}
        {{--    const type = $('#type').val();--}}
        {{--    if (type === "{{ \App\Enums\DataPageEnum::OPPORTUNITY->value }}") {--}}
        {{--        initOpportunityEditor();--}}
        {{--    }--}}
        {{--});--}}

        // Also re-init on type change
        $('#type').on('change', async function () {
            if (this.value == "{{ \App\Enums\DataPageEnum::OPPORTUNITY->value }}") {
                await new Promise(resolve => setTimeout(resolve, 800));
                initOpportunityEditor();
            } else {
                destroyOpportunityEditor();
            }
        });

        function initOpportunityEditor() {
            const editorElement = document.querySelector('#opportunityEditor');

            ClassicEditor
                .create(editorElement, {
                    language: "{{ Auth::user()->default_dashboard_language === 'ar' ? 'ar' : 'en' }}",
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                            'outdent', 'indent', '|',
                            'blockQuote', 'insertTable', 'undo', 'redo'
                        ]
                    }
                })
                .then(editor => {
                    opportunityEditorInstance = editor;

                    // Load initial data from Livewire
                    editor.setData(@this.get('state.opportunity') || '');

                    // Editor -> Livewire
                    editor.model.document.on('change:data', () => {
                    @this.set('state.opportunity', editor.getData());
                    });

                    // Livewire -> Editor
                    Livewire.hook('message.processed', () => {
                        const currentData = editor.getData();
                        const livewireData = @this.get('state.opportunity') || '';
                        if (currentData !== livewireData) {
                            editor.setData(livewireData);
                        }
                    });
                })
                .catch(error => console.error('CKEditor init error:', error));
        }

        function destroyOpportunityEditor() {
            if (opportunityEditorInstance) {
                opportunityEditorInstance.destroy()
                    .then(() => { opportunityEditorInstance = null; })
                    .catch(error => console.error('CKEditor destroy error:', error));
            }
        }
    </script>

@endsection
