@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.quotes.quotes') }}
@endsection
@section('style')
    <style>
        .th-btn {
            cursor: pointer !important;
        }

        .author-select+.select2 .select2-selection--single {
            height: auto;
            min-height: 42px;
            padding: 5px 15px;
            background-color: #f5f8fa;
            border: 1px solid #e4e6ef;
            border-radius: 0.475rem;
        }

        .author-select+.select2 .select2-selection__rendered {
            display: flex;
            align-items: center;
            padding-left: 30px;
            position: relative;
            color: #5e6278;
        }

        .author-select+.select2 .select2-selection__clear {
            position: absolute;
            left: 8px;
            color: #a1a5b7;
            font-size: 1.2em;
            margin-right: 5px;
        }

        .author-select+.select2 .select2-selection__clear:hover {
            color: #f1416c;
        }

        .author-select+.select2 .select2-selection__arrow {
            height: 100%;
            right: 8px;
        }

        /* Ensure search input is visible and interactive */
        .select2-container--open .select2-dropdown--below {
            z-index: 1061 !important;
            /* Above modal */
        }

        .select2-search--dropdown {
            padding: 8px;
            background: #fff;
        }

        .select2-search__field {
            width: 100% !important;
            padding: 6px 12px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }

        /* Fix for input not receiving focus */
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            outline: 0 !important;
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
                    {{ __('messages.quotes.quotes') }}
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
                    <li class="breadcrumb-item text-muted">{{ __('messages.quotes.quotes') }}</li>
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
                                       placeholder="{{ __('messages.quotes.search_quote') }}" />
                            </div>

                            @can('quotes_delete')
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
                    @can('quotes_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a wire:click="addNew" class="btn btn-primary">{{ __('messages.quotes.add_quote') }}</a>
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
                                @can('quotes_delete')
                                    <th><input type="checkbox" wire:model.live="selectAll"></th>
                                @endcan
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('id')">
                                    {{ __('messages.quotes.quote_id') }}
                                </th>
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('author_id')">
                                    {{ __('messages.quotes.quote_author') }}
                                </th>
                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('quote_from')">
                                    {{ __('messages.quotes.quote_from') }}
                                </th>

                                <th class="text-start min-w-70px th-btn" wire:click="sortBy('quote_text')">
                                    {{ __('messages.quotes.quote_text') }}
                                </th>
                                @canany(['quotes_edit', 'quotes_delete'])
                                    <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($this->quotes as $key => $quote)
                                <tr>
                                    <td class="text-start " wire:key="{{ $quote->id }}_"
                                        id="{{ $quote->id }}_">
                                        <input type="checkbox" id="{{ $quote->id }}" wire:model.live="selected"
                                            value="{{ $quote->id }}">
                                    </td>
                                    <td class="text-start ">
                                        {{ $quote?->id }}
                                    </td>
                                    <td class="text-start ">
                                        {{ $quote?->author?->name ?? __('messages.no_data') }}
                                    </td>
                                    <td class="text-start ">
                                        {{ $quote?->quote_from ?? __('messages.no_data') }}
                                    </td>

                                    <td class="text-start ">
                                        <textarea class="form-control form-control-solid overflow-hidden w-75" name="quote_text" rows="3" readonly>{{ $quote?->quote_text ?? __('messages.no_data') }}</textarea>
                                    </td>
                                    @canany(['quotes_edit', 'quotes_delete'])
                                        <td class="text-start ">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {{ __('messages.options') }}
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                    style="z-index: 10;">
                                                    @can('quotes_edit')
                                                        <li><a class="dropdown-item text-warning btn"
                                                                wire:click="edit({{ $quote }})">
                                                                <i class="bi bi-pencil text-warning"></i>
                                                                {{ __('messages.edit') }}
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('quotes_delete')
                                                        <li><a class="dropdown-item text-danger btn"
                                                                wire:click="quoteDelete({{ $quote }})">
                                                                <i class="bi bi-trash text-danger"></i>
                                                                {{ __('messages.delete') }}
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
                        {{ $this->quotes->links() }}
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

    
        @include('components.modals.quote_modals')
    

</div>
@section('script')
    <script>
        // Modal show/hide handlers
        window.addEventListener('show_delete_selected_modal', event => {
            $('#deleteSelected').modal('show');
        });

        window.addEventListener('hide_delete_selected_modal', event => {
            $('#deleteSelected').modal('hide');
        });

        window.addEventListener('show_quote_delete', event => {
            $('#quoteDelete').modal('show');
        });

        window.addEventListener('hide_quote_delete', event => {
            $('#quoteDelete').modal('hide');
        });

        // Quote form handling with proper Select2 initialization
        document.addEventListener('livewire:init', function() {
            Livewire.on('show_quote_form', (event) => {
                const select2Elements = [{
                    selector: '#author_id',
                    wireModel: 'state.author_id'
                }];

                function initializeSelect2(selector, selectedValue) {
                    // Destroy existing instance if it exists
                    if ($(selector).hasClass('select2-hidden-accessible')) {
                        $(selector).select2('destroy');
                    }

                    // Initialize new instance
                    $('#author_id').select2({
                        placeholder: "{{ __('messages.choose') }}",
                        allowClear: false,
                        width: '100%',
                        dropdownParent: $('#quoteForm'),
                        minimumInputLength: 0,
                        templateSelection: function(data) {
                            if (!data.id) {
                                return data.text;
                            }
                            return $('<span class="selected-author">' + data.text + '</span>');
                        },
                        language: {
                            noResults: function() {
                                return "{{ __('messages.no_authors') }}";
                            }
                        }
                    });

                    // Set the value if provided
                    if (selectedValue) {
                        $(selector).val(selectedValue).trigger('change');
                    }
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
                        initializeSelect2(element.selector, @this.get(element.wireModel));
                        setupChangeEvent(element.selector, element.wireModel);

                        // For edit mode - ensure the option exists
                        if (@this.get('showEdit') && @this.get(element.wireModel)) {
                            const authorId = @this.get(element.wireModel);
                            const authorName = @this.get('state.author_name');

                            if (authorId && authorName && !$(
                                    `#author_id option[value="${authorId}"]`).length) {
                                $('#author_id').append(new Option(authorName, authorId,
                                    true, true));
                                $('#author_id').val(authorId).trigger('change');
                            }
                        }
                    }, 100);
                });

                // Reinitialize Select2 after Livewire updates
                Livewire.hook('message.processed', (message, component) => {
                    select2Elements.forEach(function(element) {
                        initializeSelect2(element.selector, component.serverMemo.data.state
                            .author_id);
                    });
                });
            });

            // Handle author updates specifically
            Livewire.on('authorUpdated', (event) => {
                const {
                    id,
                    name
                } = event;

                if (id && name) {
                    // Ensure the option exists
                    if (!$('#author_id option[value="' + id + '"]').length) {
                        $('#author_id').append(new Option(name, id, true, true));
                    }
                    // Set the value
                    $('#author_id').val(id).trigger('change');
                }
            });
        });

        // Modal show/hide handlers for quote form
        window.addEventListener('show_quote_form', event => {
            $('#quoteForm').modal('show');
        });

        window.addEventListener('hide_quote_form', event => {
            if ($('#author_id').hasClass('select2-hidden-accessible')) {
                $('#author_id').select2('destroy');
            }
            $('#quoteForm').modal('hide');
        });

        // Success notifications
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('hide_quote_form', function() {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.quote_saved') }}',
                    icon: 'success',
                    confirmButtonText: '{{ __('messages.alert_message.done') }}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
            });

            document.body.addEventListener('hide_quote_delete', function() {
                Swal.fire({
                    title: '{{ __('messages.alert_message.success') }}',
                    text: '{{ __('messages.alert_message.quote_deleted') }}',
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
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('livewire:init', function () {
                const select2Elements = [
                    {selector: '#author_id', wireModel: 'state.author_id'},
                ];

                document.addEventListener('livewire:init', function() {
                    Livewire.on('show_quote_form', (event) => {
                        const select2Elements = [{
                            selector: '#author_id',
                            wireModel: 'state.author_id'
                        }];

                        function initializeSelect2(selector, selectedValue) {
                            // Destroy existing instance if it exists
                            if ($(selector).hasClass('select2-hidden-accessible')) {
                                $(selector).select2('destroy');
                            }

                            // Initialize new instance with search
                            $(selector).select2({
                                placeholder: "{{ __('messages.choose') }}",
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#quoteForm'),
                                minimumInputLength: 1, // Start searching after 1 character
                                language: {
                                    noResults: function() {
                                        return "{{ __('messages.no_authors') }}";
                                    },
                                    searching: function() {
                                        return "{{ __('messages.searching') }}...";
                                    }
                                },
                                escapeMarkup: function (markup) {
                                    return markup;
                                },
                                templateResult: function(data) {
                                    if (!data.id) {
                                        return data.text;
                                    }
                                    return $('<span class="author-option">' + data.text + '</span>');
                                },
                                templateSelection: function(data) {
                                    if (!data.id) {
                                        return data.text;
                                    }
                                    return $('<span class="selected-author">' + data.text + '</span>');
                                }
                            });

                            // Set the value if provided
                            if (selectedValue) {
                                $(selector).val(selectedValue).trigger('change');
                            }
                        }

                        function setupChangeEvent(selector, wireModel) {
                            $(selector).on('change', function(e) {
                                var data = $(this).val();
                            @this.set(wireModel, data);
                            });
                        }

                        // Initialize Select2
                        select2Elements.forEach(function(element) {
                            setTimeout(function() {
                                initializeSelect2(element.selector, @this.get(element.wireModel));
                                setupChangeEvent(element.selector, element.wireModel);
                            }, 100);
                        });
// test
                        // Reinitialize Select2 after Livewire updates
                        Livewire.hook('message.processed', (message, component) => {
                            select2Elements.forEach(function(element) {
                                const currentValue = component.get(element.wireModel);
                                // Only reinitialize if the value changed
                                if ($(element.selector).val() !== currentValue) {
                                    $(element.selector).val(currentValue).trigger('change');
                                }
                            });
                        });
                    });

                    // Handle modal close to reset Select2
                    $('#quoteForm').on('hidden.bs.modal', function () {
                        $('#author_id').select2('destroy');
                    });
                });

                function setupChangeEvent(selector, wireModel) {
                    $(selector).on('change', function (e) {
                        var data = $(this).val();
                    @this.set(wireModel, data);
                    });
                }

                // Initialize Select2 for all elements
                select2Elements.forEach(function (element) {
                    setTimeout(function () {
                        initializeSelect2(element.selector, @this.get(element.wireModel));
                        setupChangeEvent(element.selector, element.wireModel);
                    }, 100);
                });

                // Reinitialize Select2 after Livewire updates
                Livewire.hook('message.processed', (message, component) => {
                    select2Elements.forEach(function (element) {
                        initializeSelect2(element.selector, @this.get(element.wireModel));
                    });
                });

                // Additional: Handle modal show event to ensure proper initialization
                $('#quoteForm').on('shown.bs.modal', function () {
                    setTimeout(function () {
                        select2Elements.forEach(function (element) {
                            initializeSelect2(element.selector, @this.get(element.wireModel));
                        });
                    }, 300);
                });
            });
        });
    </script>

@endsection
