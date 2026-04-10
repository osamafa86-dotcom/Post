<div class="modal fade" id="UserLogsDetails" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    {{ __('messages.user_logs.edit_details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                @if(!empty($this->changes))
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                        <tr>
                                    <th>{{ __('messages.user_logs.field') }}</th>
                            <th>{{ __('messages.user_logs.before_edit') }}</th>
                            <th>{{ __('messages.user_logs.after_edit') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->changes as $change)
                            @php
                                $isDifferent = $change['before'] != $change['after'];
                            @endphp
                            <tr>
                                            <td><strong>{{ $change['label'] }}</strong></td>
                                <td class="{{ $isDifferent ? 'text-danger' : 'text-dark' }}">
                                    {{ is_array($change['before']) ? json_encode($change['before'], JSON_UNESCAPED_UNICODE) : $change['before'] }}
                                </td>
                                <td class="{{ $isDifferent ? 'text-success' : 'text-dark' }}">
                                    {{ is_array($change['after']) ? json_encode($change['after'], JSON_UNESCAPED_UNICODE) : $change['after'] }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                @else
                    <p class="text-muted text-center">
                        {{ __('messages.no_changes_found') }}
                    </p>
                @endif
            </div>

            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
