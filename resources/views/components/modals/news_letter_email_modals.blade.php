<!--Create/Edit Modal -->
<div class="modal fade" id="news_letter_emailForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.news_letter_emails.edit_news_letter_email') : __('messages.news_letter_emails.add_news_letter_email')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'update' : 'create'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="email">{{__('messages.news_letter_emails.email')}}</label>
                            <input wire:model="state.email" type="text"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   placeholder="{{__('messages.news_letter_emails.email')}}">
                            @error('email')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button type="submit" class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{__('messages.confirm')}}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="news_letter_emailDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.news_letter_emails.delete_news_letter_email')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.news_letter_emails.confirm_delete_news_letter_email')}}</h4>
                <h6>{{__('messages.news_letter_emails.delete_news_letter_email_message')}}: {{$NewsLetterEmails_->email ?? __('messages.not_available')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>
