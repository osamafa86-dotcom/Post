@php use App\Enums\PublishEnum;use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.trash_bin_posts.trash_bin') }}
@endsection
@section('style')
    <style>
        .th-btn {
            cursor: pointer !important;
        }
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
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('messages.trash_bin_posts.trash_bin') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}" class="text-muted text-hover-primary">
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.trash_bin_posts.trash_bin') }}</li>
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
                                       placeholder="{{ __('messages.draft_posts.search_post') }}"/>
                            </div>
                            <select wire:model.live="perPage" class="form-select form-select-solid w-100px text-center">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <div class="dropdown ms-2">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="columnVisibilityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-columns-gap"></i> {{ __('messages.columns') }}
                                </button>
                                <ul class="dropdown-menu p-2" aria-labelledby="columnVisibilityDropdown">

                                    <li class="mb-2">
                                        <div class="form-check ms-2">
                                            <input class="form-check-input" type="checkbox" id="col-select-all" wire:model.live="selectAllColumns">
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
                                                    {{ __('messages.posts.' . $column) }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @can('posts_delete')
                                @if(!empty($selected))
                                    <div class="dropdown">
                                        <button class="btn btn-danger dropdown-toggle p-2 m-1" type="button"
                                                id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                aria-expanded="false" wire:key="{{uniqid()}}">
                                            {{__('messages.bulk_actions')}} {{count($selected)}}
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                            style="z-index: 10;">

                                            <li><a class="dropdown-item"

                                                   data-bs-toggle="modal" data-bs-target="#bulkpostChangeStatus">
                                                    {{__('messages.posts.draft')}}
                                                    <i class="bi bi-box-arrow-down text-dark"></i>
                                                </a>
                                            </li>


                                            <li>
                                                <button class="dropdown-item text-danger border-0 bg-transparent text-start w-100"
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
                        <!--end::Search-->
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by ID' : 'Sort all posts by ID' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by title' : 'Sort all posts by title' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by category' : 'Sort all posts by category' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by author' : 'Sort all posts by author' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by tags' : 'Sort all posts by tags' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by types' : 'Sort all posts by types' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by views' : 'Sort all posts by views' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by admin' : 'Sort all posts by admin' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by publish time' : 'Sort all posts by publish time' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by Available At' : 'Sort all posts by Available At' }}">
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
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by updates' : 'Sort all posts by updates' }}">
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
                            @if(isset($columnVisibility['actions']) && $columnVisibility['actions'])
                                @canany(['posts_show','posts_edit','posts_delete'])
                                    <th class="text-start min-w-70px">{{__('messages.actions')}}</th>
                                @endcanany
                            @endif
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->deleted_posts as $key =>  $post)
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
                                                        $bgColor = $isMain ? '#007bff' : '#6c757d';
                                                        $textColor = '#fff';
                                                        $categoryTitle = $category?->relationable?->category_title ?? __('messages.not_available');
                                                    @endphp
                                                    <span class="badge me-1 mb-1"
                                                          style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $categoryTitle }}
                                                        @if($isMain)
                                                            <i class="bi bi-star-fill ms-1"
                                                               style="font-size: 0.8em; color: #fea900;"></i>
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
                                                        $bgColor = $isMain ? '#007bff' : '#6c757d';
                                                        $textColor = '#fff';
                                                        $authorName = $author?->relationable?->name ?? $author->name ?? __('messages.not_available');
                                                    @endphp
                                                    <span class="badge me-1 mb-1"
                                                          style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $authorName }}
                                                        @if($isMain)
                                                            <i class="bi bi-star-fill ms-1"
                                                               style="font-size: 0.8em; color: #fea900;"></i>
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
                                                        $bgColor = $isMain ? '#007bff' : '#6c757d';
                                                        $textColor = '#fff';
                                                        $tagName = $tag?->relationable->tag_name ?? __('messages.not_available');
                                                    @endphp
                                                    <span class="badge me-1 mb-1"
                                                          style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $tagName }}
                                                        @if($isMain)
                                                            <i class="bi bi-star-fill ms-1"
                                                               style="font-size: 0.8em; color: #fea900;"></i>
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
                                        {{Carbon::parse($post->publish_date)->format('H:i Y-m-d') ?? __('messages.not_available')}}
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
                                               class="text-blue-600 hover:underline">
                                                {{ $post->updates }}
                                            </a>
                                        @else
                                            {{ __('messages.not_available') }}
                                        @endif
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
                                                                {{__('messages.edit')}}
                                                                <i class="bi bi-pencil text-warning"></i>
                                                            </a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                               wire:click="changeStatus({{$post}})"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#postChangeStatus">
                                                                {{__('messages.posts.draft')}}
                                                                <i class="bi bi-box-arrow-down text-dark"></i>
                                                            </a>
                                                        </li>
                                                    @endcan

                                                    {{--                                                    @endif--}}
                                                    @can('posts_delete')
                                                        <li><a class="dropdown-item text-danger"
                                                               wire:click="delete({{$post}})">
                                                                {{__('messages.delete')}}
                                                                <i class="bi bi-trash text-danger"></i>
                                                            </a>
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
                        {{$this->deleted_posts->links()}}
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
                        : {{isset($Post) ? $Post?->title : __('messages.not_available')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('messages.cancel') }}
                        </button>
                        <button wire:click="confirmDelete" class="btn btn-danger">{{ __('messages.confirm') }}</button>
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
                    <h6>{{__('messages.posts.change_status_message', ['post_title' =>isset($Post) ?  $Post?->title : __('messages.not_available')])}}</h6>
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

</div>
@section('script')
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
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_delete_modal', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.post_deleted') }}',
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
