@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ __('messages.special_pages.special_pages') }}
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
                    {{ __('messages.special_pages.special_pages') }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}" class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{ __('messages.special_pages.special_pages') }}</li>
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
                                       placeholder="{{ __('messages.special_pages.search_page') }}"/>
                            </div>

                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    @can('special_pages_create')
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <!--begin::Add product-->
                            <a href="{{route('dashboard.special_pages.create_update_special_page')}}"
                               class="btn btn-primary">{{ __('messages.special_pages.add_page') }}</a>
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
                                wire:click="sortBy('id')">
                                {{ __('messages.special_pages.page_id') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('page_title')">
                                {{ __('messages.special_pages.title') }}
                            </th>
                            <th class="text-start min-w-70px">
                                {{ __('messages.special_pages.url') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('user_id')">
                                {{ __('messages.special_pages.admin') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('created_at')">
                                {{ __('messages.special_pages.publish_time') }}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('publish_status')">
                                {{ __('messages.special_pages.status') }}
                            </th>
                            @canany(['special_pages_show','special_pages_edit','special_pages_delete'])
                                <th class="text-start min-w-70px">{{ __('messages.actions') }}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->special_pages as $key => $special_page)
                            <tr>
                                <td class="text-start ">
                                    {{$special_page->id ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start ">
                                    {{$special_page->page_title ?? __('messages.no_data')}}
                                </td>

                                <td class="text-start " style="direction: ltr!important; white-space: nowrap;">
                                    <div class="d-flex align-items-center gap-2">
        <span class="text-truncate" style="max-width: 200px;">
            {{ $url = frontend_route('special_page', ['id' => $special_page->id]) }}
        </span>
                                        <button type="button" class="btn btn-sm btn-secondary bg-grey-400"
                                                onclick="copyToClipboard('{{ $url }}')">
                                            {{ __('messages.copy_link') }}
                                        </button>
                                    </div>
                                </td>

                                <td class="text-start ">
                                    {{$special_page?->user->full_name ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start ">
                                    {{Carbon::parse($special_page->created_at)->format('H:i Y-m-d') ?? __('messages.no_data')}}
                                </td>
                                <td class="text-start ">
                                    {{$special_page->publish_status ? \App\Enums\PublishEnum::from($special_page->publish_status)->label() : __('messages.no_data')}}
                                </td>
                                @canany(['special_pages_show','special_pages_edit','special_pages_delete'])
                                    <td class="text-start ">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle p-2" type="button"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ __('messages.options') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1"
                                                style="z-index: 10;">
                                                <li><a class="dropdown-item text-primary" target="_blank"
                                                       href="{{frontend_route('special_page',['id' => $special_page->id])}}">
                                                        {{ __('messages.special_pages.preview') }}
                                                        <i class="bi bi-eye text-primary"></i>
                                                    </a>
                                                </li>
                                                @can('special_pages_edit')
                                                    <li><a class="dropdown-item text-warning"
                                                           href="{{route('dashboard.special_pages.create_update_special_page',$special_page->id)}}">
                                                            {{ __('messages.edit') }}
                                                            <i class="bi bi-pencil text-warning"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('special_pages_delete')
                                                    <li><a class="dropdown-item text-danger"
                                                           wire:click="delete({{$special_page}})">
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
                        {{$this->special_pages->links()}}
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
    <div class="modal fade" id="special_pageDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.special_pages.delete_special_page') }}</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{ __('messages.special_pages.confirm_delete_special_page') }}</h4>
                    <h6>{{ __('messages.special_pages.delete_special_page_message') }}: {{$SpecialPage->page_title ?? __('messages.no_data')}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button wire:click="confirmDelete" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script>
        window.addEventListener('show_delete_modal', event => {
            $('#special_pageDelete').modal('show');
        })
        window.addEventListener('hide_delete_modal', event => {
            $('#special_pageDelete').modal('hide');
        })
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('hide_user_delete', function () {
                Swal.fire(
                    {
                        title: '{{ __('messages.alert_message.success') }}',
                        text: '{{ __('messages.alert_message.special_page_deleted') }}',
                        icon: 'success',
                        confirmButtonText: '{{ __('messages.alert_message.done') }}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }
                );

            });
        });

        function copyToClipboard(text) {
            var tempInput = document.createElement("input");
            tempInput.value = text;
            document.body.appendChild(tempInput);

            tempInput.select();
            tempInput.setSelectionRange(0, 99999);

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text)
                        .then(() => {
                            showCopySuccess();
                        })
                        .catch(err => {
                            fallbackCopy(text);
                        });
                } else {
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
                document.execCommand("copy");
                showCopySuccess();
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: @json(__('messages.link_copying_failed')),
                    text: @json(__('messages.failed_copying_try')) + err,
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
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        }

        // Rest of your existing script...
        window.addEventListener('show_delete_modal', event => {
            $('#special_pageDelete').modal('show');
        });
    </script>
@endsection
