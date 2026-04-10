@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.categories.categories') }}
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
                    {{ __('messages.categories.categories') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.main') }}" class="text-muted text-hover-primary">
                            {{ __('messages.dashboard') }}
                        </a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        {{ __('messages.categories.categories') }}
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
                                       placeholder="{{ __('messages.categories.search_category') }}"/>
                            </div>

                            @can('categories_delete')
                                @if (!empty($selected))
                                    <div class="ms-2">
                                        <button wire:click="deleteSelected" class="btn btn-danger"
                                                wire:key="{{ uniqid() }}">

                                            {{ __('messages.bulk_deleted') }} {{ count($selected) }}
                                        </button>
                                    </div>
                                @endif
                            @endcan
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    @can('categories_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">
                                {{ __('messages.categories.add_category') }}
                            </a>
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
                            @can('categories_delete')
                                <th><input type="checkbox" wire:model.live="selectAll"></th>
                            @endcan
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                {{ __('messages.categories.category_id') }}</th>
                            <th class="text-start min-w-70px th-btn">{{ __('messages.categories.image') }}</th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('category_title')">
                                {{ __('messages.categories.category_title') }}</th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('category_description')">
                                {{ __('messages.categories.category_description') }}</th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('parent_id')">
                                {{ __('messages.categories.category_section') }}</th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('category_type')">
                                {{ __('messages.categories.category_type') }}</th>
                            @canany(['categories_edit', 'categories_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach ($this->categories as $key => $category)
                            <tr>
                                <td class="text-start " wire:key="{{ $category->id }}_"
                                    id="{{ $category->id }}_">
                                    <input type="checkbox" id="{{ $category->id }}" wire:model.live="selected"
                                           value="{{ $category->id }}">
                                </td>
                                <td class="text-start ">
                                    {{ $category->id }}
                                </td>
                                <td class="text-start ">
                                    @if (isset($category?->files?->file?->path))
                                        <div class="symbol symbol-50px">
                                            <img src="{{ file_url($category?->files?->file?->path) }}"
                                                 alt="{{ __('messages.categories.image') }}"/>
                                        </div>
                                    @else
                                        <div class="symbol symbol-50px">
                                            @php
                                                $colors = ['primary', 'danger', 'info'];
                                                $color = $colors[array_rand($colors)];
                                            @endphp
                                            <div
                                                class="symbol-label fs-2 fw-semibold bg-{{ $color }} text-inverse-{{ $color }}">
                                                @php
                                                    $names = explode(' ', $category->category_title);
                                                    $firstInitial = mb_substr($names[0], 0, 1);
                                                    $lastInitial = mb_substr(end($names), 0, 1);
                                                    $initials = $firstInitial . ' ' . $lastInitial;
                                                @endphp
                                                {{ $initials }}
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-start ">
                                    {{ $category->category_title ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start  text-truncate">
                                    {{ Str::limit($category->category_description, 50) ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $category?->parent?->category_title ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $category->category_type
                                        ? \App\Enums\CategoryTypeEnum::from($category->category_type)->label()
                                        : __('messages.no_data') }}
                                </td>
                                @canany(['categories_edit', 'categories_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('categories_edit')
                                                    <li>
                                                        <a class="dropdown-item text-warning btn"
                                                           wire:click="edit({{ $category }})">
                                                            <i class="bi bi-pencil text-warning"></i>
                                                            {{ __('messages.edit') }}

                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('categories_delete')
                                                    <li>
                                                        <a class="dropdown-item text-danger btn"
                                                           wire:click="categoryDelete({{ $category }})">
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
                        {{ $this->categories->links() }}
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
    
        @include('components.modals.category_modals')
    


</div>
@section('script')
    <script>
        window.addEventListener('show_delete_selected_modal', event => {
            $('#deleteSelected').modal('show');
        })
        window.addEventListener('hide_delete_selected_modal', event => {
            $('#deleteSelected').modal('hide');
        })
        window.addEventListener('show_category_form', event => {
            $('#categoryForm').modal('show');
        })
        window.addEventListener('hide_category_form', event => {
            $('#categoryForm').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_category_form', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.category_saved') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
            });
        });

        window.addEventListener('show_category_delete', event => {
            $('#categoryDelete').modal('show');
        })
        window.addEventListener('hide_category_delete', event => {
            $('#categoryDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_category_delete', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.category_deleted') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
            });
        });
    </script>
@endsection
