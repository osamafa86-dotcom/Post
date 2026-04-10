@php use App\Enums\PublishEnum; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.'.$model_name.'.title') }}
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
                    {{ __('messages.'.$model_name.'.title') }}
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
                        {{ __('messages.'.$model_name.'.title') }}
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
                                       placeholder="{{ __('messages.user_details.search') }}"/>
                            </div>

                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                 {{--   @if(isset($publishable))
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <select class="form-control"
                                wire:model.live="publish_filter"
                                id="publish_filter">
                            <option value="%">الكل</option>
                            <option value="{{PublishEnum::PUBLISHED->value}}">{{PublishEnum::PUBLISHED->value}}</option>
                            <option value="{{PublishEnum::DRAFT->value}}">{{PublishEnum::DRAFT->value}}</option>
                        </select>
                        </div>
                    @endif--}}

                @can($model_name.'_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">
                                {{ __('messages.create') }}
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
                            @foreach($this->columns as $column)
                                <th class="text-start min-w-70px th-btn"
                                    wire:click="sortBy('{{$column}}')">{{ __('messages.'.$model_name.'.'.$column) }}</th>
                            @endforeach
                          {{--  @if(isset($publishable))
                                <th class="text-start min-w-70px th-btn"
                                    wire:click="sortBy('publish_status')">{{ __('messages.publish') }}</th>
                            @endif--}}
                            @canany([$model_name.'_edit', $model_name.'_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach($this->items as $key => $item)
                                <tr>
                                    @foreach($this->columns as $column)
                                        <td class="text-start ">
                                            {{ $item->{$column} }}
                                        </td>
                                    @endforeach
                                 {{--   @if(isset($publishable))
                                        <td class="text-start ">
                                            {{ $item->publish_status }}
                                        </td>
                                    @endif--}}
                                    @canany([$model_name.'_edit', $model_name.'_delete', $model_name.'_publish'])
                                            <td class="text-start ">
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                            id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                        {{ __('messages.options') }}
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                        style="z-index: 10;">
                                                        @if(isset($publishable) && $item->publish_status == PublishEnum::DRAFT->value )
                                                            @can($model_name.'_publish')
                                                                <li>
                                                                    <a class="dropdown-item text-warning"
                                                                       wire:click="publish({{ $item }})">
                                                                        {{ __('messages.publish') }}
                                                                        <i class="bi bi-pencil text-warning"></i>
                                                                    </a>
                                                                </li>
                                                            @elseif(in_array($item->publisher_id,Auth::user()->employees()->pluck('id')->toArray()))
                                                                <li>
                                                                    <a class="dropdown-item text-warning"
                                                                       wire:click="publish({{ $item }})">
                                                                        {{ __('messages.publish_request') }}
                                                                        <i class="bi bi-pencil text-warning"></i>
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        @endif
                                                        @can($model_name.'_edit')
                                                            <li>
                                                                <a class="dropdown-item text-warning"
                                                                   wire:click="edit({{ $item }})">
                                                                    {{ __('messages.edit') }}
                                                                    <i class="bi bi-pencil text-warning"></i>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can($model_name."_delete")
                                                            <li>
                                                                <a class="dropdown-item text-danger"
                                                                   wire:click="delete({{ $item }})">
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
                        {{ $this->items->links() }}
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
    @include('components.modals.'.$model_name.'_modals')

</div>

@section('script')
    <script>
        window.addEventListener('show_form', event => {
            $('#form').modal('show');

        })
        window.addEventListener('hide_form', event => {
            $('#form').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_form', function () {
                Swal.fire(  {
                    title: '{{__('messages.alert_message.success')}}',
                    text: '{{__('messages.alert_message.profile_saved')}}',
                    icon: 'success',
                    confirmButtonText: '{{__('messages.alert_message.done')}}',
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
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_delete', function () {
                Swal.fire(  {
                    title: '{{__('messages.alert_message.success')}}',
                    text: '{{__('messages.alert_message.profile_saved')}}',
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

