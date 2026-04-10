@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.posts.posts')}}
@endsection
@section('style')
<style>
    .loading-overlay {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: grid;
        place-items: center;
        z-index: 99999;
    }
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

<div>
    {{-- Loading Overlay --}}
    <div wire:loading class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{__('messages.posts.posts')}}
                </h1>

                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}"
                           class="text-muted text-hover-primary">{{__('messages.dashboard')}}</a>
                    </li>

                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>

                    <li class="breadcrumb-item text-muted">{{__('messages.posts.posts')}}</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="card card-flush">
                <div class="card-header align-items-center py-4 py-lg-5">
                    <div class="w-100 d-flex flex-column gap-3">

                        {{-- Row 1: Search + Filter --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <!-- Search -->
                            <div class="input-group" style="min-width:160px; max-width:720px;">
                                <div class="input-group-prepend">
                                    <i class="bi bi-search fs-3  input-group-text"></i>
                                </div>
                                <input type="text" wire:model.live.debounce.500ms="search"
                                       class="form-control form-control-solid w-250px h-40px"
                                       placeholder="{{ __('messages.draft_posts.search_post') }} (ID, Title, Content...)"/>
                            </div>


                            <!-- Filter Offcanvas trigger -->
                            <button
                                class="btn btn-primary d-inline-flex align-items-center gap-2 filter-btn mt-2 mt-sm-0"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#filterSidebar"
                                type="button"
                                aria-controls="filterSidebar"
                            >
                                <i class="bi bi-funnel"></i>
                                <span>{{ __('messages.posts.filters') }}</span>
                            </button>
                        </div>

                        {{-- Row 2: Per page + Column visibility + Bulk actions + Add post --}}
                        <div class="d-flex  align-items-center justify-content-between gap-2">

                            <!-- Left cluster: per-page + column visibility (+ bulk if any) -->
                            <div class="d-flex  align-items-center gap-2">
                                <!-- Per page -->
                                <div class="d-flex align-items-center ">
                                    <label for="perPageSelect"
                                           class="form-label mb-0 me-2 small text-muted d-none d-sm-inline">
                                        {{ __('messages.posts.per_page') ?? 'Per page' }}
                                    </label>
                                    <select
                                        id="perPageSelect"
                                        wire:model.live="perPage"
                                        class="form-select form-select-solid select-different h-40px"
                                        style="min-width:60px; width:auto;"
                                        aria-label="{{ __('messages.posts.per_page') ?? 'Per page' }}"
                                    >
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>

                                <!-- Column visibility dropdown -->
                                <div class="dropdown">
                                    <button
                                        class="btn btn-secondary d-inline-flex align-items-center gap-2 h-40px"
                                        type="button"
                                        id="columnVisibilityDropdown"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                    >
                                        <i class="bi bi-columns-gap"></i>
                                        <span>{{ __('messages.columns') }}</span>
                                    </button>
                                    <ul class="dropdown-menu p-2" aria-labelledby="columnVisibilityDropdown"
                                        style="min-width:260px;">
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
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           wire:model.live="columnVisibility.{{ $column }}"
                                                           id="col-{{ $column }}">
                                                    <label class="form-check-label" for="col-{{ $column }}">
                                                        {{ __('messages.column.post.' . $column) }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                @can('posts_delete')
                                    @if(!empty($selected))
                                        <!-- Bulk actions -->
                                        <div class="dropdown">
                                            <button class="btn btn-danger dropdown-toggle p-2" type="button"
                                                    id="bulkActionsDropdown" data-bs-toggle="dropdown"
                                                    aria-expanded="false" wire:key="{{uniqid()}}">
                                                {{__('messages.bulk_actions')}} {{count($selected)}}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown"
                                                style="z-index: 10;">
                                                <li>
                                                    <a class="dropdown-item text-primary" data-bs-toggle="modal"
                                                       data-bs-target="#bulkpostChangeCategory">
                                                        <i class="bi bi-arrow-clockwise text-primary"></i>
                                                        {{ __('messages.posts.change_category') }}

                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-info" data-bs-toggle="modal"
                                                       data-bs-target="#bulkpostChangeAuthor">
                                                        <i class="bi bi-person-gear text-info"></i>
                                                        {{ __('messages.posts.change_author') }}

                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-dark" data-bs-toggle="modal"
                                                       data-bs-target="#bulkpostChangeStatus">
                                                        <i class="bi bi-box-arrow-down text-dark"></i>
                                                        {{__('messages.posts.draft')}}
                                                    </a>
                                                </li>
                                                <li>
                                                    <button
                                                        class="dropdown-item text-danger border-0 bg-transparent text-start w-100"
                                                        wire:click="deleteSelected"
                                                        wire:loading.attr="disabled"
                                                        wire:key="{{uniqid()}}"
                                                        type="button">
                                                        <i class="bi bi-trash text-danger"></i>
                                                        <span wire:loading.remove wire:target="deleteSelected">{{__('messages.delete')}}</span>
                                                        <span wire:loading wire:target="deleteSelected">{{__('messages.loading')}}...</span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                @endcan
                            </div>

                            <!-- Right cluster: primary action -->
                            @can('posts_create')
                                <div class="card-toolbar flex-shrink-0">
                                    <a href="{{route('dashboard.posts.create_update_post')}}"
                                       class="btn btn-primary d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>{{__('messages.posts.add_post')}}</span>
                                    </a>
                                </div>
                            @endcan
                        </div>

                    </div>
                </div>


                <div class="card-body pt-0 table-responsive">
                    @if(count($selected) > 0)
                        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>{{ __('messages.posts.selection_summary:') }}</strong>
                                {{ count($selected) }} {{ __('messages.posts.posts_selected') }}

                                @if($sortOnlySelected)
                                    <span class="badge bg-warning ms-2">
                                        <i class="bi bi-filter-circle"></i> {{ __('messages.posts.sorting_selected_posts_only') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary ms-2">
                                        <i class="bi bi-filter"></i> {{ __('messages.posts.sorting_all_posts') }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                @if(count($selected) > 0)
                                    <button wire:click="toggleSortMode"
                                            wire:loading.attr="disabled"
                                            class="btn btn-sm btn-outline-primary me-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sort_all_posts') : __('messages.posts.sort_only_selected_posts') }}">
                                        <i class="bi {{ $sortOnlySelected ? 'bi-filter' : 'bi-filter-circle' }}"></i>
                                        {{ $sortOnlySelected ? __('messages.posts.sort_all' ): __('messages.posts.sort_selected') }}
                                    </button>
                                @endif
                                <button wire:click="clearSelection"
                                        wire:loading.attr="disabled"
                                        class="btn btn-sm btn-outline-secondary">
                                    {{ __('messages.posts.clear_selection') }}
                                </button>
                            </div>
                        </div>
                    @endif
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            @can('posts_delete')
                                <th><input type="checkbox" wire:model.live="selectAll"></th>
                            @endcan

                            {{--                            @if($columnVisibility['order'])--}}
                            {{--                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('order_number')">--}}
                            {{--                                    {{__('messages.posts.order')}}--}}
                            {{--                                </th>--}}
                            {{--                            @endif--}}

                            @if(isset($columnVisibility['post_id']) && $columnVisibility['post_id'])

                                <th class="text-start min-w-70px th-btn {{ $sortField === 'id' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('id')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.post_id')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.post_id')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{__('messages.posts.post_id')}}
                                        <i class="bi {{ $this->getSortIcon('id') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif


                            @if(isset($columnVisibility['title']) && $columnVisibility['title'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'title' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('title')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.title')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.title')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{__('messages.posts.title')}}
                                        <i class="bi {{ $this->getSortIcon('title') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif

                            @if(isset($columnVisibility['category']) && $columnVisibility['category'])

                                <th class="text-start min-w-70px th-btn {{ $sortField === 'category' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('category')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.category')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.category')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{__('messages.posts.category')}}
                                        <i class="bi {{ $this->getSortIcon('category') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            @if(isset($columnVisibility['author']) && $columnVisibility['author'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'author' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('author')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.author')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.author')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.author') }}
                                        <i class="bi {{ $this->getSortIcon('author') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif

                            @if(isset($columnVisibility['tags']) && $columnVisibility['tags'])

                                <th class="text-start min-w-70px th-btn {{ $sortField === 'tag' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('tag')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.tags')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.tags')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.tags') }}
                                        <i class="bi {{ $this->getSortIcon('tag') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            @if(isset($columnVisibility['type']) && $columnVisibility['type'])

                                <th class="text-start min-w-70px th-btn {{ $sortField === 'type' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('type')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.types')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.types')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.types') }}
                                        <i class="bi {{ $this->getSortIcon('type') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            @if(isset($columnVisibility['views']) && $columnVisibility['views'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'views' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('views')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.views')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.views')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.views') }}
                                        <i class="bi {{ $this->getSortIcon('views') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif

                            @if(isset($columnVisibility['admin']) && $columnVisibility['admin'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'user_id' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('user_id')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.admin')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.admin')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.admin') }}
                                        <i class="bi {{ $this->getSortIcon('user_id') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            @if(isset($columnVisibility['publish_time']) && $columnVisibility['publish_time'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'publish_date' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('publish_date')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.publish_time')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.publish_time')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.publish_time') }}
                                        <i class="bi {{ $this->getSortIcon('publish_date') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            @if(isset($columnVisibility['available_at']) && $columnVisibility['available_at'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'available_at' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('available_date')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.available_at')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.available_at')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.available_at') }}
                                        <i class="bi {{ $this->getSortIcon('available_date') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            @if(isset($columnVisibility['updates']) && $columnVisibility['updates'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'updates' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('updates')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? __('messages.posts.sorting_selected_posts_by', ['field' => __('messages.posts.updates')]) : __('messages.posts.sorting_all_posts_by', ['field' => __('messages.posts.updates')]) }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{ __('messages.posts.updates') }}
                                        <i class="bi {{ $this->getSortIcon('updates') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>
                                        @if($sortOnlySelected)
                                            <i class="bi bi-filter-circle text-warning" style="font-size: 0.7rem;"></i>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            @if(config('app.active_languages_system'))
                            <th class="min-w-150px text-center">{{ __('messages.translations') }}</th>
                            @endif

                            @if(isset($columnVisibility['actions']) && $columnVisibility['actions'])
                                @canany(['posts_show','posts_edit','posts_delete'])
                                    <th class="text-start min-w-70px">{{__('messages.actions')}}</th>
                                @endcanany
                            @endif
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->posts as $key =>  $post)
                            <tr>
                                @can('posts_delete')
                                    <td class="text-start " wire:key="{{$post->id}}_" id="{{$post->id}}_">
                                        <input type="checkbox" id="{{$post->id}}" wire:model.live="selected"
                                               value="{{ $post->id }}">
                                    </td>
                                @endcan

                                {{--                                @if($columnVisibility['order'])--}}
                                {{--                                    <td class="text-start ">--}}
                                {{--                                        {{$post->order_number ?? __('messages.not_available')}}--}}
                                {{--                                    </td>--}}
                                {{--                                @endif--}}

                                @if(isset($columnVisibility['post_id']) && $columnVisibility['post_id'])

                                    <td class="text-start ">
                                        {{$post->id ?? __('messages.not_available')}}
                                    </td>
                                @endif

                                @if(isset($columnVisibility['title']) && $columnVisibility['title'])
                                    <td class="text-start  text-wrap text-break" style="max-width: 420px;">
                                        {{ $post->title ?? __('messages.not_available') }}
                                    </td>
                                @endif

                                {{-- التصنيفات --}}
                                @if(isset($columnVisibility['category']) && $columnVisibility['category'])
                                    @php
                                        $categories   = $post?->categories;
                                        $mainCategory = $post->category?->relationable;
                                    @endphp
                                    <td class="text-start  limited-cell">
                                        @if($categories->isNotEmpty())
                                            <div class="badge-container">
                                                @foreach($categories as $category)
                                                    @php
                                                        $isMain = $mainCategory && $category->relationable_id === $mainCategory->id;
                                                        $bgColor = $isMain ? 'var(--brand-primary)' : 'var(--brand-secondary)';
                                                        $textColor = '#fff';
                                                        $categoryTitle = $category?->relationable?->category_title ?? __('messages.not_available');
                                                    @endphp
                                                    <span class="badge me-1 mb-1"
                                                          style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $categoryTitle }}
                                                        @if($isMain)
                                                            <i class="bi bi-star-fill ms-1"
                                                               style="font-size: 0.8em; color: var(--brand-warning);"></i>
                                                        @endif
                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ __('messages.not_available') }}
                                        @endif
                                    </td>
                                @endif
                                {{-- الكتّاب --}}
                                @if(isset($columnVisibility['author']) && $columnVisibility['author'])
                                    @php
                                        $authors    = $post?->authors;
                                        $mainAuthor = $post->author?->relationable;
                                    @endphp
                                    <td class="text-start  limited-cell">
                                        @if($authors->isNotEmpty())
                                            <div class="badge-container">
                                                @foreach($authors as $author)
                                                    @php
                                                        $isMain = $mainAuthor && $author->relationable_id === $mainAuthor->id;
                                                        $bgColor = $isMain ? 'var(--brand-primary)' : 'var(--brand-secondary)';
                                                        $textColor = '#fff';
                                                        $authorName = $author?->relationable?->name ?? $author->name ?? __('messages.not_available');
                                                    @endphp
                                                    <span class="badge me-1 mb-1"
                                                          style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                                        {{ $authorName }}
                                                        @if($isMain)
                                                            <i class="bi bi-star-fill ms-1"
                                                               style="font-size: 0.8em; color: var(--brand-warning);"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ __('messages.not_available') }}
                                        @endif
                                    </td>
                                @endif
                                {{-- التاجز --}}
                                @if(isset($columnVisibility['tags']) && $columnVisibility['tags'])

                                    @php
                                        $tags    = $post?->tags;
                                        $mainTag = $post->tag;
                                    @endphp
                                    <td class="text-start  limited-cell">
                                        @if($tags && $tags->isNotEmpty())
                                            <div class="badge-container">
                                                @foreach($tags as $tag)
                                                    @php
                                                        $isMain = $mainTag && $tag->id === $mainTag->id;
                                                        $bgColor = $isMain ? 'var(--brand-primary)' : 'var(--brand-secondary)';
                                                        $textColor = '#fff';
                                                        $tagName = $tag?->relationable->tag_name ?? __('messages.not_available');
                                                    @endphp
                                                    <span class="badge me-1 mb-1"
                                                          style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                                        {{ $tagName }}
                                                        @if($isMain)
                                                            <i class="bi bi-star-fill ms-1"
                                                               style="font-size: 0.8em; color: var(--brand-warning);"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ __('messages.not_available') }}
                                        @endif
                                    </td>
                                @endif
                                @if(isset($columnVisibility['type']) && $columnVisibility['type'])
                                    @php
                                        $types    = $post?->types;
                                        $mainType = $post->type;

                                    @endphp
                                    <td class="text-start  limited-cell">
                                        @if($types && $types->isNotEmpty())
                                            <div class="badge-container">
                                                @foreach($types as $type)
                                                    @php
                                                        $isMain = $mainType && $type->id === $mainType->relationable->id;
                                                        $bgColor = $isMain ? '#28a745' : '#20c997';
                                                        $textColor = '#fff';
                                                        $typeName = $type?->relationable?->type_name ?? __('messages.not_available');
                                                    @endphp
                                                    <span class="badge me-1 mb-1"
                                                          style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                                        {{ $typeName }}
                                                        @if($isMain)
                                                            <i class="bi bi-star-fill ms-1"
                                                               style="font-size: 0.8em; color: #ffd700;"></i>
                                                        @endif
                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ __('messages.not_available') }}
                                        @endif
                                    </td>
                                @endif
                                @if(isset($columnVisibility['views']) && $columnVisibility['views'])

                                    <td class="text-start ">
                                        {{ number_format((int)($post->views_sum ?? 0)) }}
                                    </td>
                                @endif

                                @if(isset($columnVisibility['admin']) && $columnVisibility['admin'])
                                    <td class="text-start ">
                                        {{$post?->user->full_name ?? __('messages.not_available')}}
                                    </td>
                                @endif

                                @if(isset($columnVisibility['publish_time']) && $columnVisibility['publish_time'])
                                    <td class="text-start ">
                                        @php $publish = Carbon::parse($post->publish_date); @endphp
                                        <div class="d-flex flex-column">
        <span class="post-time">
            <i class="bi bi-clock me-1"></i> {{ $publish->format('H:i') }}
        </span>
                                            <span class="post-date">{{ $publish->format('Y-m-d') }}</span>
                                        </div>
                                    </td>

                                @endif
                                @if(isset($columnVisibility['available_at']) && $columnVisibility['available_at'])
                                    <td class="text-start ">
                                        {{Carbon::parse($post?->post_special_content?->available_date)->format('H:i Y-m-d') ?? __('messages.not_available')}}
                                    </td>
                                @endif

                                @if(isset($columnVisibility['updates']) && $columnVisibility['updates'])
                                    <td class="text-start ">
                                        @if($post->updates)
                                            <a href="{{ route('dashboard.user_logs', ['actionable_type' => \App\Enums\LoggableModelsEnum::POST->value, 'actionable_id' => $post->id]) }}"
                                               class="edit-numbers">
                                                {{ $post->updates }}
                                            </a>
                                        @else
                                            {{ __('messages.not_available') }}
                                        @endif
                                    </td>
                                @endif

                                @if(config('app.active_languages_system'))
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        @php
                                            $postLang = $post->lang ?? config('app.locale');
                                            $translationMap = [];
                                            if ($post->translation_group) {
                                                $translationMap[$postLang] = $post->id;
                                                foreach ($post->translations as $t) {
                                                    $translationMap[$t->lang] = $t->id;
                                                }
                                            } else {
                                                $translationMap[$postLang] = $post->id;
                                            }
                                        @endphp
                                        @php
                                            $projectLangs = config('features.frontend_languages', []);
                                        @endphp
                                        @foreach(config('app.languages') as $code => $lang)
                                            @if($lang['enabled'] && (!empty($projectLangs) ? ($projectLangs[$code] ?? false) : true))
                                                @if(isset($translationMap[$code]))
                                                    @if($code === $postLang)
                                                        <span class="badge badge-primary fs-8" title="{{ $lang['name'] }} ({{ __('messages.original_post') }})">
                                                            {{ strtoupper($code) }}
                                                        </span>
                                                    @else
                                                        <a href="{{ route('dashboard.posts.create_update_post', $translationMap[$code]) }}"
                                                           class="badge badge-light-success fs-8"
                                                           title="{{ __('messages.edit_translation', ['lang' => $lang['name']]) }}">
                                                            {{ strtoupper($code) }}
                                                            <i class="bi bi-pencil-fill fs-9 ms-1"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    <a href="{{ route('dashboard.posts.create_update_post') }}?translate_from={{ $post->id }}&lang={{ $code }}"
                                                       class="badge badge-light-danger fs-8"
                                                       title="{{ __('messages.translate_to', ['lang' => $lang['name']]) }}">
                                                        {{ strtoupper($code) }}
                                                        <i class="bi bi-plus fs-8 ms-1"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                @endif

                                @if(isset($columnVisibility['actions']) && $columnVisibility['actions'])
                                    @canany(['posts_show','posts_edit','posts_delete'])
                                        <td class="text-start ">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                    {{__('messages.options')}}
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                    style="z-index: 10;">
                                                    @can('posts_edit')
                                                        <li><a class="dropdown-item text-warning"
                                                               href="{{route('dashboard.posts.create_update_post',$post->id)}}">
                                                                <i class="bi bi-pencil text-warning"></i>
                                                                {{__('messages.edit')}}

                                                            </a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                               wire:click="changeStatus({{$post}})"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#postChangeStatus">
                                                                <i class="bi bi-box-arrow-down text-dark"></i>
                                                                {{__('messages.posts.draft')}}

                                                            </a>
                                                        </li>
                                                    @endcan
                                                    <li><a class="dropdown-item text-primary" target="_blank"
                                                           href="{{frontend_route('show_post',['id' => $post?->id, 'slug' => $post?->slug])}}">
                                                            <i class="bi bi-eye text-primary"></i>
                                                            {{__('messages.preview')}}

                                                        </a>
                                                    </li>
                                                    {{--                                                    @if(config('app.launch') == 'almohajer')--}}
                                                    <li>
                                                        <x-dynamic-component
                                                            :component="'layouts.main.' . config('app.launch') . '.share-post'"
                                                            :share_url="frontend_route('share', ['id' => $post->id])"
                                                            :title="$post->title"
                                                            :show_meta="true"
                                                            :post="$post"
                                                            as="li"
                                                        />
                                                    </li>
                                                    {{--                                                    @endif--}}
                                                    @can('posts_delete')
                                                        <li>
                                                            <button
                                                                class="dropdown-item text-danger border-0 bg-transparent text-start w-100"
                                                                wire:click="delete({{$post->id}})"
                                                                type="button">
                                                                <i class="bi bi-trash text-danger ms-1"></i>
                                                                {{__('messages.delete')}}

                                                            </button>
                                                        </li>

                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    @endcanany
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{$this->posts->links()}}
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

    <!--Delete Modal -->
    <div class="modal fade" id="postDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"
                        id="staticBackdropLabel">{{__('messages.posts.delete_post')}}</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.posts.confirm_delete_post')}}</h4>
                    <h6>{{__('messages.posts.delete_post_message')}}
                        : {{$Post->title ?? __('messages.not_available')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="confirmDelete" class="btn btn-danger">{{__('messages.confirm')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="postDeleteSelected" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"
                        id="staticBackdropLabel">{{__('messages.posts.delete_posts')}}   </h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.posts.confirm_delete_posts')}}</h4>
                    <h6>{{__('messages.posts.delete_posts_message')}}</h6>
                    <div class="form-group col-md-12 mb-3">
                        <input wire:model="delete_text"
                               class="form-control"
                               id="category_description"
                        >
                    </div>
                    <!-- Error Message -->
                    @if (session()->has('error'))
                        <div style="color: red; padding: 10px;">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="confirmDeleteSelected"
                                class="btn btn-danger">{{__('messages.confirm')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="bulkpostChangeCategory" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"
                        id="staticBackdropLabel">{{__('messages.posts.change_category_selected')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.posts.confirm_category_selected')}}</h4>
                    <div class="d-flex align-items-center" wire:ignore>
                        <select class="form-select"
                                wire:model.live="category_ids"
                                id="category_id"
                                multiple
                                data-control="select2"
                                data-placeholder="{{ __('messages.choose') }}">
                            @foreach($this->categories as $category)
                                <option
                                    value="{{ $category['id'] }}">{{ $category['category_title'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <h6>{{__('messages.posts.change_category_message_selected')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="bulkConfirmChangeCategory"
                                wire:loading.attr="disabled"
                                class="btn btn-primary">
                            <span wire:loading.remove>{{__('messages.confirm')}}</span>
                            <span wire:loading>{{__('messages.processing')}}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bulkpostChangeAuthor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"
                        id="staticBackdropLabel">{{__('messages.posts.change_author_selected')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.posts.confirm_author_selected')}}</h4>
                    <div class="d-flex align-items-center" wire:ignore>
                        <select class="form-select"
                                wire:model.live="author_ids"
                                id="author_id"
                                multiple
                                data-control="select2"
                                data-placeholder="{{ __('messages.posts.choose_author') }}">
                            @foreach($this->authors as $author)
                                <option value="{{ $author['id'] }}">{{ $author['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <h6>{{__('messages.posts.change_author_message_selected')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="bulkConfirmChangeAuthor"
                                wire:loading.attr="disabled"
                                class="btn btn-primary">
                            <span wire:loading.remove>{{__('messages.confirm')}}</span>
                            <span wire:loading>{{__('messages.processing')}}...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Change Status Modal -->
    <div class="modal fade" id="postChangeStatus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="staticBackdropLabel">{{__('messages.posts.change_status')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.posts.confirm_draft_status')}}</h4>
                    <h6>{{__('messages.posts.change_status_message', ['post_title' => $Post->title ?? __('messages.not_available')])}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="confirmChangeStatus" data-bs-dismiss="modal"
                                class="btn btn-primary">{{__('messages.confirm')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkpostChangeStatus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"
                        id="staticBackdropLabel">{{__('messages.posts.change_status_selected')}}  </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.posts.confirm_draft_status_selected')}}</h4>
                    <h6>{{__('messages.posts.change_status_message_selected')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="bulkConfirmChangeStatus" data-bs-dismiss="modal"
                                class="btn btn-primary">{{__('messages.confirm')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Modal (Offcanvas) -->
    <div class="offcanvas offcanvas-end custom-filter-offcanvas" tabindex="-1" id="filterSidebar"
         aria-labelledby="filterSidebarLabel">
        <div class="offcanvas-header d-flex justify-content-between align-items-center">
            <h5 class="offcanvas-title fw-bold" id="filterSidebarLabel">
                {{ __('messages.posts.filter_title') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <!-- Search Section -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted small mb-1">
                    {{ __('messages.posts.search_for_title') }} (ID, Title, Content)
                </label>
                <input type="text" class="form-control" wire:model.defer="state_filter.search_text"
                       placeholder="{{ __('messages.posts.type_a_keyword') }} (ID, Title, Content)">
            </div>

            <!-- Date Pickers Section -->
            <div class="date-pickers mb-4">
                {{--                <label class="section-subtitle fw-semibold d-block mb-2">--}}
                {{--                    {{ __('messages.posts.date_range_add') }}--}}
                {{--                </label>--}}
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">{{ __('messages.posts.from_date_add') }}</label>
                        <input type="date" class="form-control" wire:model="state_filter.from_date_add">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">{{ __('messages.posts.to_date_add') }}</label>
                        <input type="date" class="form-control" wire:model="state_filter.to_date_add">
                    </div>
                </div>
                <hr>
                {{--                <label class="section-subtitle fw-semibold d-block mb-2">--}}
                {{--                    {{ __('messages.posts.date_range_add') }}--}}
                {{--                </label>--}}
                <div class="row g-2">
                    <div class="col-6">
                        <label
                            class="form-label small text-muted mb-1">{{ __('messages.posts.from_date_news') }}</label>
                        <input type="date" class="form-control" wire:model="state_filter.from_date_news">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">{{ __('messages.posts.to_date_news') }}</label>
                        <input type="date" class="form-control" wire:model="state_filter.to_date_news">
                    </div>
                </div>
            </div>

            <!-- Accordion Filters -->
            <div class="accordion custom-filter-accordion" id="accordionExample">

                <!-- Categories -->
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="headingCategories">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseCategories" aria-expanded="false"
                                aria-controls="collapseCategories">
                            #{{ __('messages.posts.categories') }}
                        </button>
                    </h2>
                    <div id="collapseCategories" class="accordion-collapse collapse"
                         aria-labelledby="headingCategories" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ul class="list-unstyled row g-2">
                                @if($this->categories)
                                    @foreach($this->categories as $row)
                                        <li class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       wire:model.defer="state_filter.category_id"
                                                       value="{{ $row->id }}"
                                                       id="cat_{{ $row->id }}">
                                                <label class="form-check-label" for="cat_{{ $row->id }}">
                                                    {{ $row->category_title }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                <div class="accordion-item  mb-2">
                    <h2 class="accordion-header" id="headingTags">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTags" aria-expanded="false" aria-controls="collapseTags">
                            #{{ __('messages.posts.tags') }}
                        </button>
                    </h2>
                    <div id="collapseTags" class="accordion-collapse collapse"
                         aria-labelledby="headingTags" data-bs-parent="#accordionExample">
                        <div class="accordion-body">

                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control"
                                           placeholder="{{ __('Search tags...') }}"
                                           wire:model.live="tagSearch"
                                           id="tagSearchInput">

                                </div>
                            </div>

                            @if(count($selectedTags) > 0)
                                <div class="mb-3">
                                    <h6 class="fw-semibold">{{ __('Selected Tags') }}:</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($selectedTags as $tagId)
                                            @php $tag = $this->loadedTags->firstWhere('id', $tagId); @endphp
                                            @if($tag)
                                                <span class="badge bg-primary">
                                                {{ $tag->tag_name }}
                                                <button type="button" class="btn-close btn-close-white ms-1"
                                                        style="font-size: 0.6rem;"
                                                        wire:click="removeTag({{ $tagId }})"></button>
                                            </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Tags List -->
                            <ul class="list-unstyled row g-2" style="max-height: 300px; overflow-y: auto;">
                                @if($loadedTags->count() > 0)
                                    @foreach($loadedTags as $tag)
                                        <li class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       wire:model="selectedTags"
                                                       value="{{ $tag->id }}"
                                                       id="tag_{{ $tag->id }}">
                                                <label class="form-check-label" for="tag_{{ $tag->id }}">
                                                    {{ $tag->tag_name }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="col-12">
                                        <p class="text-muted text-center">{{ __('No tags found') }}</p>
                                    </li>
                                @endif
                            </ul>


                        </div>
                    </div>
                </div>

                <!-- Authors -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingAuthors">
                        <button class="accordion-button collapsed fw-semibold  " type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAuthors" aria-expanded="false" aria-controls="collapseAuthors">
                            #{{ __('messages.posts.authors') }}
                        </button>
                    </h2>
                    <div id="collapseAuthors" class="accordion-collapse collapse"
                         aria-labelledby="headingAuthors" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ul class="list-unstyled row g-2">
                                @if($this->authors)
                                    @foreach($this->authors as $author)
                                        <li class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       wire:model="state_filter.author_id"
                                                       value="{{ $author->id }}"
                                                       id="auth_{{ $author->id }}">
                                                <label class="form-check-label" for="auth_{{ $author->id }}">
                                                    {{ $author->name }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

            </div> <!-- End Accordion -->

            <!-- Apply Filter Button -->
            <div class="mt-4">
                <button class="btn btn-primary w-100" wire:click="filterResults" data-bs-dismiss="offcanvas">
                    <i class="bi bi-filter"></i> {{ __('messages.posts.apply_filter') }}
                </button>
            </div>
        </div>
    </div>


</div>
@script
<script>

    window.addEventListener('show_delete_selected_modal', event => {
        $('#postDeleteSelected').modal('show');
    })
    window.addEventListener('hide_delete_selected_modal', event => {
        $('#postDeleteSelected').modal('hide');
    })
    window.addEventListener('show_delete_modal', event => {
        $('#postDelete').modal('show');
    })
    window.addEventListener('hide_delete_modal', event => {
        $('#postDelete').modal('hide');
    })

    window.addEventListener('hide_bulk_post_change_status_modal', event => {
        $('#bulkpostChangeStatus').modal('hide');
    })

    window.addEventListener('hide_bulk_post_change_category_modal', event => {
        $('#bulkpostChangeCategory').modal('hide');
    })

    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('hide_delete_modal', function () {
            Swal.fire(
                {
                    title: '{{__('messages.alert_message.success')}}',
                    text: '{{__('messages.alert_message.post_deleted')}}',
                    icon: 'success',
                    confirmButtonText: '{{__('messages.alert_message.done')}}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                }
            );

        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('no-data', function () {
            Swal.fire(
                {
                    title: 'لا يوجد داتا محددة',
                    // text: '{{__('messages.alert_message.post_deleted')}}',
                    //  icon: 'success',
                    //  confirmButtonText: '{{__('messages.alert_message.done')}}',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    }
                }
            );

        });
    });

    // Initialize both Select2 elements
    function initializeSelect2() {
        // Initialize category Select2
        $('#category_id').select2({
            placeholder: "{{ __('messages.choose') }}",
            multiple: true,
            width: '100%'
        });

        // Initialize author Select2
        $('#author_id').select2({
            placeholder: "{{ __('messages.posts.choose_author') }}",
            multiple: true,
            width: '100%'
        });

        // Set initial values from Livewire
        const categoryValues = @this.get('category_ids') || [];
        const authorValues = @this.get('author_ids') || [];

        $('#category_id').val(categoryValues).trigger('change');
        $('#author_id').val(authorValues).trigger('change');
    }

    // Sync Select2 changes with Livewire
    function setupChangeEvents() {
        $('#category_id').off('change').on('change', function (e) {
            const selectedValues = $(this).val() || [];
        @this.set('category_ids', selectedValues)
            ;
            console.log('Category IDs updated:', selectedValues);
        });

        $('#author_id').off('change').on('change', function (e) {
            const selectedValues = $(this).val() || [];
        @this.set('author_ids', selectedValues)
            ;
            console.log('Author IDs updated:', selectedValues);
        });
    }

    // Initialize when component is ready
    setTimeout(function () {
        initializeSelect2();
        setupChangeEvents();
    }, 500);

    // Reinitialize when modals are shown (important!)
    $('#bulkpostChangeCategory').on('shown.bs.modal', function () {
        setTimeout(function () {
            $('#category_id').select2({
                placeholder: "{{ __('messages.choose') }}",
                multiple: true,
                width: '100%'
            });

            const categoryValues = @this.get('category_ids') || [];
            $('#category_id').val(categoryValues).trigger('change');
        }, 100);
    });

    $('#bulkpostChangeAuthor').on('shown.bs.modal', function () {
        setTimeout(function () {
            $('#author_id').select2({
                placeholder: "{{ __('messages.posts.choose_author') }}",
                multiple: true,
                width: '100%'
            });

            const authorValues = @this.get('author_ids') || [];
            $('#author_id').val(authorValues).trigger('change');
        }, 100);
    });

    // Update Select2 when Livewire properties change
    Livewire.hook('property.updated', (property, value, component) => {
        if (property === 'category_ids') {
            setTimeout(() => {
                $('#category_id').val(value || []).trigger('change');
            }, 50);
        }
        if (property === 'author_ids') {
            setTimeout(() => {
                $('#author_id').val(value || []).trigger('change');
            }, 50);
        }
    });
</script>
<script>

    document.addEventListener('livewire:init', () => {
        Livewire.on('preview-token-generated', (data) => {
            console.log('Token generated:', data);

            // Update the data-token attribute for the specific post
            const previewLink = document.querySelector(`[data-post-id="${data.postId}"]`);
            if (previewLink) {
                previewLink.setAttribute('data-token', data.token);

                // Open the preview in new tab
                const previewUrl = '{{ route("posts.preview", ["token" => "TOKEN_PLACEHOLDER"]) }}'.replace('TOKEN_PLACEHOLDER', data.token);
                window.open(previewUrl, '_blank');
            }
        });
    });

    Livewire.on('postCreated', (data) => {
        Swal.fire({
            icon: data.status,
            title: data.message,
            confirmButtonText: @json(__('messages.confirm'))
        });
    });

</script>
<script>
    document.addEventListener('livewire:init', () => {
        let offcanvasOpen = false;

        document.getElementById('filterSidebar').addEventListener('show.bs.offcanvas', function () {
            offcanvasOpen = true;
        });

        document.getElementById('filterSidebar').addEventListener('hide.bs.offcanvas', function () {
            offcanvasOpen = false;
        });

        Livewire.hook('message.processed', (message, component) => {
            if (offcanvasOpen) {
                const offcanvasElement = document.getElementById('filterSidebar');
                const bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
                bsOffcanvas.show();
            }
        });
    });


</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyToClipboard(text) {
        // Create a temporary input element
        const tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);

        // Select and copy the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        try {
            // Try using the modern Clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showCopySuccess();
                }).catch(err => {
                    fallbackCopy(text);
                });
            } else {
                // Fallback for older browsers
                fallbackCopy(text);
            }
        } catch (err) {
            fallbackCopy(text);
        } finally {
            // Clean up
            document.body.removeChild(tempInput);
        }
    }

    function fallbackCopy(text) {
        try {
            // Use the deprecated execCommand for fallback
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess();
            } else {
                throw new Error('Fallback copy failed');
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: @json(__('messages.link_copying_failed')),
                text: @json(__('messages.failed_copying_try')) +err,
                confirmButtonText: @json(__('messages.try_again')),
                confirmButtonColor: '#ff3a3a'
            });
        }
    }

    function showCopySuccess() {
        Swal.fire({
            icon: 'success',
            title: @json(__('messages.link_copied')),
            text: @json(__('messages.link_copied_to_clipboard')),
            confirmButtonText: @json(__('messages.confirm')),
            confirmButtonColor: '#0c6331',
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'center'
        });
    }

    document.addEventListener('livewire:init', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        Livewire.hook('message.processed', () => {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    });
    window.addEventListener('hide_bulk_post_change_author_modal', event => {
        $('#bulkpostChangeAuthor').modal('hide');
    })
</script>
@endscript
