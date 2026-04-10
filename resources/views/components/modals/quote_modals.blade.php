<!--Create/Edit Modal -->
<div class="modal fade" id="quoteForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{ $showEdit ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title text-white" id="staticBackdropLabel">
                    {{ $showEdit ? __('messages.quotes.edit_quote') : __('messages.quotes.add_quote') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{ $showEdit ? 'updateQuote' : 'createQuote' }}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="author_id">{{ __('messages.posts.author') }}</label>
                            <div class="d-flex align-items-center" wire:ignore>
                                <select class="form-select"
                                        wire:model.live="state.author_id"
                                        id="author_id"
                                        data-control="select2"
                                        data-placeholder="{{ __('messages.choose') }}">
                                    <option value="">{{ __('messages.choose') }}</option>
                                    @foreach($this->authors as $author)
                                        <option value="{{ $author['id'] }}">{{ $author['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('state.author_id')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="quote_from">{{ __('messages.quotes.quote_from') }}</label>
                            <input wire:model.live="state.quote_from" type="text"
                                   class="form-control @error('state.quote_from') is-invalid @enderror" id="quote_from"
                                   placeholder="{{ __('messages.quotes.quote_from') }}">
                            @error('state.quote_from')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="quote_text">{{ __('messages.quotes.quote_text') }}</label>
                            <textarea wire:model.live="state.quote_text" rows="4" class="form-control @error('state.quote_text') is-invalid @enderror"
                                      id="quote_text" placeholder="{{ __('messages.quotes.quote_text') }}"></textarea>
                            @error('state.quote_text')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit"
                                class="btn {{ $showEdit ? 'btn-warning' : 'btn-primary' }}">{{ __('messages.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Delete Modal -->
<div class="modal fade" id="quoteDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.quotes.delete_quote') }}
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.quotes.confirm_delete_quote') }}</h4>

                <h6>{{ $Quotes_->quote_text ?? __('messages.no_data') }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="quoteDeleteConfirm"
                        class="btn btn-danger">{{ __('messages.confirm') }}</button>
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.quotes.delete_quotes') }}
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.quotes.confirm_delete_quotes') }}</h4>
                <h6>{{ __('messages.quotes.delete_quotes_message') }}</h6>
                <div class="form-group col-md-12 mb-3">
                    <input wire:model="delete_text" class="form-control" id="category_description">
                </div>
                <!-- Error Message -->
                @if (session()->has('error'))
                    <div style="color: red; padding: 10px;">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="confirmDeleteSelected"
                        class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
