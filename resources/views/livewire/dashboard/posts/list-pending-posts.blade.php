
@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.posts.posts')}}
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
                    {{__('messages.sidebar.pending_posts')}}
                </h1>
                <!--end::Title-->
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
                <!--begin::Card body-->
                <div class="card-body pt-0 table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                        <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('order_number')">
                                {{__('messages.order')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('id')">
                                {{__('messages.post_number')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('title')">
                                {{__('messages.title')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('category_id')">
                                {{__('messages.category')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('views')">
                                {{__('messages.views')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('user_id')">
                                {{__('messages.admin')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('publish_date')">
                                {{__('messages.publish_time')}}
                            </th>
                            <th class="text-start min-w-70px th-btn"
                                wire:click="sortBy('updates')">
                                {{__('messages.updates')}}
                            </th>
                            @canany(['posts_show','posts_edit','posts_delete'])
                                <th class="text-start min-w-70px">{{__('messages.actions')}}</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                        @foreach($this->posts as $key => $post)
                            <tr>
                                <td class="text-start ">
                                    {{$post->order_number ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$post->id ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$post->title ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$post?->category->category_title ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$post->views ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$post?->user->full_name ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{Carbon::parse($post->publish_date)->format('H:i Y-m-d') ?? __('messages.not_available')}}
                                </td>
                                <td class="text-start ">
                                    {{$post->updates ?? __('messages.not_available')}}
                                </td>
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
                                                @can('posts_publish')
                                                    <li>
                                                        <a class="dropdown-item text-success"
                                                           wire:click="publish({{$post}})"
                                                           data-bs-toggle="modal">
                                                            {{__('messages.publish')}}
                                                            <i class="bi bi-box-arrow-down text-white"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                @can('posts_edit')
                                                    <li>
                                                        <a class="dropdown-item text-warning"
                                                           href="{{route('dashboard.posts.create_update_post',$post->id)}}">
                                                            {{__('messages.edit')}}
                                                            <i class="bi bi-pencil text-warning"></i>
                                                        </a>
                                                    </li>
                                                @endcan
                                                <li><a class="dropdown-item text-primary" target="_blank"
                                                       href="{{route('main.show_post',[$post->id,$post->slug])}}">
                                                        {{__('messages.preview')}}
                                                        <i class="bi bi-eye text-primary"></i>
                                                    </a>
                                                </li>
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
                    <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.delete_post')}}</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.confirm_delete_post')}}</h4>
                    <h6>{{__('messages.delete_post_message', ['post_title' => $Post->title ?? __('messages.not_available')])}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="confirmDelete" class="btn btn-danger">{{__('messages.confirm')}}</button>
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
                <div class="modal-header btn-secondary">
                    <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.change_status')}}</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>{{__('messages.confirm_draft_status')}}</h4>
                    <h6>{{__('messages.change_status_message', ['post_title' => $Post->title ?? __('messages.not_available')])}}</h6>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button wire:click="confirmChangeStatus" data-bs-dismiss="modal" class="btn btn-light">{{__('messages.confirm')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script>

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
    </script>

@endsection
