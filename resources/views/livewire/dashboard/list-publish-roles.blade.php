@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.publish_role.publish_role') }}
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
                    {{ __('messages.publish_role.publish_role') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.main') }}"
                            class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>

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
                        <!-- (If you have a search box, include it here) -->
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->

                    @can('publish_roles_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">
                                {{ __('messages.publish_role.add_publish_roles') }}
                            </a>
                            <!--end::Add product-->
                        </div>
                    @endcan
                </div>

                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                    {{ __('messages.publish_role.from') }}
                                </th>
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('title')">
                                    {{ __('messages.publish_role.to') }}
                                </th>

                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-start ">
                                        <div class="w-100 mw-150px">
                                            <span>
                                                {{ $user?->from_role?->name ? __('messages.roles.' . $user->from_role->name) : __('messages.not_available') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="text-start ">
                                        <div class="w-100 mw-150px">
                                            <span>
                                                {{ $user?->to_role?->name ? __('messages.roles.' . $user->to_role->name) : __('messages.not_available') }}
                                            </span>
                                        </div>
                                    </td>


                                    <td>
                                        @canany(['publish_roles_edit', 'publish_roles_delete'])
                                        <td class="text-start ">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {{ __('messages.options') }}
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                    style="z-index: 10;">
                                                    @can('publish_roles_edit')
                                                        <li>
                                                            <a class="dropdown-item text-warning"
                                                                wire:click="edit({{ $user }})">
                                                                {{ __('messages.edit') }}
                                                                <i class="bi bi-pencil text-warning"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('publish_roles_delete')
                                                        <li>
                                                            <a class="dropdown-item text-danger"
                                                                wire:click="publishRoleDelete({{ $user }})">
                                                                {{ __('messages.delete') }}
                                                                <i class="bi bi-trash text-danger"></i>
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
    
        @include('components.modals.publish_roles_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_publish_role_form', event => {
            $('#publishRoleForm').modal('show');
        })
        window.addEventListener('hide_publish_role_form', event => {
            $('#publishRoleForm').modal('hide');
        })

        window.addEventListener('show_publish_role_delete', event => {
            $('#deletePublishRoleModal').modal('show');
        });

        window.addEventListener('hide_publish_role_delete', event => {
            $('#deletePublishRoleModal').modal('hide');
        });
    </script>
@endsection
