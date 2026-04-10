<div class="modal fade" id="UserLogsTypeDetails" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="staticBackdropLabel">
                    <i class="bi bi-geo-alt-fill me-2"></i> {{ __('messages.user_logs.location_details') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                @if(!empty($this->details))
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th class="fw-bold">{{ __('enums.field') }}</th>
                                <th class="fw-bold">{{ __('enums.value') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->details as $item)
                                <tr>
                                    <td class="text-muted" style="width: 40%">
                                        <i class="bi bi-tag me-1 text-primary"></i> {{ $item['label'] }}
                                    </td>
                                    <td>
                                        {{ $item['value'] }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">{{ __('messages.no_data') }}</p>
                @endif
            </div>

            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
