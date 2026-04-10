@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.courses.courses') }}
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
                    {{ __('messages.courses.courses') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.main') }}" class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{ __('messages.courses.courses') }}</li>
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
                    {{--     <div class="card-title">

                         </div>--}}
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    @if($this->courses->count() == 0)
                    @can('courses_create')

                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">{{ __('messages.courses.add_course') }}</a>
                            <!--end::Add product-->
                        </div>

                    @endcan
                    @endif
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
                                wire:click="sortBy('id')">{{ __('messages.courses.id') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.courses.title') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.courses.url') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.courses.is_active') }}
                            </th>
                            @canany(['courses_edit', 'courses_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->courses as $key => $item)
                            <tr>
                                <td class="text-start ">
                                    {{$item->id}}
                                </td>
                                <td class="text-start ">
                                    {{$item->title}}
                                </td>
                                <td class="text-start ">
                                    {{$item->url}}
                                </td>
                                <td class="text-start ">
                                    {{$item->is_active ?  __('messages.courses.active')  :  __('messages.courses.unactive') }}
                                </td>

                                @canany(['courses_edit', 'courses_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('courses_edit')
                                                    <li><a class="dropdown-item text-warning"
                                                           wire:click="edit({{$item}})">
                                                            {{ __('messages.edit') }}
                                                            <i class="bi bi-pencil text-warning"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('courses_delete')
                                                    <li><a class="dropdown-item text-danger"
                                                           wire:click="delete({{$item}})">
                                                            {{ __('messages.delete') }}
                                                            <i class="bi bi-trash text-danger"></i>
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
                        {{$this->courses->links()}}
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
    
        @include('components.modals.course_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_form', event => {
            $('#addForm').modal('show');
        })
        window.addEventListener('hide_form', event => {
            $('#addForm').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_form', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.course_saved') }}',
                        icon: 'success',
                        confirmButtonText: '{{ __('messages.alert_message.done') }}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );

            });
        });

        window.addEventListener('show_delete', event => {
            $('#delete').modal('show');
        })
        window.addEventListener('hide_delete', event => {
            $('#delete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_delete', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.course_deleted') }}',
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
