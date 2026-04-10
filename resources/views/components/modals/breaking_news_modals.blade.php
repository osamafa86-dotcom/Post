
<!--Create/Edit Modal -->
<div class="modal fade" id="breaking_newsForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __("messages.breaking_news.edit_urgent_news") : __("messages.breaking_news.add_urgent_news")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateBreakingNews' : 'createBreakingNews'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="title">{{ __('messages.breaking_news.news_title') }}</label>
                            <input wire:model="state.title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   placeholder="{{ __('messages.breaking_news.news_title_placeholder') }}">
                            @error('title')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="url">{{ __('messages.breaking_news.news_url') }}</label>
                            <input wire:model="state.url" type="text"
                                   class="form-control @error('url') is-invalid @enderror"
                                   id="url"
                                   placeholder="{{ __('messages.breaking_news.news_url_placeholder') }}">
                            @error('url')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
{{--                        <div class="form-group col-md-12 mb-3">--}}
{{--                            <label for="hide_date">{{ __('messages.breaking_news.news_hide_date') }}</label>--}}
{{--                            <input wire:model="state.hide_date"  type="datetime-local"--}}
{{--                                   class="form-control @error('hide_date') is-invalid @enderror"--}}
{{--                                   id="hide_date"--}}
{{--                                   placeholder="{{ __('messages.breaking_news.news_hide_date_placeholder') }}">--}}
{{--                            @error('hide_date')--}}
{{--                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{ __('messages.confirm') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="breaking_newsDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.breaking_news.delete_breaking_news') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.breaking_news.confirm_delete_breaking_news') }}</h4>
                <h6>{{ __('messages.breaking_news.delete_breaking_news_message', ['newsTitle' => $BreakingNews_->title ?? __('messages.no_data')]) }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="breaking_newsDeleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="deleteSelected" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{__('messages.breaking_news.delete_breaking_newss')}}   </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.breaking_news.confirm_delete_breaking_newss')}}</h4>
                <h6>{{__('messages.breaking_news.delete_breaking_newss_message')}}</h6>
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

