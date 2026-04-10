@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.applications.applications') }}
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
                    {{ __('messages.applications.applications') }}
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.applications.applications') }}</li>
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
                                       placeholder="{{ __('messages.applications.search') }}"/>
                            </div>

                        </div>

                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="filterCategory('all')"
                               class="btn btn-primary mx-1">{{ __('messages.applications.all') }}
                                ({{$this->applications['applications']->count()}})</a>
                            <!--end::Add product-->
                        </div>

                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="filterCategory('تطوع')"
                               class="btn btn-primary mx-1">{{ __('messages.applications.volunteering') }}
                                ({{$this->applications['volunteering']}})</a>
                            <!--end::Add product-->
                        </div>
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="filterCategory('تدريب')"
                               class="btn btn-primary mx-1">{{ __('messages.applications.courses') }}
                                ({{$this->applications['courses']}})</a>
                            <!--end::Add product-->
                        </div>
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="filterCategory('مؤتمرات')"
                               class="btn btn-primary mx-1">{{ __('messages.applications.conference') }}
                                ({{$this->applications['conference']}})</a>
                            <!--end::Add product-->
                        </div>
                        <!--end::Search-->
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="filterAccepted('1')"
                               class="btn btn-primary mx-1">{{ __('messages.applications.accepted') }}
                                ({{$this->applications['accepted']}})</a>
                            <!--end::Add product-->
                        </div>
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="filterAccepted('0')"
                               class="btn btn-primary mx-1">{{ __('messages.applications.not_accepted') }}
                                ({{$this->applications['not_accepted']}})</a>
                            <!--end::Add product-->
                        </div>
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->

                    {{--                    @can('services_create')--}}

                    {{--                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">--}}
                    {{--                            <!--begin::Add product-->--}}
                    {{--                            <a wire:click="addNew" class="btn btn-primary">{{ __('messages.applications.add_application') }}</a>--}}
                    {{--                            <!--end::Add product-->--}}
                    {{--                        </div>--}}

                    {{--                    @endcan--}}

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
                                wire:click="sortBy('id')">{{ __('messages.applications.id') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.applications.image') }}</th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.name') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.course') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.birth_date') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.country') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.city') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.town') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.gender') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.specialization') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.skills') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.whatsapp') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.email') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.facebook') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.change_status') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.applications.file') }}
                            </th>
                            @canany(['applications_edit', 'applications_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->applications['applications'] as $key => $item)
                            <tr>
                                <td class="text-start ">
                                    {{$item->id}}
                                </td>
                                <td class="text-start ">
                                    <img width="45" src="{{file_url($item->image)}}" alt="icon">
                                </td>
                                <td class="text-start ">
                                    {{$item->name}}
                                </td>
                                <td class="text-start ">
                                    {{$item->course_application?->title}}
                                </td>
                                <td class="text-start ">
                                    {{$item->birth_date}}
                                </td>
                                <td class="text-start ">
                                    {{$item->country}}
                                </td>
                                <td class="text-start ">
                                    {{$item->city}}
                                </td>
                                <td class="text-start ">
                                    {{$item->town}}
                                </td>
                                <td class="text-start ">
                                    {{$item->gender}}
                                </td>
                                <td class="text-start ">
                                    {{$item->specialization}}
                                </td>
                                <td class="text-start ">
                                    {{$item->skills}}
                                </td>
                                <td class="text-start ">
                                    {{$item->whatsapp}}
                                </td>
                                <td class="text-start ">
                                    {{$item->email}}
                                </td>
                                <td class="text-start ">
                                    {{$item->facebook}}
                                </td>

                                <td class="text-start ">
                                    {{$item->is_accepted ?  __('messages.applications.accepted')  :  __('messages.applications.not_accepted') }}
                                </td>
                                <td class="text-start ">

                                    <a href="{{ file_url($item->portfolio) }}" target="_blank"
                                       title="{{__('messages.applications.open_file')}}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                                @canany(['applications_edit', 'applications_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('applications_edit')
                                                    <li><a class="dropdown-item text-warning"
                                                           wire:click="edit({{$item}})">
                                                            {{$item->is_accepted ?  __('messages.applications.change_not_accepted')  :  __('messages.applications.change_status') }}
                                                            <i class="bi bi-pencil text-warning"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('applications_delete')
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
                        {{$this->applications['applications']->links()}}
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
    
        @include('components.modals.applications_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_change_status_form', event => {
            $('#changeStatus').modal('show');
        })
        window.addEventListener('hide_change_status_form', event => {
            $('#changeStatus').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_change_status_form', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.application_saved') }}',
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
                        text: '{{ __('messages.alert_message.application_deleted') }}',
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
