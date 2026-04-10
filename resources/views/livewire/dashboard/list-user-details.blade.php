@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.user_details.user_details')}}
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
            font-size: 15px;
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

        .tooltip-content th, .tooltip-content td {
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
                    {{__('messages.user_details.user_details')}}
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
                    <li class="breadcrumb-item text-muted">{{__('messages.user_details.user_details')}}</li>
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
                                       placeholder="{{__('messages.user_details.search')}}"/>
                            </div>


                            @can('events_delete')
                                @if(!empty($selected))
                                    <button wire:click="deleteSelected" class="btn btn-danger"
                                            wire:key="{{uniqid()}}">
                                        {{__('messages.bulk_deleted')}} {{count($selected)}}
                                    </button>
                                @endif
                            @endcan
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    @can('user_details_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew"
                               class="btn btn-primary">{{__('messages.user_details.add_user_detail')}}</a>
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

                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('id')">{{__('messages.user_details.id')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.user_details.image') }}</th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('first_name')">{{__('messages.user_details.first_name')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.user_details.last_name') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.user_details.email') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.user_details.gender') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.user_details.birthday') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.user_details.country') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                            >{{ __('messages.user_details.description') }}
                            </th>
                            @canany(['user_details_edit','user_details_delete'])
                                <th class="text-start min-w-70px">{{__('messages.actions')}}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->userDetails as $key => $item)
                            <tr>

                                <td class="text-start ">
                                    {{$item->id}}
                                </td>
                                <td class="text-start ">
                                    <img width="50px" src="{{$item?->user?->avatar}}" alt="image"/>
                                </td>
                                <td class="text-start ">
                                    {{$item->user?->first_name ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start ">
                                    {{$item->user?->last_name ?? __('messages.no_data')}}

                                </td>
                                <td class="text-start ">
                                    {{$item->user?->email ?? __('messages.no_data')}}

                                </td>
                                <td class="text-start ">
                                    {{$item->gender ?? __('messages.no_data')}}

                                </td>

                                <td class="text-start ">
                                    {{$item->birthday ?? __('messages.no_data')}}

                                </td>
                                <td class="text-start ">
                                    {{$item->country ?? __('messages.no_data')}}

                                </td>
                                <td class="text-start ">
                                    {{$item->description ?? __('messages.no_data')}}

                                </td>
                                @canany(['user_details_edit','user_details_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{__('messages.options')}}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                @can('user_details_edit')
                                                    <li><a class="dropdown-item text-warning btn"
                                                           wire:click="edit({{$item}})">
                                                            <i class="bi bi-pencil text-warning"></i>
                                                            {{__('messages.edit')}}

                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('user_details_edit')
                                                    <li><a class="dropdown-item text-warning btn"
                                                           wire:click="editActive({{$item}})">
                                                            <i class="bi bi-pencil text-warning"></i>
                                                            {{__('messages.user_details.edit_pending')}}

                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('user_details_delete')
                                                    <li><a class="dropdown-item text-danger btn"
                                                           wire:click="delete({{$item}})">
                                                            <i class="bi bi-trash text-danger"></i>
                                                            {{__('messages.delete')}}

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
                        {{$this->userDetails->links()}}
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
    
        @include('components.modals.user_details_modals')
    

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
            $('#addForm').modal('show');
        })
        window.addEventListener('hide_form', event => {
            $('#addForm').modal('hide');
        })
        window.addEventListener('show_user_edit_active', event => {
            $('#usereditactive').modal('show');
        })
        window.addEventListener('hide_user_edit_active', event => {
            $('#usereditactive').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_form', function () {
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.user_details_saved')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );

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
                Swal.fire(
                    {
                        title: '{{__('messages.alert_message.success')}}',
                        text: '{{__('messages.alert_message.user_details_deleted')}}',
                        icon: 'success',
                        confirmButtonText: '{{__('messages.alert_message.done')}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );

            });
        });


        document.addEventListener('livewire:init', function () {
            Livewire.on('show_form', (event) => {
                const select2Elements = [
                    {selector: '#category_id', wireModel: 'state.category_id'},
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
                    $(selector).on('change', function (e) {
                        var data = $(this).val();
                    @this.set(wireModel, data)
                        ;
                    });
                }

                // Initialize Select2 for all elements
                select2Elements.forEach(function (element) {
                    setTimeout(function () {
                        initializeSelect2(element.selector, @this.get(element.wireModel)); // Pass the initial value to select2
                        setupChangeEvent(element.selector, element.wireModel);
                    }, 100)
                });

                // Reinitialize Select2 after Livewire updates
                Livewire.hook('message.processed', (message, component) => {
                    select2Elements.forEach(function (element) {
                        initializeSelect2(element.selector, @this.get(element.wireModel)); // Update with live data
                    });
                });
            });
        });

        document.addEventListener('livewire:init', function () {
            var input = document.querySelector('#tags');
            var tagify = new Tagify(input, {
                whitelist: @json($this->tags), // Pass the tags array to Tagify
                dropdown: {
                    maxItems: 5,           // Show 20 suggestions at most
                    classname: "tags-look", // Custom dropdown class
                    enabled: 0,             // Show suggestions immediately
                    closeOnSelect: false    // Keep the dropdown open after selecting a suggestion
                }
            });

            tagify.addTags(@json($state['tags'] ?? []));

            tagify.on('add', function (e) {
            @this.set('state.tags', tagify.value.map(tag => tag.value).join(','))
                ;
            });

            tagify.on('remove', function (e) {
            @this.set('state.tags', tagify.value.map(tag => tag.value).join(','))
                ;
            });
        });

        document.addEventListener('livewire:init', function () {
            var input = document.querySelector('#interests');
            var tagify = new Tagify(input, {
                whitelist: @json($this->interests), // Pass the tags array to Tagify
                dropdown: {
                    maxItems: 5,           // Show 20 suggestions at most
                    classname: "tags-look", // Custom dropdown class
                    enabled: 0,             // Show suggestions immediately
                    closeOnSelect: false    // Keep the dropdown open after selecting a suggestion
                }
            });

            tagify.addTags(@json($state['interests'] ?? []));

            tagify.on('add', function (e) {
            @this.set('state.interests', tagify.value.map(tag => tag.value).join(','))
                ;
            });

            tagify.on('remove', function (e) {
            @this.set('state.interests', tagify.value.map(tag => tag.value).join(','))
                ;
            });
        });
        document.addEventListener('livewire:init', function () {
            var input = document.querySelector('#languages');
            var tagify = new Tagify(input, {
                whitelist: @json($this->languages), // Pass the tags array to Tagify
                dropdown: {
                    maxItems: 5,           // Show 20 suggestions at most
                    classname: "tags-look", // Custom dropdown class
                    enabled: 0,             // Show suggestions immediately
                    closeOnSelect: false    // Keep the dropdown open after selecting a suggestion
                }
            });

            tagify.addTags(@json($state['languages'] ?? []));

            tagify.on('add', function (e) {
            @this.set('state.languages', tagify.value.map(tag => tag.value).join(','))
                ;
            });

            tagify.on('remove', function (e) {
            @this.set('state.languages', tagify.value.map(tag => tag.value).join(','))
                ;
            });
        });
    </script>
@endsection

