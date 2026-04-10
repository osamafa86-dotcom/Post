<div>
    @section('title')
        {{config('system.site_name') . ' - '}}{{ __('messages.dashboard') }}
    @endsection
    @section('style')
        <style>
            /* ========== Layout tokens (بدون أي :root جديدة) ========== */
            :where(.cmdbar,.qfilter,.kpi,.cardx,.tags) {
                background: var(--bs-card-bg);
                border: 1px solid var(--bs-border-color);
                border-radius: 16px;
                box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
            }

            .section-title {
                font-weight: 800;
                letter-spacing: .2px;
            }

            /* ===== Command bars ===== */
            .cmdbar {
                display: flex;
                gap: 18px;
                flex-wrap: wrap;
                align-items: stretch;
                padding: 14px
            }

            .cmdbar__left {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center
            }

            .cmdbar__right {
                display: flex;
                gap: 10px;
                align-items: center;
                margin-inline-start: auto
            }

            .btn-ghost {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                background: color-mix(in srgb, var(--bs-body-bg) 40%, transparent);
                border: 1px solid var(--bs-border-color);
                color: var(--bs-body-color);
                padding: .6rem .9rem;
                border-radius: 10px;
                font-weight: 800;
                letter-spacing: .2px;
                transition: background .2s, border-color .2s, transform .05s, box-shadow .2s;
                cursor: pointer;
            }

            .btn-ghost:hover {
                background: color-mix(in srgb, var(--bs-primary) 8%, transparent);
                border-color: color-mix(in srgb, var(--bs-primary) 30%, var(--bs-border-color));
                box-shadow: 0 0 0 3px rgba(100, 108, 252, .16);
            }

            .btn-ghost:active {
                transform: translateY(1px)
            }

            .cmdbar .form-control, .cmdbar .form-select,
            .qfilter .form-control, .qfilter .form-select {
                background: color-mix(in srgb, var(--bs-body-bg) 60%, transparent);
                border: 1px solid var(--bs-border-color);
                color: var(--bs-body-color);
                border-radius: 10px;
            }

            .cmdbar .form-control:focus, .cmdbar .form-select:focus,
            .qfilter .form-control:focus, .qfilter .form-select:focus {
                border-color: color-mix(in srgb, var(--bs-primary) 55%, var(--bs-border-color));
                box-shadow: var(--focus-ring, 0 0 0 3px rgba(100, 108, 252, .40));
            }

            /* ===== Quick filter ===== */
            /* Quick filter layout */
            .qfilter {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 14px;
                padding: 16px 18px;
                justify-content: space-between;
            }

            /* Left side: time chips */
            .qfilter__chips {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }

            /* Right side: custom range pill */
            .qfilter__range {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                background: color-mix(in srgb, var(--bs-body-bg) 75%, transparent);
                border: 1px solid var(--bs-border-color);
                box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
                margin-inline-start: auto;
            }

            .qfilter__range-label {
                font-size: 11px;
                color: var(--bs-muted);
                margin: 0 2px;
            }

            .qfilter__range .form-control {
                width: 130px;
                padding-inline: 10px;
                height: 34px;
                font-size: 12px;
                border-radius: 10px;
                background: color-mix(in srgb, var(--bs-body-bg) 80%, transparent);
                border: 1px solid var(--bs-border-color);
                color: var(--bs-body-color);
            }

            .qfilter__range .form-control:focus {
                border-color: color-mix(in srgb, var(--bs-primary) 55%, var(--bs-border-color));
                box-shadow: 0 0 0 2px rgba(76, 14, 229, 0.16);
            }

            .qfilter__range .btn-chip {
                padding-inline: 14px;
                height: 34px;
                font-size: 12px;
            }

            /* Reuse for KPI custom range (تحت كل كارت) */
            .range-inline {
                display: inline-flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px;
                padding: 6px 10px;
                border-radius: 999px;
                background: color-mix(in srgb, var(--bs-body-bg) 80%, transparent);
                border: 1px solid var(--bs-border-color);
            }

            .range-inline span.small {
                font-size: 10px;
                color: var(--bs-muted);
            }

            .range-inline .form-control-sm {
                width: 115px;
                border-radius: 9px;
                background: transparent;
                border: 1px solid var(--bs-border-color);
                font-size: 11px;
                padding-inline: 8px;
            }

            /* Mobile: خلي الرينج يسقط تحت الشيبس بعرض كامل */
            @media (max-width: 768px) {
                .qfilter {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .qfilter__range {
                    width: 100%;
                    justify-content: flex-start;
                    border-radius: 14px;
                }

                .qfilter__range .form-control {
                    flex: 1 1 0;
                    width: auto;
                }
            }


            .btn-chip {
                --chip-bg: color-mix(in srgb, var(--bs-body-bg) 60%, transparent);
                --chip-br: var(--bs-border-color);
                --chip-fg: var(--bs-body-color);
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                padding: .5rem .9rem;
                border-radius: 999px;
                font-weight: 800;
                font-size: .9rem;
                background: var(--chip-bg);
                color: var(--chip-fg);
                border: 1px solid var(--chip-br);
                transition: background .2s, border-color .2s, box-shadow .2s, color .2s, transform .05s;
                cursor: pointer;
            }

            .btn-chip:hover {
                background: color-mix(in srgb, var(--bs-primary) 8%, var(--chip-bg));
                border-color: color-mix(in srgb, var(--bs-primary) 35%, var(--chip-br));
                box-shadow: 0 0 0 3px rgba(100, 108, 252, .14);
            }

            .btn-chip:active {
                transform: translateY(1px)
            }

            .btn-chip.active,
            .btn-chip[aria-pressed="true"] {
                background: var(--bs-primary);
                color: #fff;
                border-color: color-mix(in srgb, #ffffff 18%, var(--bs-primary));
                box-shadow: 0 0 0 3px rgba(100, 108, 252, .22);
            }

            .btn-chip .dot {
                inline-size: .55rem;
                block-size: .55rem;
                border-radius: 50%;
                background: currentColor;
                opacity: .85;
            }

            /* ===== Loading States ===== */
            .page-loader {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 99999;background: transparent;
                opacity: 0;
                animation: fadeIn 0.2s forwards;
            }

            @keyframes fadeIn {
                to { opacity: 1; }
            }

            .loader-content {
                text-align: center;
                padding: 2rem;
            }

            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 4px solid color-mix(in srgb, var(--bs-primary) 20%, transparent);
                border-top-color: var(--bs-primary);
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
                margin: 0 auto 1rem;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            .loader-text {
                color: var(--bs-body-color);
                font-size: 14px;
                font-weight: 600;
                margin-top: 0.5rem;
            }

            .kpi.loading,
            .cardx.loading {
                opacity: 0.6;
                pointer-events: none;
                transition: opacity 0.2s;
            }

            [wire\:loading] {
                display: none;
            }

            [wire\:loading].flex,
            [wire\:loading].inline-flex {
                display: none !important;
            }

            [wire\:loading].show {
                display: block;
            }

            button[wire\:loading\.attr="disabled"],
            select[wire\:loading\.attr="disabled"],
            input[wire\:loading\.attr="disabled"] {
                opacity: 0.6;
                cursor: pointer;
            }

            .range-inline {
                display: flex;
                gap: 8px;
                align-items: center;
                flex-wrap: wrap;
                margin-inline-start: auto
            }

            .range-inline .form-control {
                background: color-mix(in srgb, var(--bs-body-bg) 60%, transparent);
                border: 1px solid var(--bs-border-color);
                color: var(--bs-body-color);
                border-radius: 10px;
            }

            .range-inline .form-control:focus {
                border-color: color-mix(in srgb, var(--bs-primary) 55%, var(--bs-border-color));
                box-shadow: var(--focus-ring, 0 0 0 3px rgba(100, 108, 252, .40));
            }

            /* ===== KPI belt (6) ===== */
            .kpis {
                display: grid;
                gap: 18px;
                grid-template-columns: repeat(6, minmax(0, 1fr))
            }

            .kpi {
                grid-column: span 2 / span 2;
                min-height: 168px;
                display: flex;
                flex-direction: column;
                justify-content: space-between
            }

            .kpi__body {
                padding: 16px 16px 8px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px
            }

            .kpi__foot {
                padding: 8px 16px 14px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px
            }

            .kpi__icon {
                inline-size: 48px;
                block-size: 48px;
                border-radius: 14px;
                display: grid;
                place-items: center;
                color: var(--bs-primary);
                font-size: 1.25rem;
                background: color-mix(in srgb, var(--bs-primary) 22%, transparent);
                border: 1px solid color-mix(in srgb, var(--bs-primary) 45%, transparent);
            }

            .kpi__icon i {
                font-size: 1.25rem;
                color: inherit;
            }

            .kpi__value {
                font-size: 28px;
                font-weight: 800;
                line-height: 1
            }

            .kpi__muted {
                color: var(--bs-text-muted)
            }


            /* ===== Charts board ===== */
            .board {
                display: grid;
                gap: 18px;
                grid-template-columns: repeat(12, minmax(0, 1fr))
            }

            .cardx {
                min-height: 300px;
                display: flex;
                flex-direction: column
            }

            .cardx__head {
                padding: 14px 16px;
                border-bottom: 1px solid var(--bs-border-color);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px
            }

            .cardx__body {
                padding: 12px 16px;
                display: flex;
                flex-direction: column;
                flex: 1 1 auto
            }

            .cardx__tools {
                display: flex;
                gap: 8px;
                align-items: center
            }

            .board .span-6 {
                grid-column: span 6 / span 6
            }

            /* ====== ثبيت ارتفاع الكروت الدونات لتكون مرتبة ومتساوية ====== */
            .cardx--donut .cardx__body {
                position: relative;
            }

            .cardx--donut .chart-wrap {
                position: relative;
                width: 100%;
                height: 420px;
            }

            @media (max-width: 1200px) {
                .board .span-6 {
                    grid-column: span 12 / span 12
                }

                .cardx--donut .chart-wrap {
                    height: 360px;
                }
            }

            @media (max-width: 576px) {
                .cardx--donut .chart-wrap {
                    height: 320px;
                }
            }

            /* ===== Discovery (tags & categories) ===== */
            .discovery {
                display: grid;
                gap: 18px;
                grid-template-columns: repeat(12, minmax(0, 1fr))
            }

            .discovery .span-6 {
                grid-column: span 6 / span 6
            }

            .tags__head {
                padding: 14px 16px;
                border-bottom: 1px solid var(--bs-border-color);
                display: flex;
                justify-content: space-between;
                align-items: center
            }

            .tags__body {
                padding: 14px 16px
            }

            .tag-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 12px
            }

            .tag-chip {
                --chip-bg: color-mix(in srgb, var(--bs-primary) 10%, var(--bs-body-bg));
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: .55rem .85rem;
                border-radius: 999px;
                font-weight: 800;
                border: 1px solid color-mix(in srgb, var(--bs-primary) 28%, var(--bs-border-color));
                color: color-mix(in srgb, var(--bs-body-color) 92%, #0000);
                background: var(--chip-bg);
                transition: transform .05s, background .2s, border-color .2s, box-shadow .2s;
            }

            .tag-chip:hover {
                background: color-mix(in srgb, var(--bs-primary) 16%, var(--bs-body-bg));
                box-shadow: 0 0 0 3px rgba(100, 108, 252, .12);
            }

            .tag-badge {
                background: color-mix(in srgb, var(--bs-primary) 28%, transparent);
                font-weight: 800;
                border: 1px solid color-mix(in srgb, var(--bs-primary) 48%, transparent);
                padding: .1rem .55rem;
                border-radius: 10px;
                font-size: .8rem
            }

            /* ==== Responsive ==== */
            @media (max-width: 1400px) {
                .kpi {
                    grid-column: span 3 / span 3
                }
            }

            @media (max-width: 1200px) {
                .discovery .span-6 {
                    grid-column: span 12 / span 12
                }
            }

        </style>
    @endsection

    <div class="dash app-container " wire:init="loadCharts">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ __('messages.dashboard') }}</h1>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3"></div>
            </div>
        </div>

        <!-- Quick actions -->
        <h4 class="section-title mb-2">{{ __('messages.index.quick_actions') }}</h4>
        <div class="cmdbar mb-4">
            <div class="cmdbar__left">
                @if (config('features.permissions.posts'))
                    <a class="btn-ghost" href="{{ route('dashboard.posts.create_update_post') }}">
                        <i class="bi bi-file-earmark-text"></i> <span>{{ __('messages.index.post') }}</span>
                    </a>
                @endif
                @if (config('features.permissions.breaking_news'))
                    <a class="btn-ghost" href="{{ route('dashboard.breaking_news') }}">
                        <i class="bi bi-lightning-fill"></i> <span>{{ __('messages.index.breaking_news') }}</span>
                    </a>
                @endif
                @if (config('features.permissions.advertisements'))
                    <a class="btn-ghost" href="{{ route('dashboard.advertisements.create_update_advertisement') }}">
                        <i class="bi bi-megaphone-fill"></i> <span>{{ __('messages.index.advertisement') }}</span>
                    </a>
                @endif
                @if (config('features.permissions.settings'))
                    @canany(['settings_general_settings','settings_extra_codes','settings_custom_tags','settings_landing_page_information'])
                        <a class="btn-ghost" href="{{ route('dashboard.settings') }}">
                            <i class="bi bi-gear-fill"></i> <span>{{ __('messages.index.settings') }}</span>
                        </a>
                    @endcan
                @endif
            </div>
            <div class="cmdbar__right">
                <form wire:submit.prevent="editPost" class="d-flex align-items-center gap-2">
                    <div class="input-group">
                        <input type="text" class="form-control" wire:model.live="postNumber"
                               placeholder="{{ __('messages.index.post_number') }}"
                               aria-label="{{ __('messages.index.post_number') }}">
                        <button class="btn-ghost"><i class="bi bi-pencil-fill"></i>
                            <span>{{ __('messages.edit') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick filter -->
        <h4 class="section-title mb-2">{{ __('messages.index.quick_filter') }}</h4>
        <div class="qfilter mb-4" id="quickFilterBar">
            <div class="qfilter__chips">
                <button type="button"
                        class="btn-chip {{ ($quickFilter ?? '')==='daily' ? 'active' : '' }}"
                        data-filter="daily"
                        aria-pressed="{{ ($quickFilter ?? '')==='daily' ? 'true' : 'false' }}"
                        wire:click.prevent="quickFilter('daily')"
                        wire:loading.attr="disabled"
                        wire:target="quickFilter">
                    <span class="dot" aria-hidden="true"></span>{{ __('messages.index.daily') }}
                    <span wire:loading wire:target="quickFilter('daily')" class="spinner-border spinner-border-sm ms-1" role="status"></span>
                </button>

                <button type="button"
                        class="btn-chip {{ ($quickFilter ?? '')==='weekly' ? 'active' : '' }}"
                        data-filter="weekly"
                        aria-pressed="{{ ($quickFilter ?? '')==='weekly' ? 'true' : 'false' }}"
                        wire:click.prevent="quickFilter('weekly')"
                        wire:loading.attr="disabled"
                        wire:target="quickFilter">
                    <span class="dot" aria-hidden="true"></span>{{ __('messages.index.weekly') }}
                    <span wire:loading wire:target="quickFilter('weekly')" class="spinner-border spinner-border-sm ms-1" role="status"></span>
                </button>

                <button type="button"
                        class="btn-chip {{ ($quickFilter ?? '')==='monthly' ? 'active' : '' }}"
                        data-filter="monthly"
                        aria-pressed="{{ ($quickFilter ?? '')==='monthly' ? 'true' : 'false' }}"
                        wire:click.prevent="quickFilter('monthly')"
                        wire:loading.attr="disabled"
                        wire:target="quickFilter">
                    <span class="dot" aria-hidden="true"></span>{{ __('messages.index.monthly') }}
                    <span wire:loading wire:target="quickFilter('monthly')" class="spinner-border spinner-border-sm ms-1" role="status"></span>
                </button>

                <button type="button"
                        class="btn-chip {{ ($quickFilter ?? '')==='yearly' ? 'active' : '' }}"
                        data-filter="yearly"
                        aria-pressed="{{ ($quickFilter ?? '')==='yearly' ? 'true' : 'false' }}"
                        wire:click.prevent="quickFilter('yearly')"
                        wire:loading.attr="disabled"
                        wire:target="quickFilter">
                    <span class="dot" aria-hidden="true"></span>{{ __('messages.index.yearly') }}
                    <span wire:loading wire:target="quickFilter('yearly')" class="spinner-border spinner-border-sm ms-1" role="status"></span>
                </button>

                {{-- Custom: ناشط لو الـ quickFilter = custom أو مظهرين الرينج --}}
                <button type="button"
                        class="btn-chip {{ (($quickFilter ?? '')==='custom' || $showCustomFilter) ? 'active' : '' }}"
                        data-filter="custom"
                        aria-pressed="{{ (($quickFilter ?? '')==='custom' || $showCustomFilter) ? 'true' : 'false' }}"
                        wire:click.prevent="updateShowCustomFilter"
                        wire:loading.attr="disabled"
                        wire:target="updateShowCustomFilter">
                    <i class="bi bi-funnel"></i> {{ __('messages.index.custom') }}
                </button>
            </div>

            @if($showCustomFilter)
                <div class="qfilter__range" id="customRangeHolder">
                    <span class="qfilter__range-label">{{ __('messages.index.from') }}</span>
                    <input type="date"
                           class="form-control"
                           wire:model.change="customFromDate"
                           aria-label="{{ __('messages.index.from') }}">

                    <span class="qfilter__range-label">—</span>

                    <span class="qfilter__range-label">{{ __('messages.index.to') }}</span>
                    <input type="date"
                           class="form-control"
                           wire:model.change="customToDate"
                           aria-label="{{ __('messages.index.to') }}">

                    <button type="button"
                            class="btn-chip"
                            data-filter="custom-apply"
                            wire:click.prevent="quickFilter('custom')"
                            wire:loading.attr="disabled"
                            wire:target="quickFilter">
                        <span wire:loading.remove wire:target="quickFilter('custom')">{{ __('messages.index.edit') }}</span>
                        <span wire:loading wire:target="quickFilter('custom')">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            {{ __('messages.loading') }}...
                        </span>
                    </button>
                </div>
            @endif
        </div>


        <!-- KPI belt (6) -->
        <div class="kpis mb-5">
            {{-- 1 --}}
            <div class="kpi" wire:loading.class="loading" wire:target="quickFilter,postsFilter,from_date,to_date">
                <div class="kpi__body">
                    <div>
                        <div class="kpi__muted fw-bold">{{ __('messages.index.total_posts') }}</div>
                        <div class="kpi__value">{{$this->postsCount['current']}}</div>
                        <small class="kpi__muted d-block mt-1">
                            @if($this->postsCount['posts'] > 0)
                                {{ __('messages.index.posts') }}: {{$this->postsCount['posts']}},
                            @endif
                            @if($this->postsCount['podcasts'] > 0)
                                {{ __('messages.index.podcasts') }}: {{$this->postsCount['podcasts']}},
                            @endif
                            @if($this->postsCount['videos'] > 0)
                                {{ __('messages.index.videos') }}: {{$this->postsCount['videos']}},
                            @endif
                            @if($this->postsCount['events'] > 0)
                                {{ __('messages.index.events') }}: {{$this->postsCount['events']}}
                            @endif
                        </small>
                    </div>
                    <div class="kpi__icon"><i class="bi bi-newspaper"></i></div>
                </div>
                <div class="kpi__foot">
                    <p class="mb-0 kpi__muted">
                        @if($changePercentage > 0)
                            <span style="color: green"><i
                                    class="bi bi-arrow-up"></i> {{ round($changePercentage, 2) }}%</span>
                        @else
                            <span style="color: red"><i
                                    class="bi bi-arrow-down"></i> {{ round($changePercentage, 2) }}%</span>
                        @endif
                        @switch($postsFilter)
                            @case('daily')  <span
                                class="kpi__muted"> · {{ __('messages.index.since_today') }}</span>   @break
                            @case('weekly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_week') }}</span>@break
                            @case('monthly')<span
                                class="kpi__muted"> · {{ __('messages.index.since_last_month') }}</span>@break
                            @case('yearly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_year') }}</span>@break
                        @endswitch
                    </p>
                    <select class="form-select form-select-sm w-auto" wire:model.live="postsFilter"
                            wire:loading.attr="disabled" wire:target="postsFilter,quickFilter">
                        <option value="daily">{{ __('messages.index.daily') }}</option>
                        <option value="weekly">{{ __('messages.index.weekly') }}</option>
                        <option value="monthly">{{ __('messages.index.monthly') }}</option>
                        <option value="yearly">{{ __('messages.index.yearly') }}</option>
                        <option value="custom">{{ __('messages.index.custom') }}</option>
                    </select>
                    <div wire:loading wire:target="postsFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                </div>
                @if($postsFilter == 'custom')
                    <div class="px-3 pb-3">
                        <div class="range-inline">
                            <span class="small text-muted">{{ __('messages.from') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="from_date">
                            <span class="small text-muted">{{ __('messages.to') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="to_date">
                        </div>
                    </div>
                @endif
            </div>

            {{-- 2 --}}
            <div class="kpi" wire:loading.class="loading" wire:target="quickFilter,draftPostsFilter,from_date,to_date">
                <div class="kpi__body">
                    <div>
                        <div class="kpi__muted fw-bold">{{ __('messages.index.total_drafts') }}</div>
                        <div class="kpi__value">{{$this->draftPostsCount}}</div>
                    </div>
                    <div class="kpi__icon"><i class="bi bi-file-earmark-text"></i></div>
                </div>
                <div class="kpi__foot">
                    <p class="mb-0 kpi__muted">
                        @if($changePercentagedraft > 0)
                            <span style="color: green"><i class="bi bi-arrow-up"></i> {{ round($changePercentagedraft, 2) }}%</span>
                        @else
                            <span style="color: red"><i class="bi bi-arrow-down"></i> {{ round($changePercentagedraft, 2) }}%</span>
                        @endif
                        @switch($draftPostsFilter)
                            @case('daily')  <span
                                class="kpi__muted"> · {{ __('messages.index.since_today') }}</span>   @break
                            @case('weekly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_week') }}</span>@break
                            @case('monthly')<span
                                class="kpi__muted"> · {{ __('messages.index.since_last_month') }}</span>@break
                            @case('yearly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_year') }}</span>@break
                        @endswitch
                    </p>
                    <select class="form-select form-select-sm w-auto" wire:model.live="draftPostsFilter"
                            wire:loading.attr="disabled" wire:target="draftPostsFilter,quickFilter">
                        <option value="daily">{{ __('messages.index.daily') }}</option>
                        <option value="weekly">{{ __('messages.index.weekly') }}</option>
                        <option value="monthly">{{ __('messages.index.monthly') }}</option>
                        <option value="yearly">{{ __('messages.index.yearly') }}</option>
                        <option value="custom">{{ __('messages.index.custom') }}</option>
                    </select>
                    <div wire:loading wire:target="draftPostsFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                </div>
                @if($draftPostsFilter == 'custom')
                    <div class="px-3 pb-3">
                        <div class="range-inline">
                            <span class="small text-muted">{{ __('messages.from') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="from_date">
                            <span class="small text-muted">{{ __('messages.to') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="to_date">
                        </div>
                    </div>
                @endif
            </div>

            {{-- 3 --}}
            <div class="kpi" wire:loading.class="loading" wire:target="quickFilter,fileUploadedFilter,from_date,to_date">
                <div class="kpi__body">
                    <div>
                        <div class="kpi__muted fw-bold">{{ __('messages.index.total_uploaded') }}</div>
                        <div class="kpi__value">{{$this->fileUploadedCount['total']}}</div>
                        <small class="kpi__muted d-block mt-1">
                            @if($this->fileUploadedCount['images'] >0)
                                {{ __('messages.index.images') }}: {{$this->fileUploadedCount['images']}}
                            @endif
                            @if($this->fileUploadedCount['videos'] >0)
                                , {{ __('messages.index.videos') }}: {{$this->fileUploadedCount['videos']}}
                            @endif
                            @if($this->fileUploadedCount['audios'] >0)
                                , {{ __('messages.index.audios') }}: {{$this->fileUploadedCount['audios']}}
                            @endif
                            @if($this->fileUploadedCount['docs'] >0)
                                , {{ __('messages.index.docs') }}: {{$this->fileUploadedCount['docs']}}
                            @endif
                        </small>
                    </div>
                    <div class="kpi__icon"><i class="bi bi-cloud-upload"></i></div>
                </div>
                <div class="kpi__foot">
                    <p class="mb-0 kpi__muted">
                        @if($changePercentageUploaded > 0)
                            <span style="color: green"><i class="bi bi-arrow-up"></i> {{ round($changePercentageUploaded, 2) }}%</span>
                        @else
                            <span style="color: red"><i class="bi bi-arrow-down"></i> {{ round($changePercentageUploaded, 2) }}%</span>
                        @endif
                        @switch($fileUploadedFilter)
                            @case('daily')  <span
                                class="kpi__muted"> · {{ __('messages.index.since_today') }}</span>   @break
                            @case('weekly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_week') }}</span>@break
                            @case('monthly')<span
                                class="kpi__muted"> · {{ __('messages.index.since_last_month') }}</span>@break
                            @case('yearly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_year') }}</span>@break
                        @endswitch
                    </p>
                    <select class="form-select form-select-sm w-auto" wire:model.live="fileUploadedFilter"
                            wire:loading.attr="disabled" wire:target="fileUploadedFilter,quickFilter">
                        <option value="daily">{{ __('messages.index.daily') }}</option>
                        <option value="weekly">{{ __('messages.index.weekly') }}</option>
                        <option value="monthly">{{ __('messages.index.monthly') }}</option>
                        <option value="yearly">{{ __('messages.index.yearly') }}</option>
                        <option value="custom">{{ __('messages.index.custom') }}</option>
                    </select>
                    <div wire:loading wire:target="fileUploadedFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                </div>
                @if($fileUploadedFilter == 'custom')
                    <div class="px-3 pb-3">
                        <div class="range-inline">
                            <span class="small text-muted">{{ __('messages.from') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="from_date">
                            <span class="small text-muted">{{ __('messages.to') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="to_date">
                        </div>
                    </div>
                @endif
            </div>

            {{-- 4 --}}
            <div class="kpi" wire:loading.class="loading" wire:target="quickFilter,postViewsFilter,from_date,to_date">
                <div class="kpi__body">
                    <div>
                        <div class="kpi__muted fw-bold">{{ __('messages.posts.views') }}</div>
                        <div class="kpi__value">{{$this->postViewsCount['mainCount']}}</div>
                        <small class="kpi__muted d-block">
                            @if($this->postViewsCount['postCount'] > 0)
                                {{__('messages.index.posts')}}: {{$this->postViewsCount['postCount']}}
                            @endif
                            @if($this->postViewsCount['podcastCount'] > 0)
                                , {{__('messages.podcast_tracks.podcasts')}}: {{$this->postViewsCount['podcastCount']}}
                            @endif
                            @if($this->postViewsCount['videoCount'] > 0)
                                , {{__('messages.index.videos')}}: {{$this->postViewsCount['videoCount']}}
                            @endif
                            @if($this->postViewsCount['imageCount'] > 0)
                                , {{__('messages.index.images')}}: {{$this->postViewsCount['imageCount']}}
                            @endif
                        </small>
                    </div>
                    <div class="kpi__icon"><i class="bi bi-eye"></i></div>
                </div>
                <div class="kpi__foot">
                    <p class="mb-0 kpi__muted">
                        @if($changePercentageviews > 0)
                            <span style="color: green"><i class="bi bi-arrow-up"></i> {{ round($changePercentageviews, 2) }}%</span>
                        @else
                            <span style="color: red"><i class="bi bi-arrow-down"></i> {{ round($changePercentageviews, 2) }}%</span>
                        @endif
                        @switch($postViewsFilter)
                            @case('daily')  <span
                                class="kpi__muted"> · {{ __('messages.index.since_today') }}</span>   @break
                            @case('weekly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_week') }}</span>@break
                            @case('monthly')<span
                                class="kpi__muted"> · {{ __('messages.index.since_last_month') }}</span>@break
                            @case('yearly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_year') }}</span>@break
                        @endswitch
                    </p>
                    <select class="form-select form-select-sm w-auto" wire:model.live="postViewsFilter"
                            wire:loading.attr="disabled" wire:target="postViewsFilter,quickFilter">
                        <option value="daily">{{ __('messages.index.daily') }}</option>
                        <option value="weekly">{{ __('messages.index.weekly') }}</option>
                        <option value="monthly">{{ __('messages.index.monthly') }}</option>
                        <option value="yearly">{{ __('messages.index.yearly') }}</option>
                        <option value="custom">{{ __('messages.index.custom') }}</option>
                    </select>
                    <div wire:loading wire:target="postViewsFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                </div>
                @if($postViewsFilter == 'custom')
                    <div class="px-3 pb-3">
                        <div class="range-inline">
                            <span class="small text-muted">{{ __('messages.from') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="from_date">
                            <span class="small text-muted">{{ __('messages.to') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="to_date">
                        </div>
                    </div>
                @endif
            </div>

            {{-- 5 --}}
            <div class="kpi" wire:loading.class="loading" wire:target="quickFilter,totalVisitorsFilter,from_date,to_date">
                <div class="kpi__body">
                    <div>
                        <div class="kpi__muted fw-bold">{{ __('messages.index.total_visitors') }}</div>
                        <div class="kpi__value">{{$this->totalVisitors}}</div>
                    </div>
                    <div class="kpi__icon"><i class="bi bi-people"></i></div>
                </div>
                <div class="kpi__foot">
                    <p class="mb-0 kpi__muted">
                        @if($changePercentageVisitors > 0)
                            <span style="color: green"><i class="bi bi-arrow-up"></i> {{ round($changePercentageVisitors, 2) }}%</span>
                        @else
                            <span style="color: red"><i class="bi bi-arrow-down"></i> {{ round($changePercentageVisitors, 2) }}%</span>
                        @endif
                        @switch($totalVisitorsFilter)
                            @case('daily')  <span
                                class="kpi__muted"> · {{ __('messages.index.since_today') }}</span>   @break
                            @case('weekly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_week') }}</span>@break
                            @case('monthly')<span
                                class="kpi__muted"> · {{ __('messages.index.since_last_month') }}</span>@break
                            @case('yearly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_year') }}</span>@break
                        @endswitch
                    </p>
                    <select class="form-select form-select-sm w-auto" wire:model.live="totalVisitorsFilter"
                            wire:loading.attr="disabled" wire:target="totalVisitorsFilter,quickFilter">
                        <option value="daily">{{ __('messages.index.daily') }}</option>
                        <option value="weekly">{{ __('messages.index.weekly') }}</option>
                        <option value="monthly">{{ __('messages.index.monthly') }}</option>
                        <option value="yearly">{{ __('messages.index.yearly') }}</option>
                        <option value="custom">{{ __('messages.index.custom') }}</option>
                    </select>
                    <div wire:loading wire:target="totalVisitorsFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                </div>
                @if($totalVisitorsFilter == 'custom')
                    <div class="px-3 pb-3">
                        <div class="range-inline">
                            <span class="small text-muted">{{ __('messages.from') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="from_date">
                            <span class="small text-muted">{{ __('messages.to') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="to_date">
                        </div>
                    </div>
                @endif
            </div>

            {{-- 6 --}}
            <div class="kpi" wire:loading.class="loading" wire:target="quickFilter,participantsFilter,from_date,to_date">
                <div class="kpi__body">
                    <div>
                        <div class="kpi__muted fw-bold">{{ __('messages.index.active_participants') }}</div>
                        <div class="kpi__value">{{$this->totalParticipants['total']}}</div>
                        <small class="kpi__muted d-block mt-1">
                            @if($this->totalParticipants['authors'] >0)
                                {{ __('messages.ParticipantTypeEnum.authors') }}
                                : {{$this->totalParticipants['authors']}},
                            @endif
                            @if($this->totalParticipants['presenters'] >0)
                                {{ __('messages.ParticipantTypeEnum.presenters') }}
                                : {{$this->totalParticipants['presenters']}},
                            @endif
                            @if($this->totalParticipants['guests'] >0)
                                {{ __('messages.ParticipantTypeEnum.guests') }}: {{$this->totalParticipants['guests']}},
                            @endif
                            @if($this->totalParticipants['clients'] >0)
                                {{ __('messages.ParticipantTypeEnum.clients') }}
                                : {{$this->totalParticipants['clients']}},
                            @endif
                            @if($this->totalParticipants['supporters'] >0)
                                {{ __('messages.ParticipantTypeEnum.supporters') }}
                                : {{$this->totalParticipants['supporters']}},
                            @endif
                            @if($this->totalParticipants['team'] >0)
                                {{ __('messages.ParticipantTypeEnum.team') }}: {{$this->totalParticipants['team']}},
                            @endif
                            @if($this->totalParticipants['partners'] >0)
                                {{ __('messages.ParticipantTypeEnum.partners') }}
                                : {{$this->totalParticipants['partners']}}
                            @endif
                        </small>
                    </div>
                    <div class="kpi__icon"><i class="bi bi-person-badge"></i></div>
                </div>
                <div class="kpi__foot">
                    <p class="mb-0 kpi__muted">
                        @if($changePercentageParticipants > 0)
                            <span style="color: green"><i class="bi bi-arrow-up"></i> {{ round($changePercentageParticipants, 2) }}%</span>
                        @else
                            <span style="color: red"><i class="bi bi-arrow-down"></i> {{ round($changePercentageParticipants, 2) }}%</span>
                        @endif
                        @switch($participantsFilter)
                            @case('daily')  <span
                                class="kpi__muted"> · {{ __('messages.index.since_today') }}</span>   @break
                            @case('weekly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_week') }}</span>@break
                            @case('monthly')<span
                                class="kpi__muted"> · {{ __('messages.index.since_last_month') }}</span>@break
                            @case('yearly') <span
                                class="kpi__muted"> · {{ __('messages.index.since_last_year') }}</span>@break
                        @endswitch
                    </p>
                    <select class="form-select form-select-sm w-auto" wire:model.live="participantsFilter"
                            wire:loading.attr="disabled" wire:target="participantsFilter,quickFilter">
                        <option value="daily">{{ __('messages.index.daily') }}</option>
                        <option value="weekly">{{ __('messages.index.weekly') }}</option>
                        <option value="monthly">{{ __('messages.index.monthly') }}</option>
                        <option value="yearly">{{ __('messages.index.yearly') }}</option>
                        <option value="custom">{{ __('messages.index.custom') }}</option>
                    </select>
                    <div wire:loading wire:target="participantsFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                </div>
                @if($participantsFilter == 'custom')
                    <div class="px-3 pb-3">
                        <div class="range-inline">
                            <span class="small text-muted">{{ __('messages.from') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="from_date">
                            <span class="small text-muted">{{ __('messages.to') }}</span>
                            <input type="date" class="form-control form-control-sm" wire:model.change="to_date">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Charts -->
        <h4 class="section-title mb-2">{{ __('messages.index.charts') }}</h4>
        @if(!$chartsLoaded)
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2 small">{{ __('messages.loading') ?? 'Loading...' }}</p>
            </div>
        @endif
        <div class="board mb-5" @if(!$chartsLoaded) style="opacity: 0.4; pointer-events: none;" @endif>
            <div class="cardx span-6" wire:loading.class="loading" wire:target="visitorsChartFilter,visitorsChartDateBeginFilter,visitorsChartDateEndFilter,quickFilter,loadCharts">
                <div class="cardx__head">
                    <h5 class="mb-0">{{ __('messages.index.visitors_chart') }}</h5>
                    <div class="cardx__tools">
                        <select class="form-select form-select-sm me-2 w-auto" wire:model.live="visitorsChartFilter" wire:loading.attr="disabled" wire:target="visitorsChartFilter,quickFilter">
                            <option value="daily">{{ __('messages.index.daily') }}</option>
                            <option value="weekly">{{ __('messages.index.weekly') }}</option>
                            <option value="monthly">{{ __('messages.index.monthly') }}</option>
                            <option value="yearly">{{ __('messages.index.yearly') }}</option>
                            <option value="custom">{{ __('messages.index.custom') }}</option>
                        </select>
                        @if($visitorsChartFilter =='custom')
                            <input type="date" class="form-control form-control-sm me-2" style="width: auto;"
                                   wire:model.change="visitorsChartDateBeginFilter" wire:loading.attr="disabled" wire:target="visitorsChartDateBeginFilter">
                            <input type="date" class="form-control form-control-sm" style="width: auto;"
                                   wire:model.change="visitorsChartDateEndFilter" wire:loading.attr="disabled" wire:target="visitorsChartDateEndFilter">
                        @endif
                        <div wire:loading wire:target="visitorsChartFilter,visitorsChartDateBeginFilter,visitorsChartDateEndFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                    </div>
                </div>
                <div class="cardx__body">
                    <canvas id="visitorsChart"></canvas>
                </div>
            </div>

            <div class="cardx span-6" wire:loading.class="loading" wire:target="postViewsChartFilter,postViewsChartDateBeginFilter,postViewsChartDateEndFilter,quickFilter">
                <div class="cardx__head">
                    <h5 class="mb-0">{{ __('messages.index.views_chart') }}</h5>
                    <div class="cardx__tools">
                        <select class="form-select form-select-sm me-2 w-auto" wire:model.live="postViewsChartFilter" wire:loading.attr="disabled" wire:target="postViewsChartFilter,quickFilter">
                            <option value="daily">{{ __('messages.index.daily') }}</option>
                            <option value="weekly">{{ __('messages.index.weekly') }}</option>
                            <option value="monthly">{{ __('messages.index.monthly') }}</option>
                            <option value="yearly">{{ __('messages.index.yearly') }}</option>
                            <option value="custom">{{ __('messages.index.custom') }}</option>
                        </select>
                        @if($postViewsChartFilter == 'custom')
                            <input type="date" class="form-control form-control-sm me-1" style="width: auto;"
                                   wire:model.change="postViewsChartDateBeginFilter" wire:loading.attr="disabled" wire:target="postViewsChartDateBeginFilter">
                            <input type="date" class="form-control form-control-sm" style="width: auto;"
                                   wire:model.change="postViewsChartDateEndFilter" wire:loading.attr="disabled" wire:target="postViewsChartDateEndFilter">
                        @endif
                        <div wire:loading wire:target="postViewsChartFilter,postViewsChartDateBeginFilter,postViewsChartDateEndFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                    </div>
                </div>
                <div class="cardx__body">
                    <canvas id="viewsChart"></canvas>
                </div>
            </div>

            <div class="cardx span-6 cardx--donut" wire:loading.class="loading" wire:target="platformUsageFilter,quickFilter">
                <div class="cardx__head">
                    <h5 class="mb-0">{{ __('messages.index.device_usage') }}</h5>
                    <div class="cardx__tools">
                        <select wire:model.live="platformUsageFilter" class="form-select form-select-sm w-auto" wire:loading.attr="disabled" wire:target="platformUsageFilter,quickFilter">
                            <option value="daily">{{ __('messages.index.daily') }}</option>
                            <option value="weekly">{{ __('messages.index.weekly') }}</option>
                            <option value="monthly">{{ __('messages.index.monthly') }}</option>
                            <option value="yearly">{{ __('messages.index.yearly') }}</option>
                        </select>
                        <div wire:loading wire:target="platformUsageFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                    </div>
                </div>
                <div class="cardx__body" wire:ignore>
                    <div class="chart-wrap">
                        <canvas id="platformUsageChart" style="width:100%;height:100%"></canvas>
                    </div>
                </div>
            </div>

            <div class="cardx span-6 cardx--donut" wire:loading.class="loading" wire:target="trafficSourcesChartFilter,trafficSourcesChartDateBeginFilter,trafficSourcesChartDateEndFilter,quickFilter">
                <div class="cardx__head">
                    <h5 class="mb-0">{{ __('messages.index.traffic_sources') }}</h5>
                    <div class="d-flex align-items-center">
                        @if($trafficSourcesChartFilter=='custom')
                            <label for="trafficFromDate"
                                   class="me-2 mb-0 small text-muted">{{ __('messages.index.from') }}</label>
                            <input type="date" id="trafficFromDate" class="form-control form-control-sm me-2"
                                   style="width: auto;" wire:model.change="trafficSourcesChartDateBeginFilter" wire:loading.attr="disabled" wire:target="trafficSourcesChartDateBeginFilter">
                            <label for="trafficToDate"
                                   class="me-2 mb-0 small text-muted">{{ __('messages.index.to') }}</label>
                            <input type="date" id="trafficToDate" class="form-control form-control-sm me-2"
                                   style="width: auto;"
                                   wire:model.change="trafficSourcesChartDateEndFilter" wire:loading.attr="disabled" wire:target="trafficSourcesChartDateEndFilter">
                        @endif
                        <select wire:model.live="trafficSourcesChartFilter"
                                class="form-select form-select-sm w-auto" wire:loading.attr="disabled" wire:target="trafficSourcesChartFilter,quickFilter">
                            <option value="daily">{{ __('messages.index.daily') }}</option>
                            <option value="weekly">{{ __('messages.index.weekly') }}</option>
                            <option value="monthly">{{ __('messages.index.monthly') }}</option>
                            <option value="yearly">{{ __('messages.index.yearly') }}</option>
                            <option value="custom">{{ __('messages.index.custom') }}</option>
                        </select>
                        <div wire:loading wire:target="trafficSourcesChartFilter,trafficSourcesChartDateBeginFilter,trafficSourcesChartDateEndFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                    </div>
                </div>
                <div class="cardx__body" wire:ignore>
                    <div class="chart-wrap">
                        <canvas id="trafficSourcesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitors Map -->
        <h4 class="section-title mb-2 mt-4">{{ __('messages.index.visitors_map') }}</h4>
        <div class="board mb-5">
            <div class="cardx" style="grid-column: span 12 / span 12;" wire:loading.class="loading" wire:target="visitorsMapFilter,visitorsMapDateBeginFilter,visitorsMapDateEndFilter,quickFilter">
                <div class="cardx__head">
                    <h5 class="mb-0">{{ __('messages.index.visitors_by_country') }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="countrySearch" class="form-control form-control-sm"
                               style="width: 200px;" placeholder="@if(app()->getLocale() == 'ar')ابحث عن دولة...@else Search country...@endif">
                        @if($visitorsMapFilter=='custom')
                            <input type="date" class="form-control form-control-sm" style="width: auto;"
                                   wire:model.change="visitorsMapDateBeginFilter" wire:loading.attr="disabled" wire:target="visitorsMapDateBeginFilter">
                            <input type="date" class="form-control form-control-sm" style="width: auto;"
                                   wire:model.change="visitorsMapDateEndFilter" wire:loading.attr="disabled" wire:target="visitorsMapDateEndFilter">
                        @endif
                        <select wire:model.live="visitorsMapFilter" class="form-select form-select-sm w-auto" wire:loading.attr="disabled" wire:target="visitorsMapFilter,quickFilter">
                            <option value="daily">{{ __('messages.index.daily') }}</option>
                            <option value="weekly">{{ __('messages.index.weekly') }}</option>
                            <option value="monthly">{{ __('messages.index.monthly') }}</option>
                            <option value="yearly">{{ __('messages.index.yearly') }}</option>
                            <option value="custom">{{ __('messages.index.custom') }}</option>
                        </select>
                        <div wire:loading wire:target="visitorsMapFilter,visitorsMapDateBeginFilter,visitorsMapDateEndFilter,quickFilter" class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                    </div>
                </div>
                <div class="cardx__body" wire:ignore>
                    <canvas id="visitorsMapChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Centralized Page Loader -->
        <div wire:loading wire:target="quickFilter,postsFilter,draftPostsFilter,fileUploadedFilter,postViewsFilter,totalVisitorsFilter,participantsFilter,visitorsChartFilter,postViewsChartFilter,platformUsageFilter,trafficSourcesChartFilter,visitorsMapFilter,from_date,to_date,customFromDate,customToDate,visitorsChartDateBeginFilter,visitorsChartDateEndFilter,postViewsChartDateBeginFilter,postViewsChartDateEndFilter,trafficSourcesChartDateBeginFilter,trafficSourcesChartDateEndFilter,visitorsMapDateBeginFilter,visitorsMapDateEndFilter" class="page-loader">
            <div class="loader-content">
                <div class="loading-spinner"></div>
                <div class="loader-text">{{ __('messages.loading') }}...</div>
            </div>
        </div>

        <!-- Discovery (tags & categories) -->
        <div class="discovery mb-5">
            <div class="tags span-6">
                <div class="tags__head">
                    <h5 class="mb-0">{{ __('messages.index.most_used_tags') }}</h5>
                </div>
                <div class="tags__body">
                    <div class="tag-chips">
                        @if (config('features.permissions.posts') && $this->mostUsedTags->isNotEmpty())
                            @foreach ($this->mostUsedTags as $tag)
                                <a href="{{ route('dashboard.posts' , ['type' => 'tag' , 'id' => $tag->tags?->id]) }}">
                                    <div class="tag-chip">
                                        <span>{{ $tag->tags?->tag_name ?? 'Unknown' }}</span>
                                        <span class="tag-badge">{{ $tag->usage_count }}</span>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                        @if (config('features.permissions.materials') && $this->materialMostUsedTags->isNotEmpty())
                            @foreach ($this->materialMostUsedTags as $tag)
                                <a href="{{ route('dashboard.materials' , ['type' => 'tag' , 'id' => $tag->tags?->id]) }}">
                                    <div class="tag-chip">
                                        <span>{{ $tag->tags?->tag_name ?? 'Unknown' }}</span>
                                        <span class="tag-badge">{{ $tag->usage_count }}</span>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="tags span-6">
                <div class="tags__head">
                    <h5 class="mb-0">{{ __('messages.index.most_used_categories') }}</h5>
                </div>
                <div class="tags__body">
                    <div class="tag-chips">
                        @if (config('features.permissions.posts'))
                            @foreach ($this->mostUsedCategories as $category)
                                <a href="{{ route('dashboard.posts' , [ 'type' => 'category' , 'id' => $category->categories?->id ]) }}">
                                    <div class="tag-chip">
                                        <span>{{ $category->categories?->category_title ?? 'Unknown' }}</span>
                                        <span class="tag-badge">{{ $category->usage_count }}</span>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                        @if (config('features.permissions.materials'))
                            @foreach ($this->matreialMostUsedCategories as $category)
                                <a href="{{ route('dashboard.materials' , [ 'type' => 'category' , 'id' => $category->categories?->id ]) }}">
                                    <div class="tag-chip">
                                        <span>{{ $category->categories?->category_title ?? 'Unknown' }}</span>
                                        <span class="tag-badge">{{ $category->usage_count }}</span>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-geo@4"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2"></script>
        <script>
            let platformUsageChart = null;

            window.addEventListener('platformUsageChartUpdated', event => {
                updatePlatformUsageChart(event.detail[0] || event.detail);
            });

            function updatePlatformUsageChart(newData) {
                if (!platformUsageChart) {
                    initializePlatformUsageChart();
                    return;
                }
                platformUsageChart.data = newData;
                platformUsageChart.update();
                adjustDonutLegend(platformUsageChart);
            }

            function getTextColor() {
                return document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#fff' : '#000';
            }

            function getChartOptions() {
                const color = getTextColor();
                return {
                    maintainAspectRatio: false,
                    plugins: {legend: {labels: {color}}},
                    scales: {x: {ticks: {color}}, y: {ticks: {color}}}
                };
            }

            function getDonutOptions() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {usePointStyle: true, padding: 20, font: {size: 12}, color: getTextColor()}
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const label = ctx.label || '';
                                    const v = ctx.raw || 0;
                                    const t = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const p = t ? Math.round((v / t) * 100) : 0;
                                    return `${label}: ${v} (${p}%)`;
                                }
                            }
                        }
                    }
                }
            }

            function initializePlatformUsageChart() {
                const el = document.getElementById('platformUsageChart');
                if (!el) return;
                if (platformUsageChart) {
                    platformUsageChart.destroy();
                    platformUsageChart = null;
                }
                platformUsageChart = new Chart(el.getContext('2d'), {
                    type: 'doughnut',
                    data: {labels: [], datasets: []},
                    options: getDonutOptions()
                });
                adjustDonutLegend(platformUsageChart);
            }

            /* Chart defaults */
            Chart.defaults.animation.duration = 0;
            const allCharts = [];

            function updateChartsTheme() {
                const color = getTextColor();
                allCharts.forEach(c => {
                    if (c.options.plugins?.legend?.labels) c.options.plugins.legend.labels.color = color;
                    if (c.options.scales?.x?.ticks) c.options.scales.x.ticks.color = color;
                    if (c.options.scales?.y?.ticks) c.options.scales.y.ticks.color = color;
                    c.update('none');
                });
                if (platformUsageChart) {
                    platformUsageChart.options.plugins.legend.labels.color = color;
                    platformUsageChart.update('none');
                }
            }

            new MutationObserver(m => m.forEach(x => {
                if (x.attributeName === 'data-bs-theme') updateChartsTheme();
            }))
                .observe(document.documentElement, {attributes: true});

            // Charts initialized empty - data loaded via wire:init="loadCharts" dispatch events
            let VC = {labels: [], data: []};
            let PV = {labels: [], data: []};
            let TS = {labels: [], datasets: []};
            let DU = {labels: [], data: []};

            const ctxVisitors = document.getElementById('visitorsChart')?.getContext('2d');
            let visitorsChart = ctxVisitors ? new Chart(ctxVisitors, {
                type: 'line',
                data: {
                    labels: VC.labels || [],
                    datasets: [{
                        label: 'Visitors',
                        data: VC.data || [],
                        borderWidth: 2,
                        fill: true,
                        backgroundColor: 'rgba(59,130,246,0.2)',
                        borderColor: '#3B82F6'
                    }]
                },
                options: getChartOptions()
            }) : null;
            if (visitorsChart) allCharts.push(visitorsChart);

            const ctxViews = document.getElementById('viewsChart')?.getContext('2d');
            let viewsChart = ctxViews ? new Chart(ctxViews, {
                type: 'line',
                data: {
                    labels: PV.labels || [],
                    datasets: [{
                        label: 'Views',
                        data: PV.data || [],
                        borderWidth: 2,
                        fill: true,
                        backgroundColor: 'rgba(239,68,68,0.2)',
                        borderColor: '#EF4444'
                    }]
                },
                options: getChartOptions()
            }) : null;
            if (viewsChart) allCharts.push(viewsChart);

            // Traffic Sources (Doughnut)
            const ctxTrafficSources = document.getElementById('trafficSourcesChart')?.getContext('2d');
            let trafficSourcesChart = ctxTrafficSources ? new Chart(ctxTrafficSources, {
                type: 'doughnut',
                data: {
                    labels: TS.labels || [],
                    datasets: TS.datasets || []
                },
                options: getDonutOptions()
            }) : null;
            if (trafficSourcesChart) allCharts.push(trafficSourcesChart);

            // (موجود عندك في الداتا DU لكن مش مستخدم هنا — لو محتاج Pie تاني فعّل)
            // const ctxDevice = document.getElementById('deviceUsageChart')?.getContext('2d');

            function adjustDonutLegend(chart) {
                if (!chart) return;
                const w = chart.canvas.parentElement.clientWidth;
                const pos = w < 576 ? 'bottom' : 'right';
                if (chart.options.plugins?.legend) {
                    chart.options.plugins.legend.position = pos;
                    chart.update('none');
                }
            }

            window.addEventListener('resize', () => {
                adjustDonutLegend(platformUsageChart);
                adjustDonutLegend(trafficSourcesChart);
            });

            document.addEventListener("DOMContentLoaded", function () {
                initializePlatformUsageChart();

                Livewire.on('updateVisitorsChart', (chartData) => {
                    if (!ctxVisitors) return;
                    const d = chartData[0] || {labels: [], data: []};

                    // preserve previous dataset config so we don't lose styling/options
                    const prevDatasets = visitorsChart?.data?.datasets
                        ? visitorsChart.data.datasets.map(ds => ({...ds})) // shallow copy
                        : null;

                    // Destroy old chart
                    if (visitorsChart) {
                        visitorsChart.destroy();
                        visitorsChart = null;
                    }

                    // prepare datasets: if we had previous datasets keep them and only replace the first .data
                    let datasets;
                    if (prevDatasets && prevDatasets.length) {
                        datasets = prevDatasets;
                        datasets[0].data = d.data || [];
                    } else {
                        // follow original shape: single dataset with data array
                        datasets = [{data: d.data || []}];
                    }

                    // Rebuild chart without changing assignment logic for labels/data
                    visitorsChart = new Chart(ctxVisitors, {
                        type: 'line',
                        data: {
                            labels: d.labels || [],
                            datasets: datasets
                        },
                        options: getChartOptions()
                    });

                    allCharts.push(visitorsChart);
                });

// Views
                Livewire.on('updatePostViewsChart', (chartData) => {
                    if (!ctxViews) return;
                    const d = chartData[0] || {labels: [], data: []};

                    const prevDatasets = viewsChart?.data?.datasets
                        ? viewsChart.data.datasets.map(ds => ({...ds}))
                        : null;

                    if (viewsChart) {
                        viewsChart.destroy();
                        viewsChart = null;
                    }

                    let datasets;
                    if (prevDatasets && prevDatasets.length) {
                        datasets = prevDatasets;
                        datasets[0].data = d.data || [];
                    } else {
                        datasets = [{data: d.data || []}];
                    }

                    viewsChart = new Chart(ctxViews, {
                        type: 'line',
                        data: {
                            labels: d.labels || [],
                            datasets: datasets
                        },
                        options: getChartOptions()
                    });

                    allCharts.push(viewsChart);
                });


                // Add/Edit
                Livewire.on('updateAddAndEditChart', (chartData) => {
                    if (!ctxAddEdit) return; // Ensure context exists
                    const d = chartData[0] || {labels: [], datasets: []};

                    // Destroy old chart
                    if (addEditChart) {
                        addEditChart.destroy();
                        addEditChart = null;
                    }

                    // Rebuild chart
                    addEditChart = new Chart(ctxAddEdit, {
                        type: 'bar',
                        data: {
                            labels: d.labels || [],
                            datasets: d.datasets || []
                        },
                        options: getChartOptions()
                    });

                    // Register in allCharts
                    allCharts.push(addEditChart);
                });
                Livewire.on('updateTrafficSourcesChart', (chartData) => {
                    if (!ctxTrafficSources) return;
                    const d = chartData[0] || {labels: [], datasets: []};
                    if (trafficSourcesChart) {
                        trafficSourcesChart.destroy();
                        trafficSourcesChart = null;
                    }
                    trafficSourcesChart = new Chart(ctxTrafficSources, {
                        type: 'doughnut',
                        data: {
                            labels: d.labels || [],
                            datasets: d.datasets || []
                        },
                        options: getDonutOptions()
                    });
                    allCharts.push(trafficSourcesChart);
                });

                window.addEventListener('platformUsageChartUpdated', event => {
                    updatePlatformUsageChart(event.detail[0] || event.detail);
                });
            });
            document.addEventListener('livewire:load', function () {
                const bar = document.getElementById('quickFilterBar');
                if (!bar) return;

                function syncActive() {
                    bar.querySelectorAll('.btn-chip[data-filter]').forEach(btn => {
                        const key = btn.dataset.filter;
                        if (!['daily', 'weekly', 'monthly', 'yearly', 'custom'].includes(key)) return;

                        const pressed = btn.getAttribute('aria-pressed') === 'true';
                        btn.classList.toggle('active', pressed);
                    });
                }

                // أول تحميل
                syncActive();

                // بعد أي رندر من Livewire
                Livewire.hook('message.processed', () => {
                    if (!document.body.contains(bar)) return;
                    syncActive();
                });
            });

            // ========== Visitors Map Chart ==========
            let visitorsMapChart = null;
            let highlightedCountry = null;
            let visitorsMapData = {};

            // Country names in Arabic and English (كل الدول)
            const countryNamesMap = {
                // أمريكا
                'US': {ar: 'الولايات المتحدة', en: 'United States'}, 'CA': {ar: 'كندا', en: 'Canada'},
                'MX': {ar: 'المكسيك', en: 'Mexico'}, 'BR': {ar: 'البرازيل', en: 'Brazil'},
                'AR': {ar: 'الأرجنتين', en: 'Argentina'}, 'CL': {ar: 'تشيلي', en: 'Chile'},
                'CO': {ar: 'كولومبيا', en: 'Colombia'}, 'PE': {ar: 'بيرو', en: 'Peru'},
                'VE': {ar: 'فنزويلا', en: 'Venezuela'}, 'EC': {ar: 'الإكوادور', en: 'Ecuador'},
                'UY': {ar: 'أوروغواي', en: 'Uruguay'}, 'PY': {ar: 'باراغواي', en: 'Paraguay'},
                'BO': {ar: 'بوليفيا', en: 'Bolivia'}, 'CR': {ar: 'كوستاريكا', en: 'Costa Rica'},
                'PA': {ar: 'بنما', en: 'Panama'}, 'CU': {ar: 'كوبا', en: 'Cuba'},
                'DO': {ar: 'جمهورية الدومينيكان', en: 'Dominican Republic'},

                // أوروبا
                'GB': {ar: 'بريطانيا', en: 'United Kingdom'}, 'DE': {ar: 'ألمانيا', en: 'Germany'},
                'FR': {ar: 'فرنسا', en: 'France'}, 'IT': {ar: 'إيطاليا', en: 'Italy'},
                'ES': {ar: 'إسبانيا', en: 'Spain'}, 'NL': {ar: 'هولندا', en: 'Netherlands'},
                'SE': {ar: 'السويد', en: 'Sweden'}, 'NO': {ar: 'النرويج', en: 'Norway'},
                'DK': {ar: 'الدنمارك', en: 'Denmark'}, 'FI': {ar: 'فنلندا', en: 'Finland'},
                'PL': {ar: 'بولندا', en: 'Poland'}, 'RU': {ar: 'روسيا', en: 'Russia'},
                'GR': {ar: 'اليونان', en: 'Greece'}, 'PT': {ar: 'البرتغال', en: 'Portugal'},
                'BE': {ar: 'بلجيكا', en: 'Belgium'}, 'CH': {ar: 'سويسرا', en: 'Switzerland'},
                'AT': {ar: 'النمسا', en: 'Austria'}, 'CZ': {ar: 'التشيك', en: 'Czech Republic'},
                'HU': {ar: 'المجر', en: 'Hungary'}, 'RO': {ar: 'رومانيا', en: 'Romania'},
                'BG': {ar: 'بلغاريا', en: 'Bulgaria'}, 'UA': {ar: 'أوكرانيا', en: 'Ukraine'},
                'TR': {ar: 'تركيا', en: 'Turkey'}, 'IE': {ar: 'أيرلندا', en: 'Ireland'},
                'SK': {ar: 'سلوفاكيا', en: 'Slovakia'}, 'HR': {ar: 'كرواتيا', en: 'Croatia'},
                'RS': {ar: 'صربيا', en: 'Serbia'}, 'SI': {ar: 'سلوفينيا', en: 'Slovenia'},
                'LT': {ar: 'ليتوانيا', en: 'Lithuania'}, 'LV': {ar: 'لاتفيا', en: 'Latvia'},
                'EE': {ar: 'إستونيا', en: 'Estonia'}, 'IS': {ar: 'آيسلندا', en: 'Iceland'},

                // الشرق الأوسط وشمال أفريقيا
                'SA': {ar: 'السعودية', en: 'Saudi Arabia'}, 'AE': {ar: 'الإمارات', en: 'UAE'},
                'EG': {ar: 'مصر', en: 'Egypt'}, 'IL': {ar: 'فلسطين', en: 'Palestine'},
                'IQ': {ar: 'العراق', en: 'Iraq'}, 'IR': {ar: 'إيران', en: 'Iran'},
                'JO': {ar: 'الأردن', en: 'Jordan'}, 'LB': {ar: 'لبنان', en: 'Lebanon'},
                'SY': {ar: 'سوريا', en: 'Syria'}, 'YE': {ar: 'اليمن', en: 'Yemen'},
                'OM': {ar: 'عمان', en: 'Oman'}, 'KW': {ar: 'الكويت', en: 'Kuwait'},
                'QA': {ar: 'قطر', en: 'Qatar'}, 'BH': {ar: 'البحرين', en: 'Bahrain'},
                'MA': {ar: 'المغرب', en: 'Morocco'}, 'DZ': {ar: 'الجزائر', en: 'Algeria'},
                'TN': {ar: 'تونس', en: 'Tunisia'}, 'LY': {ar: 'ليبيا', en: 'Libya'},
                'SD': {ar: 'السودان', en: 'Sudan'}, 'PS': {ar: 'فلسطين', en: 'Palestine'},

                // آسيا
                'CN': {ar: 'الصين', en: 'China'}, 'JP': {ar: 'اليابان', en: 'Japan'},
                'KR': {ar: 'كوريا الجنوبية', en: 'South Korea'}, 'IN': {ar: 'الهند', en: 'India'},
                'PK': {ar: 'باكستان', en: 'Pakistan'}, 'BD': {ar: 'بنغلاديش', en: 'Bangladesh'},
                'TH': {ar: 'تايلاند', en: 'Thailand'}, 'VN': {ar: 'فيتنام', en: 'Vietnam'},
                'PH': {ar: 'الفلبين', en: 'Philippines'}, 'ID': {ar: 'إندونيسيا', en: 'Indonesia'},
                'MY': {ar: 'ماليزيا', en: 'Malaysia'}, 'SG': {ar: 'سنغافورة', en: 'Singapore'},
                'MM': {ar: 'ميانمار', en: 'Myanmar'}, 'KH': {ar: 'كمبوديا', en: 'Cambodia'},
                'LA': {ar: 'لاوس', en: 'Laos'}, 'NP': {ar: 'نيبال', en: 'Nepal'},
                'LK': {ar: 'سريلانكا', en: 'Sri Lanka'}, 'AF': {ar: 'أفغانستان', en: 'Afghanistan'},
                'KZ': {ar: 'كازاخستان', en: 'Kazakhstan'}, 'UZ': {ar: 'أوزبكستان', en: 'Uzbekistan'},
                'MN': {ar: 'منغوليا', en: 'Mongolia'}, 'KP': {ar: 'كوريا الشمالية', en: 'North Korea'},
                'TW': {ar: 'تايوان', en: 'Taiwan'}, 'HK': {ar: 'هونغ كونغ', en: 'Hong Kong'},

                // أفريقيا
                'ZA': {ar: 'جنوب أفريقيا', en: 'South Africa'}, 'NG': {ar: 'نيجيريا', en: 'Nigeria'},
                'KE': {ar: 'كينيا', en: 'Kenya'}, 'ET': {ar: 'إثيوبيا', en: 'Ethiopia'},
                'GH': {ar: 'غانا', en: 'Ghana'}, 'TZ': {ar: 'تنزانيا', en: 'Tanzania'},
                'UG': {ar: 'أوغندا', en: 'Uganda'}, 'AO': {ar: 'أنغولا', en: 'Angola'},
                'MZ': {ar: 'موزمبيق', en: 'Mozambique'}, 'ZW': {ar: 'زيمبابوي', en: 'Zimbabwe'},
                'SN': {ar: 'السنغال', en: 'Senegal'}, 'CI': {ar: 'ساحل العاج', en: 'Ivory Coast'},
                'CM': {ar: 'الكاميرون', en: 'Cameroon'}, 'ZM': {ar: 'زامبيا', en: 'Zambia'},

                // أوقيانوسيا
                'AU': {ar: 'أستراليا', en: 'Australia'}, 'NZ': {ar: 'نيوزيلندا', en: 'New Zealand'},
                'FJ': {ar: 'فيجي', en: 'Fiji'}, 'PG': {ar: 'بابوا غينيا الجديدة', en: 'Papua New Guinea'}
            };

            async function initVisitorsMapChart(newData = null) {
                if (newData) visitorsMapData = newData;
                const canvas = document.getElementById('visitorsMapChart');
                if (!canvas) return;

                // Fetch world topology
                const response = await fetch('https://unpkg.com/world-atlas@2/countries-50m.json');
                const worldData = await response.json();
                const countries = ChartGeo.topojson.feature(worldData, worldData.objects.countries).features;

                // ISO code mapping (country ID to ISO code)
                const isoMapping = {
                    '840': 'US', '124': 'CA', '484': 'MX', '76': 'BR', '32': 'AR', '152': 'CL',
                    '170': 'CO', '604': 'PE', '862': 'VE', '826': 'GB', '276': 'DE', '250': 'FR',
                    '380': 'IT', '724': 'ES', '528': 'NL', '752': 'SE', '578': 'NO', '208': 'DK',
                    '246': 'FI', '616': 'PL', '643': 'RU', '300': 'GR', '620': 'PT', '56': 'BE',
                    '756': 'CH', '40': 'AT', '203': 'CZ', '348': 'HU', '642': 'RO', '100': 'BG',
                    '804': 'UA', '792': 'TR', '682': 'SA', '784': 'AE', '818': 'EG', '376': 'IL',
                    '368': 'IQ', '364': 'IR', '400': 'JO', '422': 'LB', '760': 'SY', '887': 'YE',
                    '512': 'OM', '414': 'KW', '634': 'QA', '48': 'BH', '504': 'MA', '12': 'DZ',
                    '788': 'TN', '434': 'LY', '729': 'SD', '275': 'PS', '156': 'CN', '392': 'JP',
                    '410': 'KR', '356': 'IN', '586': 'PK', '50': 'BD', '764': 'TH', '704': 'VN',
                    '608': 'PH', '360': 'ID', '458': 'MY', '702': 'SG', '36': 'AU', '554': 'NZ',
                    '710': 'ZA', '566': 'NG', '404': 'KE'
                };

                // Prepare data
                const mapData = countries.map(country => {
                    const countryId = country.id;
                    const isoCode = isoMapping[countryId];
                    const visitors = isoCode ? (visitorsMapData[isoCode] || 0) : 0;
                    return {
                        feature: country,
                        value: visitors,
                        isoCode: isoCode
                    };
                });

                const ctx = canvas.getContext('2d');
                visitorsMapChart = new Chart(ctx, {
                    type: 'choropleth',
                    data: {
                        labels: countries.map(d => d.properties.name),
                        datasets: [{
                            label: '{{ __("messages.index.visitors") }}',
                            data: mapData,
                            backgroundColor: (context) => {
                                if (!context.raw) return '#f3f4f6';
                                const value = context.raw.value;
                                const isoCode = context.raw.isoCode;

                                // تظليل الدولة المحددة
                                if (highlightedCountry && isoCode === highlightedCountry) {
                                    return '#fbbf24'; // ذهبي للتظليل
                                }

                                if (value === 0) return '#f3f4f6';

                                // حساب النسبة من 1000
                                const maxValue = 1000;
                                const ratio = Math.min(value / maxValue, 1);

                                // اختيار اللون حسب الدولة (بناءً على ISO code)
                                const colorGroups = {
                                    // أحمر (الدول العربية)
                                    red: ['SA', 'AE', 'EG', 'IQ', 'JO', 'LB', 'SY', 'YE', 'OM', 'KW', 'QA', 'BH', 'MA', 'DZ', 'TN', 'LY', 'SD', 'PS'],
                                    // أزرق (أوروبا)
                                    blue: ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'SE', 'NO', 'DK', 'FI', 'PL', 'RU', 'GR', 'PT', 'BE', 'CH', 'AT', 'CZ', 'HU', 'RO', 'BG', 'UA', 'TR'],
                                    // أخضر (آسيا)
                                    green: ['CN', 'JP', 'KR', 'IN', 'PK', 'BD', 'TH', 'VN', 'PH', 'ID', 'MY', 'SG'],
                                    // برتقالي (أمريكا)
                                    orange: ['US', 'CA', 'MX', 'BR', 'AR', 'CL', 'CO', 'PE', 'VE'],
                                    // بنفسجي (أفريقيا)
                                    purple: ['ZA', 'NG', 'KE', 'ET', 'GH', 'TZ', 'UG'],
                                    // وردي (أوقيانوسيا)
                                    pink: ['AU', 'NZ']
                                };

                                // تحديد اللون
                                let baseColor;
                                if (colorGroups.red.includes(isoCode)) {
                                    // أحمر: من فاتح جداً إلى غامق
                                    const r = 239, g = Math.floor(68 + (180 * (1 - ratio))), b = Math.floor(68 + (180 * (1 - ratio)));
                                    return `rgb(${r}, ${g}, ${b})`;
                                } else if (colorGroups.blue.includes(isoCode)) {
                                    // أزرق: من فاتح جداً إلى غامق
                                    const r = Math.floor(59 + (196 * (1 - ratio))), g = Math.floor(130 + (125 * (1 - ratio))), b = 246;
                                    return `rgb(${r}, ${g}, ${b})`;
                                } else if (colorGroups.green.includes(isoCode)) {
                                    // أخضر: من فاتح جداً إلى غامق
                                    const r = Math.floor(34 + (200 * (1 - ratio))), g = 197, b = Math.floor(94 + (150 * (1 - ratio)));
                                    return `rgb(${r}, ${g}, ${b})`;
                                } else if (colorGroups.orange.includes(isoCode)) {
                                    // برتقالي: من فاتح جداً إلى غامق
                                    const r = 249, g = Math.floor(115 + (140 * (1 - ratio))), b = Math.floor(22 + (200 * (1 - ratio)));
                                    return `rgb(${r}, ${g}, ${b})`;
                                } else if (colorGroups.purple.includes(isoCode)) {
                                    // بنفسجي: من فاتح جداً إلى غامق
                                    const r = Math.floor(168 + (80 * (1 - ratio))), g = Math.floor(85 + (170 * (1 - ratio))), b = 247;
                                    return `rgb(${r}, ${g}, ${b})`;
                                } else if (colorGroups.pink.includes(isoCode)) {
                                    // وردي: من فاتح جداً إلى غامق
                                    const r = 236, g = Math.floor(72 + (183 * (1 - ratio))), b = Math.floor(153 + (100 * (1 - ratio)));
                                    return `rgb(${r}, ${g}, ${b})`;
                                } else {
                                    // رمادي للدول الأخرى
                                    const gray = Math.floor(200 - (100 * ratio));
                                    return `rgb(${gray}, ${gray}, ${gray})`;
                                }
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    generateLabels: () => {
                                        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                                        const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
                                        const textColor = isDark ? '#ffffff' : '#1f2937';

                                        return [
                                            {text: '0', fillStyle: '#f3f4f6', color: textColor},
                                            {text: isRTL ? 'الدول العربية' : 'Arab Countries', fillStyle: '#ef4444', color: textColor},
                                            {text: isRTL ? 'أوروبا' : 'Europe', fillStyle: '#3b82f6', color: textColor},
                                            {text: isRTL ? 'آسيا' : 'Asia', fillStyle: '#22c55e', color: textColor},
                                            {text: isRTL ? 'أمريكا' : 'America', fillStyle: '#f97316', color: textColor},
                                            {text: isRTL ? 'أفريقيا' : 'Africa', fillStyle: '#a855f7', color: textColor},
                                            {text: isRTL ? 'أوقيانوسيا' : 'Oceania', fillStyle: '#ec4899', color: textColor}
                                        ];
                                    },
                                    padding: 10,
                                    boxWidth: 20,
                                    font: {size: 11}
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
                                        const lang = isRTL ? 'ar' : 'en';
                                        const isoCode = context.raw.isoCode;
                                        const countryName = isoCode && countryNamesMap[isoCode] ?
                                            countryNamesMap[isoCode][lang] : context.raw.feature.properties.name;
                                        return countryName + ': ' + context.raw.value.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            projection: {
                                axis: 'x',
                                projection: 'equalEarth'
                            }
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                initVisitorsMapChart();

                // Country Search
                const searchInput = document.getElementById('countrySearch');
                if (searchInput) {
                    searchInput.addEventListener('input', (e) => {
                        const searchTerm = e.target.value.toLowerCase().trim();

                        if (!searchTerm) {
                            searchInput.style.borderColor = '';
                            searchInput.style.backgroundColor = '';
                            highlightedCountry = null;
                            if (visitorsMapChart) visitorsMapChart.update();
                            return;
                        }

                        if (!visitorsMapChart) return;

                        const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
                        const lang = isRTL ? 'ar' : 'en';

                        // Find matching country
                        let foundCountry = null;
                        for (const [code, names] of Object.entries(countryNamesMap)) {
                            if (names.ar.toLowerCase().includes(searchTerm) ||
                                names.en.toLowerCase().includes(searchTerm)) {
                                foundCountry = code;
                                break;
                            }
                        }

                        if (foundCountry) {
                            const visitors = visitorsMapData[foundCountry] || 0;
                            const countryName = countryNamesMap[foundCountry][lang];

                            // تظليل الدولة على الخريطة
                            highlightedCountry = foundCountry;
                            visitorsMapChart.update();

                            // تغيير لون الحقل للأخضر
                            searchInput.style.borderColor = '#10b981';
                            searchInput.style.borderWidth = '2px';

                            // عرض رسالة في placeholder
                            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                            searchInput.style.backgroundColor = isDark ? '#065f46' : '#d1fae5';

                            // عرض النتيجة في console
                            console.log(`✅ تم العثور على: ${countryName} - الزيارات: ${visitors.toLocaleString()}`);

                            // يمكن إضافة tooltip أو alert
                            searchInput.title = `${countryName}: ${visitors.toLocaleString()} ${lang === 'ar' ? 'زيارة' : 'visitors'}`;
                        } else {
                            highlightedCountry = null;
                            visitorsMapChart.update();
                            searchInput.style.borderColor = '#ef4444';
                            searchInput.style.borderWidth = '2px';
                            searchInput.style.backgroundColor = '';
                            searchInput.title = lang === 'ar' ? 'لم يتم العثور على الدولة' : 'Country not found';
                        }
                    });
                }
            });

            window.addEventListener('updateVisitorsMap', (event) => {
                const data = event.detail[0] || event.detail;
                if (visitorsMapChart) {
                    visitorsMapChart.destroy();
                }
                initVisitorsMapChart(data);
            });

            // تحديث المفتاح عند تغيير الثيم
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'data-bs-theme' || mutation.attributeName === 'dir') {
                        if (visitorsMapChart) {
                            visitorsMapChart.destroy();
                            initVisitorsMapChart();
                        }
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-bs-theme', 'dir']
            });

        </script>
    @endsection
</div>
