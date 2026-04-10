@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.special_files.special_files')}}
@endsection
@section('style')
    <style>
        .th-btn {
            cursor: pointer !important;
        }

        /* Drag and drop styles */
        .sortable-ghost {
            opacity: 0.4;
            background-color: #f8f9fa;
        }

        .sortable-drag {
            opacity: 0.8;
            transform: rotate(2deg);
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .sortable-chosen {
            background-color: #f8f9fa;
        }

        .draggable-handle {
            cursor: move;
            color: #7e8299;
        }

        .draggable-handle:hover {
            color: #009ef7;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet" />
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
                    {{__('messages.special_files.special_files')}}
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
                    <li class="breadcrumb-item text-muted">{{__('messages.special_files.special_files')}}</li>
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
                                       placeholder="{{__('messages.special_files.search_special_file')}}"/>
                            </div>
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    @can('special_files_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">{{__('messages.special_files.add_special_file')}}</a>
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
                            <th class="text-start min-w-50px">{{__('messages.order')}}</th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('id')">{{__('messages.special_files.special_file_id')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('file_name')">{{__('messages.special_files.special_file_title')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('file_description')">{{__('messages.special_files.special_file_description')}}
                            </th>

                            @canany(['special_files_edit','special_files_delete'])
                                <th class="text-start min-w-70px">{{__('messages.actions')}}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600" id="specialFilesSortable">
                        @foreach($this->special_files as $key => $special_file)
                            <tr data-id="{{ $special_file->id }}" class="sortable-row">
                                <td class="text-start">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-grip-vertical draggable-handle me-2 fs-4"></i>
                                        <span class="order-badge badge badge-circle badge-light-primary">
                                            {{ $special_file->order }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-start">
                                    {{$special_file->id}}
                                </td>
                                <td class="text-start">
                                    {{$special_file->file_name ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start">
                                    {{$special_file->file_description ?? __('messages.no_data')}}
                                </td>
                                @canany(['special_files_edit','special_files_delete'])
                                    <td class="text-start">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{__('messages.options')}}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('special_files_edit')
                                                    <li><a class="dropdown-item text-warning btn"
                                                           wire:click="edit({{$special_file}})">
                                                            <i class="bi bi-pencil text-warning"></i>
                                                            {{__('messages.edit')}}
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('special_files_delete')
                                                    <li><a class="dropdown-item text-danger btn"
                                                           wire:click="special_fileDelete({{$special_file}})">
                                                            <i class="bi bi-trash text-danger"></i>
                                                            {{__('messages.delete')}}
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
                        {{$this->special_files->links()}}
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
    
        @include('components.modals.special_file_modals')
    

</div>
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        window.addEventListener('show_special_file_form', event => {
            $('#special_fileForm').modal('show');
        })
        window.addEventListener('hide_special_file_form', event => {
            $('#special_fileForm').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_special_file_form', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.special_file_saved')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );
            });
        });

        window.addEventListener('show_special_file_delete', event => {
            $('#special_fileDelete').modal('show');
        })
        window.addEventListener('hide_special_file_delete', event => {
            $('#special_fileDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_special_file_delete', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.special_file_deleted')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );
            });
        });

        // Initialize Sortable.js for drag and drop
        document.addEventListener('livewire:init', function () {
            // Wait for table to be rendered
            setTimeout(initSortable, 500);

            // Reinitialize sortable when Livewire updates the DOM
            Livewire.hook('morph.updated', (el) => {
                if (el.id === 'specialFilesSortable') {
                    setTimeout(initSortable, 100);
                }
            });
        });

        function initSortable() {
            const el = document.getElementById('specialFilesSortable');
            if (!el) return;

            // Destroy existing instance if any
            if (el.sortable) {
                el.sortable.destroy();
            }

            el.sortable = Sortable.create(el, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                chosenClass: 'sortable-chosen',
                handle: '.draggable-handle',
                onEnd: function(evt) {
                    // Get the new order
                    const rows = Array.from(el.querySelectorAll('.sortable-row'));
                    const newOrder = rows.map((row, index) => ({
                        value: parseInt(row.getAttribute('data-id')),
                        order: index + 1
                    }));

                    // Update order badges
                    rows.forEach((row, index) => {
                        const badge = row.querySelector('.order-badge');
                        if (badge) {
                            badge.textContent = index + 1;
                        }
                    });

                    // Send to Livewire component
                @this.call('reorderSpecialFiles', newOrder);

                    // Show success message
                    Swal.fire({
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.order_updated')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        }
    </script>
@endsection
