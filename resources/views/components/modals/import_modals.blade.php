<!--Create/Edit Modal -->
<div class="modal fade" id="importForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{__('messages.import.import')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="importExcel">
                    <div class="row mb-5">
                        <label for="importFile" class="mt-3">{{ __('messages.posts.file') }}</label>
                        <div class="d-flex align-items-center">
                            <input class="form-control" type="file" wire:model="importFile"
                                    id="importFile">
                        </div>
                        @error('importFile')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr>
                    <div class="row mt-2 mb-5">
                        <h3 for="importFile" class="mt-3 text-center text-primary"><a wire:click.prevent="downloadSamples"><u>{{ __('messages.download_samples') }}</u></a></h3>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('messages.import.import')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

