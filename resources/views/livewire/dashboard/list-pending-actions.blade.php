@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.posts.posts')}}
@endsection
@section('style')
    <style>

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
                    {{__('messages.posts.posts')}}
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
                    <li class="breadcrumb-item text-muted">{{__('messages.posts.posts')}}</li>
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


                                            <li><a class="dropdown-item text-danger"
                                                   wire:click="deleteSelected" wire:key="{{uniqid()}}">
                                                    {{__('messages.delete')}}
                                                    <i class="bi bi-trash text-danger"></i>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                @endif
                            @endcan


                            <div class="dropdown">
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
                                                    {{ __('messages.posts.' . $column) }}
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
                    @can('posts_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a href="{{route('dashboard.posts.create_update_post')}}"
                               class="btn btn-primary">{{__('messages.posts.add_post')}}</a>
                            <!--end::Add product-->
                        </div>
                    @endcan
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    @if(count($selected) > 0)
                        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>{{ __('Selection Summary:') }}</strong>
                                {{ count($selected) }} {{ __('posts selected') }}

                                @if($sortOnlySelected)
                                    <span class="badge bg-warning ms-2">
                    <i class="bi bi-filter-circle"></i> {{ __('Sorting selected posts only') }}
                </span>
                                @else
                                    <span class="badge bg-secondary ms-2">
                    <i class="bi bi-filter"></i> {{ __('Sorting all posts') }}
                </span>
                                @endif
                            </div>
                            <div>
                                @if(count($selected) > 0)
                                    <button wire:click="toggleSortMode" class="btn btn-sm btn-outline-primary me-2"
                                            data-bs-toggle="tooltip"
                                            data-bs-title="{{ $sortOnlySelected ? 'Sort all posts' : 'Sort only selected posts' }}">
                                        <i class="bi {{ $sortOnlySelected ? 'bi-filter' : 'bi-filter-circle' }}"></i>
                                        {{ $sortOnlySelected ? 'Sort All' : 'Sort Selected' }}
                                    </button>
                                @endif
                                <button wire:click="clearSelection" class="btn btn-sm btn-outline-secondary">
                                    {{ __('Clear Selection') }}
                                </button>
                            </div>
                        </div>
                    @endif
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            @can('posts_delete')
                                <th><input type="checkbox" wire:model.live="selectAll"></th>
                            @endcan

                            @if(isset($columnVisibility['post_id']) && $columnVisibility['post_id'])
                                <th class="text-start min-w-70px th-btn {{ $sortField === 'id' ? 'sort-active' : '' }}"
                                    wire:click="sortBy('id')"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $sortOnlySelected ? 'Sort selected posts by ID' : 'Sort all posts by ID' }}">
                                    <div class="d-flex align-items-center gap-1">
                                        {{__('messages.posts.post_id')}}
                                        <i class="bi {{ $this->getSortIcon('id') }} sort-icon"
                                           style="font-size: 0.8rem;"></i>

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
                        @foreach($this->posts as $key =>  $post)
                            <tr>
                                @can('posts_delete')
                                    <td class="text-start " wire:key="{{$post->id}}_" id="{{$post->id}}_">
                                        <input type="checkbox" id="{{$post->id}}" wire:model.live="selected"
                                               value="{{ $post->id }}">
                                    </td>
                                @endcan

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

                                @if(isset($columnVisibility['tags']) && $columnVisibility['tags'])
                                    @php
                                        $tags = $post?->tags;
                                        $mainTag = $post->tag;
                                    @endphp

                                    <td class="text-start ">
                                        @if($tags && $tags->isNotEmpty())
                                            @foreach($tags as $tag)
                                                @php
                                                    $isMain = $mainTag && $tag->id === $mainTag->id;

                                                    $bgColor = $isMain ? '#17a2b8' : '#6c757d';
                                                    $textColor = '#fff';
                                                    $tagName = $tag?->relationable->tag_name ?? __('messages.not_available');
                                                @endphp

                                                <span class="badge me-1 mb-1"
                                                      style="background-color: {{ $bgColor }}; color: {{ $textColor }};
                             font-size: 0.85em; padding: 0.35em 0.65em;">
                    {{ $tagName }}

                </span>
                                            @endforeach
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

                                @if(isset($columnVisibility['publish_time']) && $columnVisibility['publish_time'])
                                    <td class="text-start ">
                                        {{Carbon::parse($post->publish_date)->format('H:i Y-m-d') ?? __('messages.not_available')}}
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
                                                    <li><a class="dropdown-item text-primary" target="_blank"
                                                           href="{{frontend_route('show_post',['id' => $post?->id, 'slug' => $post?->slug])}}">
                                                            {{__('messages.preview')}}
                                                            <i class="bi bi-eye text-primary"></i>
                                                        </a>
                                                    </li>
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
                                                    @can('posts_delete')
                                                        <li>
                                                            <button
                                                                class="dropdown-item text-danger border-0 bg-transparent text-start w-100"
                                                                wire:click="delete({{$post->id}})"
                                                                type="button">
                                                                {{__('messages.delete')}}
                                                                <i class="bi bi-trash text-danger ms-1"></i>
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

    <!--Change Status Modal -->
    <div class="modal fade" id="postChangeStatusPublish" data-bs-backdrop="static" data-bs-keyboard="false"
         tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="staticBackdropLabel">{{__('messages.posts.change_status')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.posts.confirm_Publish_status')}} </h4>
                    <h6>{{__('messages.posts.change_status_message')}} {{ $Post->title ?? __('messages.not_available')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="confirmChangeStatusPublish" data-bs-dismiss="modal"
                                class="btn btn-success">{{__('messages.confirm')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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


</div>
@section('script')
    <script>
        // Fix for aria-hidden focus issue
        document.addEventListener('DOMContentLoaded', function () {
            // Handle modal show events
            const modals = ['postDelete', 'postDeleteSelected', 'postChangeStatus', 'bulkpostChangeStatus'];

            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('show.bs.modal', function () {
                        // Remove aria-hidden when modal shows
                        this.removeAttribute('aria-hidden');
                        this.style.display = 'block';
                    });

                    modal.addEventListener('hidden.bs.modal', function () {
                        // Add aria-hidden when modal hides
                        this.setAttribute('aria-hidden', 'true');
                        this.style.display = 'none';
                    });
                }
            });
        });

        // Your existing event listeners
        window.addEventListener('show_delete_selected_modal', event => {
            $('#postDeleteSelected').modal('show');
        });

        window.addEventListener('hide_delete_selected_modal', event => {
            $('#postDeleteSelected').modal('hide');
        });

        window.addEventListener('show_delete_modal', event => {
            $('#postDelete').modal('show');
        });

        window.addEventListener('hide_delete_modal', event => {
            $('#postDelete').modal('hide');
        });

        window.addEventListener('hide_bulk_post_change_status_modal', event => {
            $('#bulkpostChangeStatus').modal('hide');
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_delete_modal', function () {
                Swal.fire({
                    title: '{{__('messages.alert_message.success')}}',
                    text: '{{__('messages.alert_message.post_deleted')}}',
                    icon: 'success',
                    confirmButtonText: '{{__('messages.alert_message.done')}}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
            });

            document.body.addEventListener('no-data', function () {
                Swal.fire({
                    title: '{{__('messages.alert_message.no_data')}}',
                    customClass: {
                        confirmButton: 'btn btn-warning'
                    }
                });
            });
        });
    </script>
@endsection
