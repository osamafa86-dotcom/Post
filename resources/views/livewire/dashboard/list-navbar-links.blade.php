@php
    use App\Enums\LinkUrlTargetEnum;
@endphp

@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.navbar_links.menus') }}
@endsection

{{-- ======================================================================
   Page Styles — visual refactor only (no backend changes)
   - White cards in light
   - Consistent gaps / paddings / headings
   - Clear active/hover states
====================================================================== --}}
@section('style')
    <style>


        .badge.text-light{
            color: #fff !important;
        }

        .equal-card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
            height: auto;
        }

        .card-header {
            border-bottom: 1px solid var(--bs-border-color);
            background: transparent;
        }

        .card-title {
            color: var(--bs-body-color);
            font-weight: 700;
            letter-spacing: .2px;
        }

        .scrollable-content {
            max-height: 64vh;
            overflow: auto;
            padding-right: .25rem;
        }

        .scrollable-content::-webkit-scrollbar {
            width: 8px;
        }

        .scrollable-content::-webkit-scrollbar-thumb {
            background: color-mix(in oklab, var(--bs-primary) 22%, #0000);
            border-radius: 8px;
        }

        .breadcrumb .bullet {
            opacity: .65;
        }

        /* ---------- Toolbar ---------- */
        .page-title h1 {
            color: var(--bs-body-color);
        }

        .page-title .breadcrumb a {
            color: var(--bs-secondary);
        }

        .page-title .breadcrumb a:hover {
            color: var(--bs-primary);
        }

        /* ---------- Small helpers ---------- */
        .no-arrows::-webkit-outer-spin-button,
        .no-arrows::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .no-arrows {
            -moz-appearance: textfield;
        }

        .order-input {
            width: 60px;
            text-align: center;
            display: inline-block;
        }
        [data-bs-theme=dark] .btn.btn-dark {
            color: #fff;}
        .grab-handle {
            cursor: grab;
            color: var(--bs-secondary);
            padding: 0 8px;
        }

        /* ---------- Buttons style unify ---------- */
        .btn-sm {
            border-radius: 10px;
        }

        .btn-dark {
            background: var(--bs-secondary);
            border-color: var(--bs-secondary);
        }

        .btn-dark:hover {
            background: var(--bs-secondary-hover);
            border-color: var(--bs-secondary-hover);
        }

        .btn-success {
            background: #16a34a;
            border-color: #16a34a;
        }

        .btn-success:hover {
            background: #149344;
            border-color: #149344;
        }

        .btn-warning {
            background: var(--bs-warning);
            border-color: var(--bs-warning);
            color: #1f2937;
        }

        .btn-warning:hover {
            background: var(--bs-warning-hover);
            border-color: var(--bs-warning-hover);
            color: #111827;
        }

        .btn-outline-success {
            border-color: #16a34a;
            color: #16a34a;
        }

        .btn-outline-success:hover {
            background: #16a34a;
            color: #fff;
        }

        .btn-light {
            background: #f3f5fb;
            border-color: var(--bs-border-color);
        }

        /* ---------- Accordion polish ---------- */
        .accordion-item {
            border: 1px solid var(--bs-border-color) !important;
            border-radius: 12px;
            overflow: hidden;
        }

        .accordion-button {
            background: #fff0;
            color: var(--bs-body-color);
        }

        .accordion-button:focus {
            box-shadow: var(--focus-ring);
        }

        .accordion-button:not(.collapsed) {
            color: var(--bs-primary);
            background: color-mix(in oklab, var(--bs-primary) 8%, transparent);
        }

        .accordion-body {
            background: color-mix(in oklab, var(--bs-card-bg) 92%, #0000);
        }

        /* ---------- List grouping & rows ---------- */
        .list-group-item {
            border-color: var(--bs-border-color);
            background: var(--bs-card-bg);
        }

        .list-group-spaces > .list-group-item {
            margin-bottom: 8px;
            border-radius: 12px;
        }

        .item-checkbox .form-check-input {
            cursor: pointer;
        }

        .item-checkbox .form-check-label {
            cursor: pointer;
        }

        /* ---------- Badges and status ---------- */
        .badge.bg-light-secondary {
            background: color-mix(in oklab, var(--bs-secondary) 14%, #fff) !important;
            color: var(--bs-secondary) !important;
            border: 1px dashed var(--bs-secondary);
        }

        /* ---------- Empty state ---------- */
        .empty-state-card {
            padding: 2rem;
            text-align: center;
            border: 1px dashed var(--bs-border-color);
            border-radius: 14px;
            background: color-mix(in oklab, #fff 85%, var(--bs-app-bg-color) 15%);
        }

        .empty-state-title {
            font-weight: 700;
            margin: 0;
            color: var(--bs-body-color);
        }

        .empty-state-description {
            margin: .25rem 0 0;
            color: var(--bs-text-muted);
        }

        /* ---------- Inline alerts spacing ---------- */
        .alert {
            border-radius: 12px;
        }

        /* ---------- Focus states ---------- */
        .form-control:focus, .form-select:focus {
            box-shadow: var(--focus-ring);
            border-color: var(--bs-primary);
        }

        /* ---------- Tables / UL inside scrollable ---------- */
        .table-responsive ul.list-group {
            margin: 0;
        }

        .list-group-item .group-checkbox .form-check-label {
            min-width: 110px;
        }
    </style>
@endsection

<div>
    {{-- ================================================================
        Page toolbar — title + breadcrumbs
        (visual only — no backend changes)
    ================================================================ --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="fw-bold fs-3 mb-1">{{ __('messages.navbar_links.menus') }}</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1 mb-0">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard.main') }}" class="text-muted text-hover-primary">
                            {{ __('messages.dashboard') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ __('messages.navbar_links.menus') }}</li>
                </ul>
            </div>
        </div>
    </div>

    @php
        $visibleCards = 0;
        if (config('features.nav_links_access.main_menu')) $visibleCards++;
        if (config('features.nav_links_access.sub_navbar')) $visibleCards++;
        if (config('features.nav_links_access.footer_links')) $visibleCards++;
        if (config('features.nav_links_access.categories')) $visibleCards++;
        if (config('features.nav_links_access.types')) $visibleCards++;
        if (config('features.nav_links_access.social_media')) $visibleCards++;

        $gridClass = match($visibleCards) {
            1 => 'col-12',
            2, 3, 4, 5, 6 => 'col-12 col-lg-6',
            default => 'col-12 col-lg-6',
        };
    @endphp

    {{-- ================================================================
        Content grid — equal visual weight cards
    ================================================================ --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row g-4 mb-4">

                {{-- ======================= Main Menu ======================= --}}
                @if(config('features.nav_links_access.main_menu'))
                    <div class="{{ $gridClass }}">
                        <div class="card card-flush equal-card">
                            <div class="card-header p-4 d-flex justify-content-between align-items-center gap-2">
                                <h3 class="card-title mb-0">{{ __('messages.navbar_links.main_menu') }}</h3>
                                <div class="d-flex gap-2">
                                    {{-- Reorder (by creation time) --}}
                                    <button wire:click="showReorderModal" class="btn btn-sm btn-dark">
                                        <i class="bi bi-arrow-clockwise me-1"></i>{{ __('messages.navbar_links.order_by_creation_time') }}
                                    </button>
                                    {{-- Add new --}}
                                    <button wire:click="addNew" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('messages.add') }}
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Inline alerts --}}
                                @foreach (['link_order_error' => 'danger', 'link_order_success' => 'success'] as $name => $type)
                                    @error($name)
                                    <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>
                                    @enderror
                                @endforeach

                                {{-- List --}}
                                <div class="scrollable-content">
                                    <div class="accordion" id="mainAccordion">
                                        <div wire:sortable="updateLinkOrder">
                                            @forelse($this->navbar_links as $key => $link)
                                                <div class="accordion-item mb-2 shadow-sm"
                                                     wire:sortable.item="{{ $link['id'] }}"
                                                     wire:key="link-{{ $link['id'] }}">
                                                    {{-- Header row --}}
                                                    <h2 class="accordion-header d-flex" id="heading-{{ $link['id'] }}">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center w-100 p-3">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span wire:sortable.handle class="grab-handle"><i
                                                                        class="bi bi-arrows-move"></i></span>
                                                                <input type="number" min="1" max="100"
                                                                       class="form-control form-control-sm border-0 text-center no-arrows order-input"
                                                                       wire:model.live="link_order.{{ $key }}">
                                                                <span
                                                                    class="badge bg-secondary text-light">{{ $key }}</span>
                                                            </div>
                                                            @php $enum = \App\Enums\LinkStatusEnum::fromValue($link['link_status']); @endphp
                                                            <span class="badge bg-light-secondary text-gray-800">
                                                            {{ $link['link_status'] != 0 ? $enum : $key }}
                                                        </span>
                                                        </div>
                                                        <button class="accordion-button collapsed flex-shrink-0 ps-2"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-{{ $link['id'] }}"
                                                                aria-expanded="false"
                                                                aria-controls="collapse-{{ $link['id'] }}"
                                                                style="width:36px;"></button>
                                                    </h2>

                                                    {{-- Body --}}
                                                    <div id="collapse-{{ $link['id'] }}"
                                                         class="accordion-collapse collapse"
                                                         data-bs-parent="#mainAccordion">
                                                        <div class="accordion-body">
                                                            {{-- Menu Text --}}
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.navbar_links.menu_text') }}</label>
                                                                <input type="text"
                                                                       class="form-control @error($key . '.link_name') is-invalid @enderror"
                                                                       wire:model="link.{{ $key }}.link_name">
                                                                @error($key . '.link_name')
                                                                <div
                                                                    class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                            {{-- URL --}}
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.navbar_links.link') }}</label>
                                                                <input type="url" class="form-control"
                                                                       wire:model="link.{{ $key }}.link_url"
                                                                       placeholder="https://example.com">
                                                            </div>

                                                            {{-- Icon picker --}}
                                                            <div class="accordion mb-3" id="iconAcc-{{ $link['id'] }}">
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="iconHead-{{ $link['id'] }}">
                                                                        <button class="accordion-button collapsed p-3"
                                                                                type="button" data-bs-toggle="collapse"
                                                                                data-bs-target="#iconCol-{{ $link['id'] }}"
                                                                                aria-expanded="false"
                                                                                aria-controls="iconCol-{{ $link['id'] }}">
                                                                            # {{ __('messages.navbar_links.icon') }}
                                                                        </button>
                                                                    </h2>
                                                                    <div id="iconCol-{{ $link['id'] }}"
                                                                         class="accordion-collapse collapse"
                                                                         data-bs-parent="#iconAcc-{{ $link['id'] }}">
                                                                        <div class="accordion-body">
                                                                            <div class="row g-3">
                                                                                @foreach ($this->icons as $icon)
                                                                                    <div class="col-3 text-center"
                                                                                         style="cursor:pointer;"
                                                                                         wire:click="iconSelect({{ $icon }})">
                                                                                        <img
                                                                                            src="{{ file_url($icon->icon_path) }}"
                                                                                            class="img-fluid {{ !empty($link['icon_id']) && $link['icon_id'] == $icon->id ? 'border border-primary rounded' : '' }}"
                                                                                            alt="icon">
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @error('icon_id')
                                                            <div class="text-danger">{{ $message }}</div> @enderror

                                                            {{-- Target --}}
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.navbar_links.open_in') }}</label>
                                                                <select class="form-select"
                                                                        wire:model="link.{{ $key }}.link_open">
                                                                    @foreach (LinkUrlTargetEnum::cases() as $case)
                                                                        <option
                                                                            value="{{ $case->value }}">{{ LinkUrlTargetEnum::fromValue($case->value) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            {{-- Actions --}}
                                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                                <button wire:click="updateNavbarLink('{{ $key }}')"
                                                                        class="btn btn-sm btn-success d-flex align-items-center">
                                                                    <i class="bi bi-check-lg me-1"></i>{{ __('messages.save') }}
                                                                </button>
                                                                <button
                                                                    wire:confirm="{{ __('messages.navbar_links.confirm_remove_link') }}"
                                                                    wire:click="deleteNavbarLink('{{ $key }}')"
                                                                    class="btn btn-sm btn-danger d-flex align-items-center">
                                                                    <i class="bi bi-trash me-1"></i>{{ __('messages.delete') }}
                                                                </button>
                                                                @if (count($link['children']))
                                                                    <button
                                                                        wire:click="showSubLinks('{{ $link['id'] }}')"
                                                                        class="btn btn-sm btn-warning d-flex align-items-center">
                                                                        <i class="bi bi-folder2-open me-1"></i>
                                                                        {{ __('messages.navbar_links.show_sub') }}
                                                                        ({{ count($link['children']) }})
                                                                    </button>
                                                                @endif
                                                                <button wire:click="addNewSub('{{ $link['id'] }}')"
                                                                        class="btn btn-sm btn-outline-success d-flex align-items-center">
                                                                    <i class="bi bi-plus-lg me-1"></i>{{ __('messages.navbar_links.add_sub_link_button') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="empty-state-card">
                                                    <p class="empty-state-title">{{ __('messages.navbar_links.no_links') }}</p>
                                                    <p class="empty-state-description">{{ __('messages.navbar_links.add_sub_link_button') }}</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ======================= Sub Navbar ======================= --}}
                @if(config('features.nav_links_access.sub_navbar'))
                    <div class="{{ $gridClass }}">
                        <div class="card card-flush equal-card">
                            <div class="card-header p-4 d-flex justify-content-between align-items-center gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="card-title mb-0">{{ __('messages.navbar_links.sub_navbar') }}</h3>
                                    @if ($parent != 0)
                                        <button wire:click="showReorderModal" class="btn btn-sm btn-dark">
                                            <i class="bi bi-arrow-clockwise me-1"></i>{{ __('messages.navbar_links.order_by_creation_time') }}
                                        </button>
                                    @endif
                                </div>
                                @if ($parent != 0)
                                    <div class="d-flex gap-2">
                                        <button wire:click="updateSubNavbarLink"
                                                class="btn btn-sm btn-warning">{{ __('messages.save') }}</button>
                                        <button wire:click="addNewSub('{{ $parent }}')" class="btn btn-sm btn-success">
                                            <i class="bi bi-plus-circle me-1"></i>{{ __('messages.add') }}
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body">
                                <div class="scrollable-content">
                                    @if ($parent != 0)
                                        @foreach (['sub_link_order_error' => 'danger', 'sub_link_order_success' => 'success'] as $name => $type)
                                            @error($name)
                                            <div class="alert alert-{{ $type }} alert-dismissible fade show"
                                                 role="alert">
                                                {{ $message }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                            </div>
                                            @enderror
                                        @endforeach

                                        <div class="accordion" id="subAccordion">
                                            <div wire:sortable="updateSubLinkOrder">
                                                @forelse($this->navbar_sub_links as $sub_key=>$sub_link)
                                                    <div class="accordion-item mb-2 shadow-sm"
                                                         wire:sortable.item="{{ $sub_link['id'] }}"
                                                         wire:key="sub-link-{{ $sub_link['id'] }}">
                                                        <h2 class="accordion-header d-flex"
                                                            id="subHead-{{ $sub_link['id'] }}">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center w-100 p-3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span wire:sortable.handle class="grab-handle"><i
                                                                            class="bi bi-arrows-move"></i></span>
                                                                    <input type="number" min="1" max="100"
                                                                           class="form-control form-control-sm border-0 text-center no-arrows order-input"
                                                                           wire:model.live="sub_link_order.{{ $sub_key }}">
                                                                    <span
                                                                        class="badge bg-secondary text-light">{{ $sub_key }}</span>
                                                                </div>
                                                                <span class="badge bg-light-secondary text-gray-800">
                                                                {{ $sub_link['link_status'] ? \App\Enums\LinkStatusEnum::fromValue($sub_link['link_status']) : __('messages.no_data') }}
                                                            </span>
                                                            </div>
                                                            <button
                                                                class="accordion-button collapsed flex-shrink-0 ps-2"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#subCollapse-{{ $sub_link['id'] }}"
                                                                aria-expanded="false"
                                                                aria-controls="subCollapse-{{ $sub_link['id'] }}"
                                                                style="width:36px;"></button>
                                                        </h2>
                                                        <div id="subCollapse-{{ $sub_link['id'] }}"
                                                             class="accordion-collapse collapse"
                                                             data-bs-parent="#subAccordion">
                                                            <div class="accordion-body">
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label">{{ __('messages.navbar_links.menu_text') }}</label>
                                                                    <input type="text"
                                                                           class="form-control @error($sub_key . '.link_name') is-invalid @enderror"
                                                                           wire:model="sub_link.{{ $sub_key }}.link_name">
                                                                    @error($sub_key . '.link_name')
                                                                    <div
                                                                        class="invalid-feedback">{{ $message }}</div> @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label">{{ __('messages.navbar_links.link') }}</label>
                                                                    <input type="url" class="form-control"
                                                                           wire:model="sub_link.{{ $sub_key }}.link_url"
                                                                           placeholder="https://example.com">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label">{{ __('messages.navbar_links.icon') }}</label>
                                                                    <input type="text" class="form-control"
                                                                           wire:model="sub_link.{{ $sub_key }}.link_icon"
                                                                           placeholder="far fa-star">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label">{{ __('messages.navbar_links.open_in') }}</label>
                                                                    <select class="form-select"
                                                                            wire:model="sub_link.{{ $sub_key }}.link_open">
                                                                        @foreach (LinkUrlTargetEnum::cases() as $case)
                                                                            <option
                                                                                value="{{ $case->value }}">{{ LinkUrlTargetEnum::fromValue($case->value) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="d-flex justify-content-end gap-2">
                                                                    <button
                                                                        wire:confirm="{{ __('messages.confirm_remove') }}"
                                                                        wire:click="deleteNavbarLink('{{ $sub_key }}')"
                                                                        class="btn btn-sm btn-danger">
                                                                        {{ __('messages.remove') }}
                                                                    </button>
                                                                    <button wire:click="updateSubLink('{{ $sub_key }}')"
                                                                            class="btn btn-sm btn-success d-flex align-items-center">
                                                                        <i class="bi bi-check-lg me-1"></i>{{ __('messages.save') }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @error($sub_key . '.link_name')
                                                        <div class="alert alert-danger my-2">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @empty
                                                    <div class="empty-state-card">
                                                        <img src="{{ asset('assets/dashboard/images/empty.svg') }}"
                                                             alt="{{ __('messages.navbar_links.no_links_icon_alt') }}"
                                                             class="empty-state-icon">
                                                        <p class="empty-state-title">{{ __('messages.navbar_links.no_sub_links_found') }}</p>
                                                        <p class="empty-state-description">{{ __('messages.navbar_links.click_add_button_to_create_sub_link') }}</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @else
                                        <div class="empty-state-card">
                                            <img src="{{ asset('assets/dashboard/images/empty.svg') }}"
                                                 alt="{{ __('messages.navbar_links.no_links_icon_alt') }}"
                                                 class="empty-state-icon">
                                            <p class="empty-state-title">{{ __('messages.navbar_links.no_sub_links_found') }}</p>
                                            <p class="empty-state-description">{{ __('messages.navbar_links.click_add_button_to_create_sub_link') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ======================= Footer Links ======================= --}}
                @if(config('features.nav_links_access.footer_links'))
                    <div class="{{ $gridClass }}">
                        <div class="card card-flush equal-card">
                            <div class="card-header p-4 d-flex justify-content-between align-items-center gap-2">
                                <h3 class="card-title mb-0">{{ __('messages.navbar_links.footer_links') }}</h3>
                                <div class="d-flex gap-2">
                                    <button wire:click="showFooterReorderModal" class="btn btn-sm btn-dark">
                                        <i class="bi bi-arrow-clockwise me-1"></i>{{ __('messages.navbar_links.order_by_creation_time') }}
                                    </button>
                                    <button wire:click="addNewFooter" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle me-1"></i>{{ __('messages.add') }}
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                @foreach (['link_order_error' => 'danger', 'link_order_success' => 'success'] as $name => $type)
                                    @error($name)
                                    <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                    </div>
                                    @enderror
                                @endforeach

                                <div class="scrollable-content">
                                    <div class="accordion" id="footerAccordion">
                                        <div wire:sortable="updateFooterLinkOrder">
                                            @forelse($this->footer_links as $key => $link)
                                                <div class="accordion-item mb-2 shadow-sm"
                                                     wire:sortable.item="{{ $link['id'] }}"
                                                     wire:key="f-link-{{ $link['id'] }}">

                                                    <h2 class="accordion-header d-flex"
                                                        id="f-heading-{{ $link['id'] }}">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center w-100 p-3">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span wire:sortable.handle class="grab-handle"><i
                                                                        class="bi bi-arrows-move"></i></span>
                                                                <input type="number" min="1" max="100"
                                                                       class="form-control form-control-sm border-0 text-center no-arrows order-input"
                                                                       wire:model.live="footer_link_order.{{ $key }}">
                                                                <span
                                                                    class="badge bg-secondary text-light">{{ $key }}</span>
                                                            </div>
                                                            @php $enum = \App\Enums\LinkStatusEnum::fromValue($link['link_status']); @endphp
                                                            <span class="badge bg-light-secondary text-gray-800">
                                                            {{ $link['link_status'] != 0 ? $enum : $key }}
                                                        </span>
                                                        </div>
                                                        <button class="accordion-button collapsed flex-shrink-0 ps-2"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#f-collapse-{{ $link['id'] }}"
                                                                aria-expanded="false"
                                                                aria-controls="f-collapse-{{ $link['id'] }}"
                                                                style="width:36px;"></button>
                                                    </h2>

                                                    <div id="f-collapse-{{ $link['id'] }}"
                                                         class="accordion-collapse collapse"
                                                         data-bs-parent="#footerAccordion">
                                                        <div class="accordion-body">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.navbar_links.menu_text') }}</label>
                                                                <input type="text"
                                                                       class="form-control @error($key . '.link_name') is-invalid @enderror"
                                                                       wire:model="footer_link.{{ $key }}.link_name">
                                                                @error($key . '.link_name')
                                                                <div
                                                                    class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.navbar_links.link') }}</label>
                                                                <input type="url" class="form-control"
                                                                       wire:model="footer_link.{{ $key }}.link_url"
                                                                       placeholder="https://example.com">
                                                            </div>

                                                            {{-- Icon picker --}}
                                                            <div class="accordion mb-3"
                                                                 id="f-iconAcc-{{ $link['id'] }}">
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="f-iconHead-{{ $link['id'] }}">
                                                                        <button class="accordion-button collapsed p-3"
                                                                                type="button" data-bs-toggle="collapse"
                                                                                data-bs-target="#f-iconCol-{{ $link['id'] }}"
                                                                                aria-expanded="false"
                                                                                aria-controls="f-iconCol-{{ $link['id'] }}">
                                                                            # {{ __('messages.navbar_links.icon') }}
                                                                        </button>
                                                                    </h2>
                                                                    <div id="f-iconCol-{{ $link['id'] }}"
                                                                         class="accordion-collapse collapse"
                                                                         data-bs-parent="#f-iconAcc-{{ $link['id'] }}">
                                                                        <div class="accordion-body">
                                                                            <div class="row g-3">
                                                                                @foreach ($this->icons as $icon)
                                                                                    <div class="col-3 text-center"
                                                                                         style="cursor:pointer;"
                                                                                         wire:click="iconSelect({{ $icon }})">
                                                                                        <img
                                                                                            src="{{ file_url($icon->icon_path) }}"
                                                                                            class="img-fluid {{ !empty($link['icon_id']) && $link['icon_id'] == $icon->id ? 'border border-primary rounded' : '' }}"
                                                                                            alt="icon">
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @error('icon_id')
                                                            <div class="text-danger">{{ $message }}</div> @enderror

                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.navbar_links.open_in') }}</label>
                                                                <select class="form-select"
                                                                        wire:model="footer_link.{{ $key }}.link_open">
                                                                    @foreach (LinkUrlTargetEnum::cases() as $case)
                                                                        <option
                                                                            value="{{ $case->value }}">{{ LinkUrlTargetEnum::fromValue($case->value) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                                <button wire:click="updateFooterLink('{{ $key }}')"
                                                                        class="btn btn-sm btn-success d-flex align-items-center">
                                                                    <i class="bi bi-check-lg me-1"></i>{{ __('messages.save') }}
                                                                </button>
                                                                <button
                                                                    wire:confirm="{{ __('messages.navbar_links.confirm_remove_link') }}"
                                                                    wire:click="deleteFooterLink('{{ $key }}')"
                                                                    class="btn btn-sm btn-danger d-flex align-items-center">
                                                                    <i class="bi bi-trash me-1"></i>{{ __('messages.delete') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="empty-state-card">
                                                    <p class="empty-state-title">{{ __('messages.navbar_links.no_links') }}</p>
                                                    <p class="empty-state-description">{{ __('messages.navbar_links.add_sub_link_button') }}</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ======================= Categories ======================= --}}
                @if(config('features.nav_links_access.categories'))
                    @canany(['categories_show','categories_create','categories_edit','categories_delete'])
                        <div class="{{ $gridClass }}">
                            <div class="card card-flush equal-card" wire:ignore>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                        <h3 class="card-title mb-0">{{ __('messages.navbar_links.categories_display_site') }}</h3>
                                    </div>
                                    <div class="scrollable-content">
                                        <ul class="list-group list-group-flush list-group-spaces"
                                            wire:sortable="updateCategoryOrder">
                                            @foreach ($this->categories as $key => $cat)
                                                <li class="list-group-item d-flex align-items-center gap-2"
                                                    wire:sortable.item="{{ $cat['id'] }}"
                                                    wire:key="cat-{{ $cat['id'] }}">
                                                    <span wire:sortable.handle class="grab-handle"><i
                                                            class="bi bi-arrows-move"></i></span>

                                                    <div class="form-check flex-grow-1 m-0 item-checkbox">
                                                        <div class="group-checkbox d-flex align-items-center gap-2">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                   id="cat-{{ $key }}" value="{{ $cat->id }}"
                                                                   wire:model.live="selectedCategories.{{ $cat->id }}.value"
                                                                   wire:change="updateSelectedCategories">
                                                            <label class="form-check-label mb-0" for="cat-{{ $key }}">
                                                                {{ $cat?->category_title }}
                                                                ({{ \App\Enums\CategoryTypeEnum::fromValue($cat?->category_type) }}
                                                                )
                                                            </label>

                                                            @if (!empty(\App\Enums\CategoryStyleEnum::available($cat?->category_type)))
                                                                <select
                                                                    wire:model.live="selectedCategories.{{ $cat->id }}.type"
                                                                    wire:change="updateSelectedCategories"
                                                                    class="form-control form-control-sm d-inline-block"
                                                                >
                                                                    <option>{{ __('messages.choose') }}</option>
                                                                    @forelse(\App\Enums\CategoryStyleEnum::available($cat?->category_type) as $case)
                                                                        <option value="{{ $case->value }}">
                                                                            {{ \App\Enums\CategoryStyleEnum::fromValue($case->value) }}
                                                                        </option>
                                                                    @empty @endforelse
                                                                </select>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcanany
                @endif

                {{-- ======================= Types ======================= --}}
                @if(config('features.nav_links_access.types'))
                    @canany(['types_show','types_create','types_edit','types_delete'])
                        <div class="{{ $gridClass }}">
                            <div class="card card-flush equal-card" wire:ignore>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                        <h3 class="card-title mb-0">{{ __('messages.navbar_links.types_display_site') }}</h3>
                                    </div>
                                    <div class="scrollable-content">
                                        <ul class="list-group list-group-flush list-group-spaces"
                                            wire:sortable="updateTypeOrder">
                                            @foreach ($this->types as $key => $type)
                                                <li class="list-group-item d-flex align-items-center gap-2"
                                                    wire:sortable.item="{{ $type['id'] }}"
                                                    wire:key="type-{{ $type['id'] }}">
                                                    <span wire:sortable.handle class="grab-handle"><i
                                                            class="bi bi-arrows-move"></i></span>
                                                    <div class="form-check flex-grow-1 m-0 item-checkbox">
                                                        <div class="group-checkbox">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                   id="type-{{ $key }}" value="{{ $type->id }}"
                                                                   wire:model.live="selectedTypes.{{ $type->id }}.value"
                                                                   wire:change="updateSelectedTypes">
                                                            <label class="form-check-label mb-0" for="type-{{ $key }}">
                                                                {{ $type?->type_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcanany
                @endif

                {{-- ======================= Social Media ======================= --}}
                @if(config('features.nav_links_access.social_media'))
                    <div class="{{ $gridClass }}">
                        <div class="card card-flush equal-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between flex-wrap align-items-center gap-2 mb-3">
                                    <h3 class="card-title mb-0">{{ __('messages.social_media_links.social_media_links') }}</h3>
                                    <div class="d-flex gap-2">
                                        <button wire:click="resetSocialMediaOrder" class="btn btn-sm btn-dark">
                                            <i class="bi bi-arrow-clockwise me-1"></i>{{ __('messages.navbar_links.order_by_creation_time') }}
                                        </button>
                                        @can('social_media_create')
                                            <button wire:click="addNewSocialMedia" class="btn btn-sm btn-success">
                                                <i class="bi bi-plus-circle me-1"></i>{{ __('messages.add') }}
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                <div class="scrollable-content">
                                    <div class="">
                                        <ul wire:sortable="updateSocialMediaOrder" class="list-group list-group-flush">
                                            @forelse($this->social_medias as $item)
                                                <li wire:sortable.item="{{ $item->id }}"
                                                    wire:key="social-media-{{ $item->id }}"
                                                    class="list-group-item d-flex align-items-center gap-3 py-3">
                                                <span wire:sortable.handle class="text-muted" style="cursor: grab;">
                                                    <i class="bi bi-arrows-move"></i>
                                                </span>

                                                    <div class="flex-shrink-0">
                                                        @if (isset($item?->icon?->icon_path))
                                                            <img width="40" src="{{ file_url($item->icon->icon_path) }}"
                                                                 class="symbol symbol-40px bg-secondary p-2 rounded"
                                                                 alt="icon">
                                                        @endif
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        {{ $item->title ?? __('messages.no_data') }}
                                                    </div>

                                                    <div class="flex-shrink-0">
                                                        <a href="{{ $item->url ?? '#' }}" target="_blank"
                                                           class="text-primary">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    </div>

                                                    <div class="flex-shrink-0">
                                                    <span class="badge bg-light-secondary">
                                                        {{ \App\Enums\LinkPosition::fromValue($item->position) }}
                                                    </span>
                                                    </div>

                                                    @canany(['social_media_edit', 'social_media_delete'])
                                                        <div class="flex-shrink-0">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-icon btn-light"
                                                                        type="button" data-bs-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    @can('social_media_edit')
                                                                        <li>
                                                                            <a class="dropdown-item text-warning d-flex justify-content-between align-items-center"
                                                                               wire:click="edit({{ $item }})">
                                                                                {{ __('messages.edit') }}
                                                                                <i class="bi bi-pencil"></i>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('social_media_delete')
                                                                        <li>
                                                                            <a class="dropdown-item text-danger d-flex justify-content-between align-items-center"
                                                                               wire:click="delete({{ $item }})">
                                                                                {{ __('messages.delete') }}
                                                                                <i class="bi bi-trash"></i>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endcanany
                                                </li>
                                            @empty
                                                <li class="list-group-item">
                                                    <div class="text-center py-4 text-muted">
                                                        {{ __('messages.no_data') }}
                                                    </div>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Modals (unchanged) --}}
    
        @include('components.modals.navbar_link_modals')
        @include('components.modals.footer_link_modals')
        @include('components.modals.social_media_modals')
    
</div>

{{-- ======================================================================
   Scripts — keep original listeners, add tiny re-init safety
====================================================================== --}}
@section('script')
    <script>
        // Bootstrap modal helpers (unchanged)
        const toggleModal = (id, show = true) => $(id).modal(show ? 'show' : 'hide');
        window.addEventListener('show_social_media_modal', () => toggleModal('#social_media', true));
        window.addEventListener('hide_social_media_modal', () => toggleModal('#social_media', false));
        window.addEventListener('show_social_media_delete', () => toggleModal('#SocialMediaDelete', true));
        window.addEventListener('hide_social_media_delete', () => toggleModal('#SocialMediaDelete', false));
        window.addEventListener('show_navbar_link_modal', () => toggleModal('#navbar_linkForm', true));
        window.addEventListener('show_footer_link_modal', () => toggleModal('#footer_linkForm', true));
        window.addEventListener('hide_navbar_link_modal', () => toggleModal('#navbar_linkForm', false));
        window.addEventListener('hide_footer_link_modal', () => toggleModal('#footer_linkForm', false));
        window.addEventListener('show-reorder-modal', () => toggleModal('#reorderModal', true));
        window.addEventListener('show-footer-reorder-modal', () => toggleModal('#footerReorderModal', true));
        window.addEventListener('hide-reorder-modal', () => toggleModal('#reorderModal', false));
        window.addEventListener('hide-footer-reorder-modal', () => toggleModal('#footerReorderModal', false));

        // Toasts (unchanged)
        const toast = (text) => Swal.fire({
            title: '{{ __('messages.alert_message.success') }}',
            text,
            icon: 'success',
            confirmButtonText: '{{ __('messages.alert_message.done') }}',
            customClass: {confirmButton: 'btn btn-success'}
        });
        document.addEventListener('DOMContentLoaded', () => {
            document.body.addEventListener('categories_order_updated', () => toast('{{ __('messages.alert_message.reorder_categories_saved') }}'));
            document.body.addEventListener('types_order_updated', () => toast('{{ __('messages.alert_message.reorder_categories_saved') }}'));
            document.body.addEventListener('navbar_links_updated', () => toast('{{ __('messages.alert_message.reorder_list_saved') }}'));
            document.body.addEventListener('footer_links_updated', () => toast('{{ __('messages.alert_message.reorder_list_saved') }}'));
            document.body.addEventListener('selected_categories_updated', () => toast('{{ __('messages.alert_message.selected_categories_saved') }}'));
            document.body.addEventListener('selected_types_updated', () => toast('{{ __('messages.alert_message.selected_types_saved') }}'));
            document.addEventListener('social_media_order_updated', () => toast('{{ __('messages.alert_message.social_media_order_saved') }}'));
        });

        // Collapse re-init on LW updates (keep)
        document.addEventListener('livewire:update', () => {
            $('[data-bs-toggle="collapse"]').collapse();
        });
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('subLinksUpdated', () => {
                setTimeout(() => {
                    $('[data-bs-toggle="collapse"]').collapse();
                }, 100);
            });
        });
    </script>
@endsection
