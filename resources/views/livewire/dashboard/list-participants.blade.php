@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.participants.participants') }}
@endsection
@section('style')
    <style>
        .th-btn {
            cursor: pointer !important;
        }
    </style>
    <style>
        .tooltip-container {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        /* Tooltip Styling */
        .tooltip-content {
            display: none;
            position: absolute;
            background: white;
            color: black;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            white-space: nowrap;
            top: 25px;
            left: 0;
            z-index: 100;
            font-size: 25px;
        }

        /* Show tooltip on hover */
        .tooltip-container:hover .tooltip-content {
            display: block;
        }

        /* Table Styling */
        .tooltip-content table {
            border-collapse: collapse;
            width: 100%;
        }

        .tooltip-content th,
        .tooltip-content td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .tooltip-content th {
            background-color: #f4f4f4;
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
                    {{ __('messages.participants.participants') }}
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.participants.participants') }}</li>
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
                                       placeholder="{{ __('messages.participants.search') }}"/>
                            </div>

                            @can('participants_delete')
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
                    @can('participants_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew"
                               class="btn btn-primary">{{ __('messages.participants.add_participant') }}</a>
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
                            @can('participants_delete')
                                <th><input type="checkbox" wire:model.live="selectAll"></th>
                            @endcan
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                {{ __('messages.participants.id') }}
                            </th>
                            <th class="text-start min-w-70px">{{ __('messages.participants.image') }}
                            </th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('name')">
                                {{ __('messages.participants.name') }}
                            </th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('type')">
                                {{ __('messages.participants.type') }}
                            </th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('work')">
                                {{ __('messages.participants.work') }}
                            </th>
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('description')">
                                {{ __('messages.participants.description') }}
                            </th>
                            <th class="text-start min-w-70px th-btn">{{ __('messages.participants.details') }}
                            </th>
                            @canany(['participants_edit', 'participants_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach ($this->participants as $key => $item)
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
                                    @if (isset($item?->files?->file?->path))
                                        <div class="avatar-tile" title="messages.author_image" bis_skin_checked="1">
                                            <img class="avatar-img" src="{{ file_url($item?->files?->file?->path) }}"
                                                 alt="{{ __('messages.author_image') }}"/>
                                        </div>
{{--                                        <div class="symbol symbol-50px">--}}
{{--                                            <img src="{{ file_url($item?->files?->file?->path) }}"--}}
{{--                                                 alt="{{ __('messages.author_image') }}"/>--}}
{{--                                        </div>--}}
                                    @else
                                        <div class="symbol symbol-50px">
                                            @php
                                                $colors = ['primary', 'danger', 'info'];
                                                $color = $colors[array_rand($colors)];
                                            @endphp
                                            <div
                                                class="symbol-label fs-2 fw-semibold bg-{{ $color }} text-inverse-{{ $color }}">
                                                @php
                                                    $names = explode(' ', $item->author_name);
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
                                    {{ $item->name ?? __('messages.no_data') }}
                                </td>

                                <td class="text-start ">
                                    {{ $item->type ? \App\Enums\ParticipantTypeEnum::from($item->type)->label() : __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $item->work ?? __('messages.no_data') }}
                                </td>
                                <td class="text-start ">
                                    {{ $item->description ?? __('messages.no_data') }}
                                </td>

                                <td class="text-start ">
                                    <div class="tooltip-container">
                                        <span class="bi bi-info"></span>
                                        <div class="tooltip-content">

                                            @foreach ($item->participant_social_media as $item2)
                                                <img width="25"
                                                     src="{{ file_url($item2['icon']['icon_path']) }}"
                                                     alt="icon">
                                                <a href="{{ $item2['social_media_link'] }}" target="_blank">
                                                    {{ $item2['social_media_name'] }}</a>

                                                <br>
                                            @endforeach

                                        </div>
                                    </div>

                                </td>
                                @canany(['participants_edit', 'participants_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('participants_edit')
                                                    <li><a class="dropdown-item text-warning"
                                                           wire:click="edit({{ $item }})">
                                                            {{ __('messages.edit') }}
                                                            <i class="bi bi-pencil text-warning"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('participants_delete')
                                                    <li><a class="dropdown-item text-danger"
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
                        {{ $this->participants->links() }}
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


    
        @include('components.modals.participants_modals')
    

</div>
@section('script')
    <script>
        window.addEventListener('show_delete_selected_modal', event => {
            $('#deleteSelected').modal('show');
        })
        window.addEventListener('hide_delete_selected_modal', event => {
            $('#deleteSelected').modal('hide');
        })
        window.addEventListener('show_form', event => {
            $('#addform').modal('show');
        })
        window.addEventListener('hide_form', event => {
            $('#addform').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_form', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.participant_saved') }}',
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
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_delete', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.participant_deleted') }}',
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
        document.addEventListener('livewire:initialized', function () {
            const typeSelect = document.getElementById('type');
            const workField = document.getElementById('work');
            const descriptionField = document.getElementById('description');
            const urlField = document.getElementById('url');

            function updateFormFields() {
                const selectedType = typeSelect.value;
                const isPublisherOrResource = selectedType === '{{ \App\Enums\ParticipantTypeEnum::PUBLISHERS->value }}' ||
                    selectedType === '{{ \App\Enums\ParticipantTypeEnum::RESOURCES->value }}';

                // Show/hide work and description fields
                if (workField && descriptionField) {
                    workField.closest('.form-group').style.display = isPublisherOrResource ? 'none' : 'block';
                    descriptionField.closest('.form-group').style.display = isPublisherOrResource ? 'none' : 'block';
                }

                // Show/hide URL field
                if (urlField) {
                    urlField.closest('.form-group').style.display = isPublisherOrResource ? 'block' : 'none';
                }
            }

            // Initial update
            updateFormFields();

            // Update on change
            if (typeSelect) {
                typeSelect.addEventListener('change', updateFormFields);
            }
        });
    </script>

@endsection
