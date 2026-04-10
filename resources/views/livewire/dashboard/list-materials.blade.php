@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.materials.materials') }}
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
                    {{ __('messages.materials.materials') }}
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.materials.materials') }}</li>
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
                    <div class="card-title w-75">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1 gap-2 w-100 flex-wrap">
                            <div class="input-group " style="flex-wrap: nowrap">
                                <div class="input-group-prepend">
                                    <i class="bi bi-search fs-3  input-group-text"></i>
                                </div>
                                <input type="text" wire:model.live="search"
                                       class="form-control form-control-solid w-250px"
                                       placeholder="{{ __('messages.materials.search') }}"/>
                            </div>

                               <select wire:model.live="perPage" class="form-select form-select-solid w-100px text-center">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>       <option value="100">100</option>
                                </select>
                            @can('materials_delete')
                                @if (!empty($selected))
                                        <button wire:click="deleteSelected" class="btn btn-danger" style="    text-wrap-mode: nowrap;"
                                            wire:key="{{ uniqid() }}">
                                            {{ __('messages.bulk_deleted') }} {{ count($selected) }}
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
                                                {{ __('messages.materials_columns.' . $column) }}
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
                    @can('materials_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">{{ __('messages.materials.add_material') }}</a>
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
                                @can('materials_delete')
                                    <th><input type="checkbox" wire:model.live="selectAll"></th>
                                @endcan
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                    {{ __('messages.materials.id') }}
                                </th>
                                    @if($columnVisibility['image'])
                                <th class="text-start min-w-80px">{{ __('messages.materials.image') }}
                                </th>
                                    @endif
                                    @if($columnVisibility['title'])
                                <th class="text-start min-w-70px">{{ __('messages.materials.title') }}
                                </th>
                                    @endif
                                    @if($columnVisibility['description'])
                                <th class="text-start min-w-70px">{{ __('messages.materials.description') }}
                                </th>
                                    @endif
                                    @if($columnVisibility['type'])
                                <th class="text-start min-w-70px">{{ __('messages.materials.type') }}
                                </th>
                                    @endif
                                    @if($columnVisibility['category'])
                                <th class="text-start min-w-70px">{{ __('messages.materials.category') }}
                                </th>
                                    @endif
                                    @if($columnVisibility['tags'])
                                        <th class="text-start min-w-70px">{{ __('messages.materials.tags') }}
                                        </th>
                                    @endif
                                    @if($columnVisibility['material_album_name'])
                                <th class="text-start min-w-70px">{{ __('messages.materials.album') }}
                                </th>
                                    @endif

                                @canany(['materials_edit', 'materials_delete'])
                                        @if($columnVisibility['actions'])
                                    <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                                        @endif
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($this->materials as $key => $item)
                                <tr>
                                    <td class="text-start " wire:key="{{ $item->id }}_"
                                        id="{{ $item->id }}_">
                                        <input type="checkbox" id="{{ $item->id }}" wire:model.live="selected"
                                            value="{{ $item->id }}">
                                    </td>
                                    <td class="text-start ">
                                        {{ $item->id }}
                                    </td>
                                    @if($columnVisibility['image'])
                                    <td class="text-start ">
                                        @if (isset($item?->files?->where('model_column', 'image')->first()?->file?->path))
                                            <div class="symbol symbol-50px">
                                                <img src="{{ file_url($item?->files?->where('model_column', 'image')->first()?->file?->path) }}"
                                                    alt="{{ __('messages.categories.image') }}" />
                                            </div>
                                        @else
                                            <div class="symbol symbol-50px">
                                                @php
                                                    $colors = ['primary', 'danger', 'info'];
                                                    $color = $colors[array_rand($colors)];
                                                @endphp
                                                <div
                                                    class="symbol-label fs-2 fw-semibold bg-{{ $color }} text-inverse-{{ $color }}">
                                                    @php
                                                        $names = explode(' ', $item?->relationable?->category_title);
                                                        $firstInitial = mb_substr($names[0], 0, 1);
                                                        $lastInitial = mb_substr(end($names), 0, 1);
                                                        $initials = $firstInitial . ' ' . $lastInitial;
                                                    @endphp
                                                    {{ $initials }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    @endif
                                    @if($columnVisibility['title'])
                                    <td class="text-start ">
                                        {{ $item?->title ?? __('messages.no_data') }}
                                    </td>
                                    @endif
                                    @if($columnVisibility['description'])
                                    <td class="text-start ">
                                        {{ $item?->description ?? __('messages.no_data') }}
                                    </td>
                                    @endif
                                    @if($columnVisibility['type'])
                                    <td class="text-start ">
                                        {{ $item->type ? \App\Enums\MaterialTypeEnum::from($item->type)->label() : __('messages.no_data') }}
                                    </td>
                                    @endif
                                    @if($columnVisibility['category'])
                                        <td class="text-start ">
                                            @foreach($item->categories as $category)
                                                @php
                                                    $isMain = $item->category && $category->id === $item->category->id;
                                                    $bgColor = $isMain ? $category->relationable->category_style : '#f0f0f0';
                                                    $textColor = $isMain ? '#fff' : '#000';
                                                @endphp
                                                <span class="badge me-1"
                                                      style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $category->relationable->category_title ?? __('messages.not_available') }}
                    </span>
                                            @endforeach
                                        </td>
                                    @endif
                                    @if($columnVisibility['tags'])
                                        <td class="text-start ">
                                            @foreach($item->tags as $tag)
                                                <span class="badge bg-light text-dark me-1">
                {{ $tag->relationable->tag_name ?? __('messages.not_available') }}
            </span>
                                            @endforeach
                                        </td>
                                    @endif
                                    @if($columnVisibility['material_album_name'])
                                    <td class="text-start ">
                                        {{ $item?->material_album?->name ?? __('messages.no_data') }}
                                    </td>
                                    @endif
                                    @canany(['materials_edit', 'materials_delete'])
                                        @if($columnVisibility['actions'])
                                        <td class="text-start ">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {{ __('messages.options') }}
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                    style="z-index: 10;">
                                                    @can('materials_edit')
                                                        <li><a class="dropdown-item text-warning"
                                                                wire:click="edit({{ $item }})">
                                                                {{ __('messages.edit') }}
                                                                <i class="bi bi-pencil text-warning"></i>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('materials_delete')
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
                                        @endif
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $this->materials->links() }}
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
    
        @include('components.modals.material_modals')
    

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
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('hide_form', function() {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.material_saved') }}',
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
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('hide_delete', function() {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.album_deleted') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });

            });
        });

        document.addEventListener('livewire:init', function() {
            Livewire.on('show_form', (event) => {
                const select2Elements = [{
                        selector: '#category_id',
                        wireModel: 'state.category_id'
                    },
                    {
                        selector: '#presenter_id',
                        wireModel: 'state.presenter_id'
                    },
                    {
                        selector: '#guest_id',
                        wireModel: 'state.guest_id'
                    },
                ];

                function initializeSelect2(selector, selectedValue) {
                    console.log(selectedValue)
                    $(selector).select2({
                        placeholder: $(selector).data('placeholder'),
                        // If there's a selected value, trigger select2 to select it
                        val: selectedValue || '',
                    });
                }

                function setupChangeEvent(selector, wireModel) {
                    $(selector).on('change', function(e) {
                        var data = $(this).val();
                        @this.set(wireModel, data);
                    });
                }

                // Initialize Select2 for all elements
                select2Elements.forEach(function(element) {
                    setTimeout(function() {
                        initializeSelect2(element.selector, @this.get(element
                            .wireModel)); // Pass the initial value to select2
                        setupChangeEvent(element.selector, element.wireModel);
                    }, 100)
                });

                // Reinitialize Select2 after Livewire updates
                Livewire.hook('message.processed', (message, component) => {
                    select2Elements.forEach(function(element) {
                        initializeSelect2(element.selector, @this.get(element
                            .wireModel)); // Update with live data
                    });
                });
            });
        });



        document.addEventListener('livewire:init', function() {
            var input = document.querySelector('#tags');
            var tagify = new Tagify(input, {
                whitelist: @json($this->tags), // Pass the tags array to Tagify
                dropdown: {
                    maxItems: 5, // Show 20 suggestions at most
                    classname: "tags-look", // Custom dropdown class
                    enabled: 0, // Show suggestions immediately
                    closeOnSelect: false // Keep the dropdown open after selecting a suggestion
                }
            });

            tagify.addTags(@json($state['tags'] ?? []));

            tagify.on('add', function(e) {
                @this.set('state.tags', tagify.value.map(tag => tag.value).join(','));
            });

            tagify.on('remove', function(e) {
                @this.set('state.tags', tagify.value.map(tag => tag.value).join(','));
            });
        });

        document.addEventListener('livewire:init', function() {
            Livewire.on('updateCategorySelect', ({
                categories
            }) => {
                const $select = $('#category_id');

                // امسح الخيارات القديمة
                $select.empty();

                // أضف خيارات جديدة
                categories.forEach(category => {
                    const newOption = new Option(category.text, category.id, false, false);
                    $select.append(newOption);
                });

                // أعِد تهيئة select2
                $select.trigger('change');
            });

        });
    </script>
@endsection
