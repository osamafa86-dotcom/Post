@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.tags.tags') }}
@endsection

@section('style')
    <style>
        /* ===========================
           Table polish (light & dark)
           =========================== */

        /* Sticky header for better scanning on long tables */
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 3;
            background: var(--bs-card-bg);
        }

        /* Row hover for readability */
        .table tbody tr:hover {
            background: color-mix(in srgb, var(--bs-primary) 6%, transparent);
        }
        [data-bs-theme="dark"] .table tbody tr:hover {
            background: color-mix(in srgb, var(--bs-primary) 12%, transparent);
        }

        /* ID column: monospace for quick scanning */
        .col-id {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            opacity: .9;
        }

        /* Ellipsis helper to keep rows tidy on small screens */
        .text-ellipsis {
            max-width: 260px;          /* tune per layout */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @media (max-width: 992px) {
            .text-ellipsis { max-width: 180px; }
        }

        /* Compact checkbox alignment */
        .table input[type="checkbox"] {
            width: 16px; height: 16px; vertical-align: middle;
        }

        /* ===========================
           Tag-type Chips (tone-aware)
           =========================== */
        .chip {
            --_bg: hsl(220 14% 96%);       /* light default */
            --_fg: hsl(220 13% 20%);
            --_bd: color-mix(in srgb, var(--_fg) 28%, transparent);
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .25rem .55rem;
            border-radius: 999px;
            border: 1px solid var(--_bd);
            background: var(--_bg);
            color: var(--_fg);
            font-size: .825rem;
            line-height: 1;
            font-weight: 600;
        }
        .chip i { font-size: .95rem; line-height: 1; }

        /* Dark theme adaptation */
        [data-bs-theme="dark"] .chip {
            --_bg: hsl(220 16% 18%);
            --_fg: hsl(220 15% 90%);
            --_bd: color-mix(in srgb, var(--_fg) 18%, transparent);
        }

        /* Tones by tag type (uses classes like .tone-person, .tone-location, …)
           Fallback stays readable if type is unknown. */
        .tone-keyword      { --_bg: hsl(221 100% 96%); --_fg: #1d4ed8; --_bd: #93c5fd; }
        .tone-topic        { --_bg: hsl(199 100% 95%); --_fg: #0ea5e9; --_bd: #7dd3fc; }
        .tone-person       { --_bg: hsl(142 78% 95%);  --_fg: #059669; --_bd: #86efac; }
        .tone-organization { --_bg: hsl(258 92% 96%);  --_fg: #7c3aed; --_bd: #c4b5fd; }
        .tone-location     { --_bg: hsl(13 89% 95%);   --_fg: #ea580c; --_bd: #fdba74; }
        .tone-event        { --_bg: hsl(48 96% 93%);   --_fg: #b45309; --_bd: #fde68a; }

        /* Dark variants (stronger bg for contrast) */
        [data-bs-theme="dark"] .tone-keyword      { --_bg: hsl(221 70% 22%); --_fg: #bfdbfe; --_bd: #60a5fa; }
        [data-bs-theme="dark"] .tone-topic        { --_bg: hsl(199 80% 20%); --_fg: #bae6fd; --_bd: #38bdf8; }
        [data-bs-theme="dark"] .tone-person       { --_bg: hsl(142 70% 18%); --_fg: #a7f3d0; --_bd: #34d399; }
        [data-bs-theme="dark"] .tone-organization { --_bg: hsl(258 70% 20%); --_fg: #e9d5ff; --_bd: #a78bfa; }
        [data-bs-theme="dark"] .tone-location     { --_bg: hsl(13 70% 18%);  --_fg: #fed7aa; --_bd: #fb923c; }
        [data-bs-theme="dark"] .tone-event        { --_bg: hsl(48 70% 20%);  --_fg: #fef9c3; --_bd: #facc15; }

        /* Actions dropdown polish */
        .table .btn.btn-secondary.dropdown-toggle {
            height: 36px; padding: .25rem .6rem; border-radius: .5rem;
        }
        .dropdown-menu {
            border-radius: .6rem;
        }
        .dropdown-item i { margin-inline-end: .35rem; }

        /* Small sort pointer affordance */
        .th-btn { cursor: pointer !important; }
        .th-btn:hover { color: var(--bs-primary); }

        /* Empty-state styling for no results */
        .empty-state {
            padding: 2rem; text-align: center; color: var(--bs-text-muted);
        }
    </style>
@endsection

<div>
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('messages.tags.tags') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.main') }}" class="text-muted text-hover-primary">
                            {{ __('messages.dashboard') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ __('messages.tags.tags') }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="card card-flush">
                <!-- Header -->
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <!-- Search + bulk actions -->
                        <div class="d-flex align-items-center position-relative my-1 gap-2 flex-wrap">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <i class="bi bi-search fs-3 input-group-text"></i>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live="search"
                                    class="form-control form-control-solid w-250px"
                                    placeholder="{{ __('messages.tags.search_tag') }}"
                                />
                            </div>

                            @can('tags_delete')
                                @if (!empty($selected))
                                    <div class="ms-2">
                                        <button wire:click="deleteSelected" class="btn btn-danger" wire:key="{{ uniqid() }}">
                                            {{-- Shows how many items are selected for deletion --}}
                                            {{ __('messages.bulk_deleted') }} {{ count($selected) }}
                                        </button>
                                    </div>
                                @endif
                            @endcan
                        </div>
                    </div>

                    @can('tags_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <a wire:click="addNew" class="btn btn-primary">
                                {{ __('messages.tags.add_tag') }}
                            </a>
                        </div>
                    @endcan
                </div>
                <!-- /Header -->

                <!-- Body -->
                <div class="card-body pt-0 table-responsive">
                    @php
                        /*
                         |----------------------------------------------------------------------
                         | Visual meta for tag types (icon + tone class)
                         | We keep it front-end only, no backend logic changes.
                         | key: label returned by TagTypeEnum::label()
                         | tone: maps to .tone-... CSS class above
                         | icon: Bootstrap Icons class
                         */
                        $tagTypeMeta = [
                            'Keyword'      => ['tone' => 'tone-keyword',      'icon' => 'bi-tag'],
                            'Topic'        => ['tone' => 'tone-topic',        'icon' => 'bi-hash'],
                            'Person'       => ['tone' => 'tone-person',       'icon' => 'bi-person'],
                            'Organization' => ['tone' => 'tone-organization', 'icon' => 'bi-building'],
                            'Location'     => ['tone' => 'tone-location',     'icon' => 'bi-geo-alt'],
                            'Event'        => ['tone' => 'tone-event',        'icon' => 'bi-calendar-event'],
                        ];
                    @endphp

                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            @can('tags_delete')
                                <th class="min-w-40px">
                                    <input type="checkbox" wire:model.live="selectAll" aria-label="Select all rows">
                                </th>
                            @endcan
                            <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.tags.tag_id') }}
                                    <i class="bi {{ $this->getSortIcon('id') }} sort-icon"></i>
                                </div>
                            </th>
                            <th class="text-start min-w-200px th-btn" wire:click="sortBy('tag_name')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.tags.tag_title') }}
                                    <i class="bi {{ $this->getSortIcon('tag_name') }} sort-icon"></i>
                                </div>
                            </th>
                            <th class="text-start min-w-180px th-btn" wire:click="sortBy('tag_type')">
                                <div class="d-flex align-items-center gap-1">
                                    {{ __('messages.tags.tag_type') }}
                                    <i class="bi {{ $this->getSortIcon('tag_type') }} sort-icon"></i>
                                </div>
                            </th>
                            @canany(['tags_edit', 'tags_delete'])
                                <th class="text-start min-w-120px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>

                        <tbody class="fw-semibold text-gray-600">
                        @forelse ($this->tags as $key => $tag)
                            @php
                                // Resolve human label from Enum (existing backend)
                                $typeLabel = $tag->tag_type
                                    ? \App\Enums\TagTypeEnum::from($tag->tag_type)->label()
                                    : null;

                                // Pick visual meta or fallback
                                $meta = $typeLabel && isset($tagTypeMeta[$typeLabel])
                                    ? $tagTypeMeta[$typeLabel]
                                    : ['tone' => 'tone-keyword', 'icon' => 'bi-tag'];

                                // Friendly, stable class name (e.g. tone-person)
                                $toneClass = $meta['tone'];

                                // Icon class for the chip
                                $iconClass = $meta['icon'];
                            @endphp

                            <tr>
                                @can('tags_delete')
                                    <td class="text-start" wire:key="{{ $tag->id }}_" id="{{ $tag->id }}_">
                                        <input
                                            type="checkbox"
                                            id="chk-{{ $tag->id }}"
                                            wire:model.live="selected"
                                            value="{{ $tag->id }}"
                                            aria-label="Select row {{ $tag->id }}"
                                        >
                                    </td>
                                @endcan

                                <!-- ID -->
                                <td class="text-start col-id">
                                    {{ $tag->id }}
                                </td>

                                <!-- Tag name (ellipsized to keep rows neat) -->
                                <td class="text-start">
                                    <span class="text-ellipsis" title="{{ $tag->tag_name ?? __('messages.no_data') }}">
                                        {{ $tag->tag_name ?? __('messages.no_data') }}
                                    </span>
                                </td>

                                <!-- Type with iconified tone-aware chip -->
                                <td class="text-start">
                                    @if($typeLabel)
                                        <span class="chip {{ $toneClass }}" title="{{ $typeLabel }}">
                                            <i class="bi {{ $iconClass }}"></i>
                                            {{ $typeLabel }}
                                        </span>
                                    @else
                                        <span class="chip" title="{{ __('messages.no_data') }}">
                                            <i class="bi bi-question-circle"></i>
                                            {{ __('messages.no_data') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                @canany(['tags_edit', 'tags_delete'])
                                    <td class="text-start">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton{{ $tag->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $tag->id }}" style="z-index: 10;">
                                                @can('tags_edit')
                                                    <li>
                                                        <a class="dropdown-item btn text-warning btn" wire:click="edit({{ $tag }})">
                                                            <i class="bi bi-pencil "></i>
                                                            {{ __('messages.edit') }}
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('tags_delete')
                                                    <li>
                                                        <!-- NOTE: wire:click was misplaced; moved into the <a> attribute -->
                                                        <a class="dropdown-item text-danger btn" wire:click="tagDelete({{ $tag }})">
                                                            <i class="bi bi-trash "></i>
                                                            {{ __('messages.delete') }}
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-inboxes fs-2 me-2"></i>
                                        {{ __('messages.no_data') }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div>
                        {{ $this->tags->links() }}
                    </div>
                </div>
                <!-- /Body -->
            </div>
        </div>
    </div>
    <!--end::Content-->

    
        @include('components.modals.tag_modals')
    
</div>

@section('script')
    <script>
        // Modal events (unchanged backend behavior)
        window.addEventListener('show_delete_selected_modal', () => $('#deleteSelected').modal('show'))
        window.addEventListener('hide_delete_selected_modal', () => $('#deleteSelected').modal('hide'))

        window.addEventListener('show_tag_form', () => $('#tagForm').modal('show'))
        window.addEventListener('hide_tag_form', () => $('#tagForm').modal('hide'))

        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_tag_form', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.tag_saved') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: { confirmButton: 'btn btn-success' }
                });
            });
        });

        window.addEventListener('show_tag_delete', () => $('#tagDelete').modal('show'))
        window.addEventListener('hide_tag_delete', () => $('#tagDelete').modal('hide'))

        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_tag_delete', function () {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.tag_deleted') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: { confirmButton: 'btn btn-success' }
                });
            });
        });
    </script>
@endsection
