@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.breaking_news.breaking_news')}}
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
                    {{__('messages.breaking_news.breaking_news')}}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}"
                           class="text-muted text-hover-primary">{{__('messages.dashboard')}}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{__('messages.breaking_news.breaking_news')}}</li>
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
                                       placeholder="{{__('messages.breaking_news.search_for_urgent_news')}}"/>
                            </div>

                            <select wire:model.live="perPage" class="form-select form-select-solid w-100px text-center">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>

                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>

                            @can('breaking_news_delete')
                                @if(!empty($selected))

                                    <button wire:click="deleteSelected" class="btn btn-danger"
                                            wire:key="{{uniqid()}}">
                                        {{__('messages.bulk_deleted')}} {{count($selected)}}
                                    </button>

                                @endif
                            @endcan

                            <div class="dropdown ">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="columnVisibilityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-columns-gap"></i> {{ __('messages.columns') }}
                                </button>
                                <ul class="dropdown-menu p-2" aria-labelledby="columnVisibilityDropdown">

                                    <li class="mb-2">
                                        <div class="form-check ms-2">
                                            <input class="form-check-input" type="checkbox" id="col-select-all"
                                                   wire:model.live="selectAllColumns">
                                            <label class="form-check-label" for="col-select-all">
                                                {{ __('messages.select_all') }}
                                            </label>
                                        </div>
                                    </li>

                                    @foreach($columnVisibility as $column => $visible)
                                        <li>
                                            <div class="form-check ms-2">
                                                <input class="form-check-input" type="checkbox"
                                                       wire:model.live="columnVisibility.{{ $column }}"
                                                       id="col-{{ $column }}">
                                                <label class="form-check-label" for="col-{{ $column }}">
                                                    {{ __('messages.breaking_new_columns.' . $column) }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    @can('breaking_news_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew"
                               class="btn btn-primary">{{__('messages.breaking_news.add_urgent_news')}}</a>
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
                            @can('breaking_news_delete')
                                @if($columnVisibility['checkbox'] ?? true)
                                    <th>
                                        <input type="checkbox" wire:model.live="selectAll">
                                    </th>
                                @endif
                            @endcan

                            @if($columnVisibility['news_id'] ?? true)
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                    {{ __('messages.breaking_news.news_id') }}
                                </th>
                            @endif

                            @if($columnVisibility['title'] ?? true)
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('title')">
                                    {{ __('messages.breaking_news.news_title') }}
                                </th>
                            @endif

                            @if($columnVisibility['url'] ?? true)
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('url')">
                                    {{ __('messages.breaking_news.news_url') }}
                                </th>
                            @endif

                            @if($columnVisibility['publish_status'] ?? true)
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('publish_status')">
                                    {{ __('messages.breaking_news.publish_status') }}
                                </th>
                            @endif

                            @canany(['breaking_news_edit','breaking_news_delete'])
                                @if($columnVisibility['actions'] ?? true)
                                    <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                                @endif
                            @endcanany
                        </tr>
                        </thead>

                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->breaking_news as $news)
                            <tr wire:key="{{ $news->id }}">
                                @can('breaking_news_delete')
                                    @if($columnVisibility['checkbox'] ?? true)
                                        <td class="text-start ">
                                            <input type="checkbox" wire:model.live="selected" value="{{ $news->id }}">
                                        </td>
                                    @endif
                                @endcan

                                @if($columnVisibility['news_id'] ?? true)
                                    <td class="text-start ">{{ $news->id }}</td>
                                @endif

                                @if($columnVisibility['title'] ?? true)
                                    <td class="text-start ">{{ $news->title ?? __('messages.no_data') }}</td>
                                @endif

                                @if($columnVisibility['url'] ?? true)
                                    <td class="text-start ">
                                        @if(!empty($news->url))
                                            <a href="{{ $news->url }}" target="_blank" class="btn btn-link">
                                                {{ __('messages.breaking_news.view_link') }}
                                            </a>
                                        @else
                                            {{ __('messages.no_data') }}
                                        @endif
                                    </td>
                                @endif

                                @if($columnVisibility['publish_status'] ?? true)
                                    <td class="text-start ">{{ App\Enums\PublishEnum::fromValue($news->publish_status) }}</td>
                                @endif

                                @canany(['breaking_news_edit','breaking_news_delete'])
                                    @if($columnVisibility['actions'] ?? true)
                                        <td class="text-start ">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ __('messages.options') }}
                                                </button>
                                                <ul class="dropdown-menu" style="z-index: 10;">
                                                    @can('breaking_news_edit')
                                                        <li>
                                                            <a class="dropdown-item text-warning"
                                                               wire:click="edit({{ $news }})">
                                                                {{ __('messages.edit') }}
                                                                <i class="bi bi-pencil text-warning"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('breaking_news_delete')
                                                        <li>
                                                            <a class="dropdown-item text-danger"
                                                               wire:click="breaking_newsDelete({{ $news }})">
                                                                {{ __('messages.delete') }}
                                                                <i class="bi bi-trash text-danger"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    @endif
                                @endcanany
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    <div>
                        {{$this->breaking_news->links()}}
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
    
        @include('components.modals.breaking_news_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_delete_selected_modal', event => {
            $('#deleteSelected').modal('show');
        })
        window.addEventListener('hide_delete_selected_modal', event => {
            $('#deleteSelected').modal('hide');
        })
        window.addEventListener('show_breaking_news_form', event => {
            $('#breaking_newsForm').modal('show');
        })
        window.addEventListener('hide_breaking_news_form', event => {
            $('#breaking_newsForm').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_breaking_news_form', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.urgent_news_saved')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );
            });
        });

        window.addEventListener('show_breaking_news_delete', event => {
            $('#breaking_newsDelete').modal('show');
        })
        window.addEventListener('hide_breaking_news_delete', event => {
            $('#breaking_newsDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_breaking_news_delete', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.urgent_news_deleted')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );
            });
        });
    </script>
@endsection
