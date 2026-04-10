@php use App\Models\UserLog; use Illuminate\Support\Carbon; @endphp

    <!-- Export With Sort Modal -->
<div class="modal fade" id="ExportWithSort"
     data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="exportWithSortLabel" aria-hidden="true"
     wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exportWithSortLabel">
                    {{ __('messages.user_logs.export_with_edit_details') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="row g-3">

                    <!-- Language -->
                    <div class="col-12">
                        <label for="export_language" class="form-label fw-semibold mb-1">
                            {{ __('messages.user_logs.export_language') }}
                        </label>
                        <select id="export_language"
                                wire:model.live="export_language"
                                class="form-select form-control-solid">
                            <option value="ar">عربي</option>
                            <option value="en">English</option>
                        </select>
                        <div class="form-text text-muted">
                            {{ __('messages.user_logs.export_language') }} — اختر لغة ملف التصدير.
                        </div>
                    </div>

                    <!-- Table / Model -->
                    <div class="col-12">
                        <label for="export_list" class="form-label fw-semibold mb-1">
                            {{ __('messages.user_logs.export_table') }}
                        </label>
                        <select id="export_list"
                                wire:model.live="list"
                                class="form-select form-control-solid">
                            @foreach(\App\Enums\LoggableModelsEnum::cases() as $case)
                                @php
                                    $count = UserLog::where('actionable_type', $case->value)->count();
                                @endphp
                                <option value="{{ $case->value }}">
                                    {{ $case->label() }}{{ $count > 0 ? ' ('.$count.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">
                            اختر الجدول أو الكيان المراد تصدير سجلاته.
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" wire:click="export" class="btn btn-primary">
                    {{ __('messages.confirm') }}
                </button>
            </div>

        </div>
    </div>
</div>
