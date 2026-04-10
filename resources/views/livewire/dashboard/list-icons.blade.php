@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.icons.icons') }}
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
                    {{ __('messages.icons.icons') }}
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.icons.icons') }}</li>
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

                    @can('icons_delete')
                        @if (!empty($selected))
                            <div class="ms-2">
                                <button wire:click="deleteSelected" class="btn btn-danger" wire:key="{{ uniqid() }}">
                                    {{ __('messages.bulk_deleted') }} {{ count($selected) }}
                                </button>
                            </div>
                        @endif
                    @endcan
                    @can('icons_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">{{ __('messages.icons.add_icon') }}</a>
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
                            @can('icons_delete')
                                <th><input type="checkbox" wire:model.live="selectAll"></th>
                            @endcan
                            <th class="text-start min-w-70px">{{ __('messages.icons.icon') }}
                            </th>
                            @canany(['icons_edit', 'icons_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach ($this->icons as $key => $icon)
                            <tr>
                                <td class="text-start " wire:key="{{ $icon->id }}_"
                                    id="{{ $icon->id }}_">
                                    <input type="checkbox" id="{{ $icon->id }}" wire:model.live="selected"
                                           value="{{ $icon->id }}">
                                </td>
                                <td class="text-start ">
                                    <div class="icon-tile" title="ID: {{ $icon->id }}">
                                        <img class="icon-img"
                                             src="{{ file_url($icon->icon_path) }}" alt="icon">
                                    </div>
                                </td>
                                @canany(['icons_edit', 'icons_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('icons_edit')
                                                    <li><a class="dropdown-item text-warning btn"
                                                           wire:click="edit({{ $icon }})">
                                                            <i class="bi bi-pencil text-warning"></i>
                                                            {{ __('messages.edit') }}

                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('icons_delete')
                                                    <li><a class="dropdown-item text-danger btn"
                                                           wire:click="iconDelete({{ $icon }})">
                                                            <i class="bi bi-trash text-danger"></i>
                                                            {{ __('messages.delete') }}

                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $this->icons->links() }}
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
    
        @include('components.modals.icon_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_delete_selected_modal', event => {
            $('#deleteSelected').modal('show');
        })
        window.addEventListener('hide_delete_selected_modal', event => {
            $('#deleteSelected').modal('hide');
        })
        window.addEventListener('show_icon_form', event => {
            $('#iconForm').modal('show');
        })
        window.addEventListener('hide_icon_form', event => {
            $('#iconForm').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_icon_form', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.icon_saved') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });

            });
        });

        window.addEventListener('show_icon_delete', event => {
            $('#iconDelete').modal('show');
        })
        window.addEventListener('hide_icon_delete', event => {
            $('#iconDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_icon_delete', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.icon_deleted') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });

            });
        });
    </script>
    <script>
        document.addEventListener('livewire:init', function () {
            const positionSelect = new Choices('#position', {
                searchEnabled: false,
                itemSelectText: '',
                shouldSort: false,
            });

            Livewire.hook('commit', ({
                                         component,
                                         commit,
                                         respond,
                                         succeed,
                                         fail
                                     }) => {
                succeed(() => {
                    if (component.id === @this.__instance.id) {
                        positionSelect.setValue(@this.state.position);
                    }
                });
            });
        });
    </script>
@endsection
