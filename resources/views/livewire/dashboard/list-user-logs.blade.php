@php use App\Models\UserLog;use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.user_logs.user_logs') }}
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
                    {{ __('messages.user_logs.user_logs') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}"
                           class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{ __('messages.user_logs.user_logs') }}</li>
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
                <!-- Header / Filters Toolbar -->
                <div class="card-header py-4">
                    <div class="w-100 d-flex flex-wrap align-items-center gap-3">

                        {{-- Search --}}
                        <div class="flex-grow-1" style="min-width:260px;">
                            <label class="visually-hidden"
                                   for="logsSearch">{{ __('messages.user_logs.search_user') }}</label>
                            <div class="input-group input-group-solid">
                    <span class="input-group-text border-0 bg-transparent">
                        <i class="bi bi-search fs-4 text-muted"></i>
                    </span>
                                <input id="logsSearch"
                                       type="text"
                                       wire:model.live="search"
                                       class="form-control form-control-solid"
                                       placeholder="{{ __('messages.user_logs.search_user') }}"
                                       aria-label="{{ __('messages.user_logs.search_user') }}">
                            </div>
                        </div>

                        {{-- Model filter --}}
                        <div class="d-flex align-items-center gap-2" style="min-width:220px;">
                            <label for="logsModelFilter"
                                   class="text-muted fw-semibold mb-0">{{ __('messages.sort') }}</label>
                            <select id="logsModelFilter"
                                    wire:model.live="list"
                                    class="form-select form-select-solid">
                                <option value="all">{{ __('messages.all') }}</option>
                                @foreach(\App\Enums\LoggableModelsEnum::cases() as $case)
                                    @php $count = $this->model_counts[$case->value] ?? 0; @endphp
                                    <option
                                        value="{{ $case->value }}">{{ $case->label() }}{{ $count>0 ? ' ('.$count.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date range (in one capsule) --}}
                        <div class="d-flex align-items-center flex-wrap gap-2 rounded-3 px-2 py-1 border"
                             style="--bs-border-color: var(--bs-border-color);">
                            <div class="d-flex align-items-center gap-2">
                                <label for="logsFrom"
                                       class="text-muted small mb-0">{{ __('messages.from_date') }}</label>
                                <input id="logsFrom"
                                       type="date"
                                       wire:model.live="start_date"
                                       class="form-control form-control-solid"
                                       style="min-width: 165px;">
                            </div>
                            <span class="text-muted">—</span>
                            <div class="d-flex align-items-center gap-2">
                                <label for="logsTo" class="text-muted small mb-0">{{ __('messages.to_date') }}</label>
                                <input id="logsTo"
                                       type="date"
                                       wire:model.live="end_date"
                                       class="form-control form-control-solid"
                                       style="min-width: 165px;">
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button type="button"
                                    wire:click="export"
                                    class="btn btn-primary btn-sm">
                                <i class="bi bi-download me-1"></i>{{ __('messages.export') }}
                            </button>

                            <button type="button"
                                    wire:click="exportWithSortModal()"
                                    class="btn btn-light btn-sm border">
                                <i class="bi bi-sliders me-1"></i>{{ __('messages.user_logs.export_with_edit_details') }}
                            </button>
                        </div>
                    </div>

                    {{-- Context filter chip (Post) --}}
                    @if($actionable_id)
                        <div class="w-100 mt-3">
                            <div
                                class="alert alert-info d-flex align-items-center justify-content-between py-2 px-3 mb-0">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-funnel"></i>
                                    <span class="fw-semibold">
                            <strong>{{ __('messages.user_logs.filtered_by') }}:</strong>
                            {{ __('messages.user_logs.post') }} ID: {{ $actionable_id }}
                        </span>
                                </div>
                                <button wire:click="clearPostFilter"
                                        type="button"
                                        class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('messages.user_logs.clear') }}
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!--end::Products-->
            <!--begin::Products-->
            <div class="card card-flush my-4">
                <!--begin::Card header-->
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h5>{{ __('messages.user_logs.distribution_by_tables') }}</h5>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.user_logs.user_log_id') }}
                                    <i class="bi {{ $this->getSortIcon('id') }} sort-icon"></i>
                                </div>
                            </th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('user_id')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.user_logs.user_name') }}
                                    <i class="bi {{ $this->getSortIcon('user_id') }} sort-icon"></i>
                                </div>
                            </th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('actionable_type')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.user_logs.location') }}
                                    <i class="bi {{ $this->getSortIcon('actionable_type') }} sort-icon"></i>
                                </div>
                            </th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('action_status')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.user_logs.operation') }}
                                    <i class="bi {{ $this->getSortIcon('action_status') }} sort-icon"></i>
                                </div>
                            </th>


                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('created_at')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.user_logs.action_date') }}
                                    <i class="bi {{ $this->getSortIcon('created_at') }} sort-icon"></i>
                                </div>
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->user_logs as $key => $log)
                            <tr>
                                <td class="text-start ">
                                    {{$log->id}}
                                </td>
                                <td class="text-start ">
                                    {{$log->user?->full_name ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    @php
                                        $modelEnum = \App\Enums\LoggableModelsEnum::tryFrom($log->actionable_type);
                                        $modelLabel = $modelEnum?->label() ?? '';
                                        $modelClass = $modelEnum?->modelClass();
                                        $titleKey = $modelClass && isset($model_titles[$modelClass]) ? $model_titles[$modelClass] : null;
                                        // Use preview fields to avoid loading full JSON
                                        $after = json_decode($log->actionable_data_after_preview ?? '{}', true) ?? [];
                                        $before = json_decode($log->actionable_data_before_preview ?? '{}', true) ?? [];
                                        $titleValue = $titleKey ? ($after[$titleKey] ?? $before[$titleKey] ?? '') : '';
                                        $titleOutput = $titleValue ? '('.$titleValue.')' : '';
                                    @endphp

                                    {{ $modelLabel }} {{ $titleOutput }}

                                </td>
                                <td class="text-start ">
                                    {{\App\Enums\ActionsEnum::fromValue($log->action_status)}}
                                </td>

                                <td class="text-start " style="direction: ltr">
                                    {{Carbon::parse($log->created_at)->format('Y-m-d H:i:s') ?? __('messages.not_available')}}
                                </td>

                                <td class="text-start " style="direction: ltr">
                                    <i class="bi bi-eye m-1" wire:click="userLogTypeDetails({{$log->id}})"></i>
                                    @if($log->action_status == \App\Enums\ActionsEnum::EDIT->value)
                                        <i class="bi bi-info-circle m-1" wire:click="userLogDetails({{$log->id}})"></i>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{$this->user_logs->links()}}
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Products-->
            <!--begin::Products-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h5>{{ __('messages.user_logs.distribution_by_users') }}</h5>
                    </div>
                    <!--end::Card title-->
                    <button type="button" wire:click="$toggle('showUserStats')" class="btn btn-sm btn-light">
                        <i class="bi bi-{{ $showUserStats ? 'chevron-up' : 'chevron-down' }}"></i>
                        {{ $showUserStats ? __('messages.hide') : __('messages.show') }}
                    </button>
                </div>
                <!--end::Card header-->
                @if($showUserStats)
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-70px">{{ __('messages.user_logs.user_name') }}</th>
                            <th class="text-start min-w-70px">{{ __('messages.add') }}</th>
                            <th class="text-start min-w-70px">{{ __('messages.edit') }}</th>
                            <th class="text-start min-w-70px">{{ __('messages.delete') }}</th>
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->user_logs_counter as $key => $log)
                            <tr>
                                <td class="text-start ">
                                    {{$key ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$log[\App\Enums\ActionsEnum::CREATE->value] ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$log[\App\Enums\ActionsEnum::EDIT->value] ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$log[\App\Enums\ActionsEnum::DELETE->value] ?? __('messages.not_available')}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
                @endif
            </div>
            <!--end::Products-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
    
        @include('components.modals.user_logs_details_modals')
        @include('components.modals.user_logs_type_details_modals')
        @include('components.modals.export_with_sort_modals')
    
</div>
@section('script')
    <script>
        window.addEventListener('show_user_logs_details', event => {
            $('#UserLogsDetails').modal('show');
        })
        window.addEventListener('hide_user_logs_details', event => {
            $('#UserLogsDetails').modal('hide');
        })
        window.addEventListener('show_export_with_sort', event => {
            $('#ExportWithSort').modal('show');
        })
        window.addEventListener('hide_export_with_sort', event => {
            $('#ExportWithSort').modal('hide');
        })
        window.addEventListener('show_user_logs_type_details', event => {
            $('#UserLogsTypeDetails').modal('show');
        })
        window.addEventListener('hide_user_logs_type_details', event => {
            $('#UserLogsTypeDetails').modal('hide');
        })
    </script>
@endsection
