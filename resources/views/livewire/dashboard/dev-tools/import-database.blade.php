@section('title')
    {{config('system.site_name') . ' - '}}{{__('messages.import.import')}}
@endsection
@section('style')
    <style>
        .dev-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
            transition: box-shadow 0.2s ease;
        }
        .dev-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .dev-card .card-header {
            padding: 14px 20px !important;
            border: none;
            border-radius: 0 !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dev-card .card-header h3 { margin: 0; font-size: 15px; font-weight: 700; color: #fff; }
        .dev-card .card-body { padding: 20px; }

        .hdr-1 { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .hdr-2 { background: linear-gradient(135deg, #0891b2, #2563eb); }
        .hdr-3 { background: linear-gradient(135deg, #ea580c, #dc2626); }
        .hdr-4 { background: linear-gradient(135deg, #059669, #0891b2); }
        .hdr-5 { background: linear-gradient(135deg, #7c3aed, #db2777); }

        .dev-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: #f8fafc;
            width: 100%;
        }
        .dev-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
            background: #fff;
            outline: none;
        }

        .dev-label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; display: block; }

        .dev-btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 40px;
            transition: all 0.15s;
            cursor: pointer;
        }
        .dev-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,0.12); }
        .dev-btn:disabled { opacity: 0.6; transform: none; box-shadow: none; cursor: not-allowed; }

        .dev-btn-primary { background: #6366f1; color: #fff; }
        .dev-btn-primary:hover { background: #4f46e5; color: #fff; }
        .dev-btn-outline { background: #fff; color: #6366f1; border: 1.5px solid #6366f1; }
        .dev-btn-outline:hover { background: #eef2ff; color: #4f46e5; }
        .dev-btn-cyan { background: #0891b2; color: #fff; }
        .dev-btn-cyan:hover { background: #0e7490; color: #fff; }
        .dev-btn-red { background: #dc2626; color: #fff; }
        .dev-btn-red:hover { background: #b91c1c; color: #fff; }
        .dev-btn-green { background: #059669; color: #fff; }
        .dev-btn-green:hover { background: #047857; color: #fff; }
        .dev-btn-purple { background: #7c3aed; color: #fff; }
        .dev-btn-purple:hover { background: #6d28d9; color: #fff; }

        .conn-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .conn-badge-ok { background: #dcfce7; color: #166534; }
        .conn-badge-err { background: #fee2e2; color: #991b1b; }

        .clean-toggle { display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: #fefce8; border: 1px solid #fde68a; border-radius: 10px; }
        .clean-toggle.active { background: #fef2f2; border-color: #fca5a5; }

        .progress-wrap { background: linear-gradient(135deg, #f0f4ff, #faf5ff); border: 1.5px solid #e0e7ff; border-radius: 14px; overflow: hidden; }
        .progress-wrap .pw-header { padding: 14px 20px; border-bottom: 1px solid #e0e7ff; display: flex; align-items: center; justify-content: space-between; }
        .progress-wrap .pw-body { padding: 16px 20px; }

        .pbar { height: 10px; border-radius: 5px; background: #e5e7eb; overflow: hidden; }
        .pbar-fill { height: 100%; border-radius: 5px; background: linear-gradient(90deg, #6366f1, #a78bfa); transition: width 0.4s ease; }

        .step-row { display: flex; align-items: center; padding: 10px 12px; border-radius: 8px; margin-bottom: 6px; gap: 10px; }
        .step-row.done { background: #f0fdf4; }
        .step-row.active { background: #eff6ff; }
        .step-row.pending { background: #f9fafb; }

        .step-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
        .done .step-dot { background: #10b981; color: #fff; }
        .active .step-dot { background: #3b82f6; color: #fff; }
        .pending .step-dot { background: #d1d5db; color: #6b7280; }

        .step-label { flex: 1; font-size: 13px; font-weight: 500; color: #1f2937; }
        .step-stat { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 12px; }
        .done .step-stat { background: #dcfce7; color: #166534; }
        .active .step-stat { background: #dbeafe; color: #1e40af; }

        .result-box { border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 500; }
        .result-ok { background: #f0fdf4; border-inline-start: 3px solid #10b981; color: #166534; }
        .result-err { background: #fef2f2; border-inline-start: 3px solid #ef4444; color: #991b1b; }

        .info-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 8px; font-size: 12px; color: #0c4a6e; margin-bottom: 16px; }

        .preview-strip { background: #f1f5f9; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px; margin-top: 12px; }
        .preview-strip code { display: block; padding: 6px 10px; border-radius: 4px; font-size: 12px; margin-top: 4px; }
        .pv-old code { background: #fee2e2; color: #991b1b; }
        .pv-new code { background: #dcfce7; color: #166534; }

        @keyframes spin { to { transform: rotate(360deg); } }
        .anim-spin { animation: spin 1s linear infinite; }
    </style>
@endsection

<div>
    {{-- Page Header --}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{__('messages.import.import')}}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}" class="text-muted text-hover-primary">{{ __('messages.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.permissions')}}" class="text-muted text-hover-primary">{{ __('messages.permissions.permissions') }}</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{__('messages.import.import')}}</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            {{-- Result --}}
            @if($result)
                <div class="result-box {{ (str_contains($result, 'Error') || str_contains($result, 'خطأ') || str_contains($result, 'فشل')) ? 'result-err' : 'result-ok' }} mb-4"
                     x-data="{ show: true }" x-show="show" x-transition>
                    <span class="flex-grow-1">{!! $result !!}</span>
                    <button @click="show = false" style="background:none;border:none;cursor:pointer;">x</button>
                </div>
            @endif

            {{-- Progress --}}
            @if($importProgress['isImporting'])
                @php
                    $totalSteps = count($importProgress['steps']);
                    $completedSteps = collect($importProgress['steps'])->where('completed', true)->count();
                    $progress = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
                    if ($completedSteps < $totalSteps && $importProgress['currentStep'] < $totalSteps) {
                        $cs = $importProgress['steps'][$importProgress['currentStep']];
                        $sp = $cs['total'] > 0 ? ($cs['processed'] / $cs['total']) * 100 : 0;
                        $progress = min(100, round(($completedSteps / $totalSteps * 100) + ($sp / $totalSteps)));
                    }
                @endphp
                <div class="progress-wrap mb-4" wire:poll.500ms>
                    <div class="pw-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold fs-6">{{ __('messages.import.import_progress') }}</span>
                            <small class="text-muted">({{ $completedSteps }}/{{ $totalSteps }} {{ __('messages.import.steps_completed') }})</small>
                        </div>
                        <span class="fw-bold" style="color:#6366f1;font-size:18px;">{{ $progress }}%</span>
                    </div>
                    <div class="pw-body">
                        <div class="pbar mb-3">
                            <div class="pbar-fill" style="width:{{ $progress }}%"></div>
                        </div>
                        @foreach($importProgress['steps'] as $index => $step)
                            @php
                                $isCurrent = $index === $importProgress['currentStep'] && !$step['completed'];
                                $cls = $step['completed'] ? 'done' : ($isCurrent ? 'active' : 'pending');
                                $sprog = $step['total'] > 0 ? min(100, round(($step['processed'] / $step['total']) * 100)) : 0;
                            @endphp
                            <div class="step-row {{ $cls }}">
                                <div class="step-dot">
                                    @if($step['completed'])
                                        &#10003;
                                    @elseif($isCurrent)
                                        <span class="anim-spin" style="font-size:11px;display:inline-block;">&#9696;</span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span class="step-label">{{ $step['name'] }}</span>
                                @if($step['completed'] && $step['total'] > 0)
                                    <span class="step-stat">{{ $step['processed'] }}/{{ $step['total'] }}</span>
                                @elseif($isCurrent && $step['total'] > 0)
                                    <span class="step-stat">{{ $step['processed'] }}/{{ $step['total'] }} ({{ $sprog }}%)</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 1. Database Import --}}
            <div class="dev-card mb-4">
                <div class="card-header hdr-1">
                    <h3>{{ __('messages.import.database_import') }}</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="confirmImport">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg col-md-6">
                                <label class="dev-label">{{__('messages.import.source')}}</label>
                                <select class="dev-input" wire:model="source">
                                    <option value="laravel">Laravel</option>
                                    <option value="cilliumfm">CilliumFM</option>
                                    <option value="almadina">Almadina</option>
                                    <option value="qudsnen">QudsNEN</option>
                                    <option value="hodhod">Hodhod</option>
                                    <option value="mohager">Mohager</option>
                                    <option value="maktoob">Maktoob</option>
                                </select>
                            </div>
                            <div class="col-lg col-md-6">
                                <label class="dev-label">{{__('messages.import.host')}}</label>
                                <input wire:model="host" type="text" class="dev-input" placeholder="127.0.0.1">
                            </div>
                            <div class="col-lg col-md-6">
                                <label class="dev-label">{{__('messages.import.db')}}</label>
                                <input wire:model="db" type="text" class="dev-input" placeholder="database_name">
                            </div>
                            <div class="col-lg col-md-6">
                                <label class="dev-label">{{__('messages.import.username')}}</label>
                                <input wire:model="username" type="text" class="dev-input" placeholder="root">
                            </div>
                            <div class="col-lg col-md-6">
                                <label class="dev-label">{{__('messages.users.password')}}</label>
                                <input wire:model="password" type="password" class="dev-input" placeholder="********">
                            </div>
                            <div class="col-lg col-md-6">
                                <label class="dev-label">{{ __('messages.import.target_language') }}</label>
                                <select class="dev-input" wire:model="language">
                                    @foreach($languages as $lang => $title)
                                        <option value="{{$lang}}">{{ is_array($title) ? ($title['name'] ?? $lang) : $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Clean toggle --}}
                        <div class="mt-3 clean-toggle {{ $cleanBeforeImport ? 'active' : '' }}" x-data>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" wire:model.live="cleanBeforeImport" id="cleanToggle" style="cursor:pointer;">
                            </div>
                            <div>
                                <label for="cleanToggle" class="fw-semibold" style="font-size:13px;cursor:pointer;color:#92400e;">
                                    {{ $cleanBeforeImport ? '⚠' : '' }} {{ __('messages.import.clean_before_import') }}
                                </label>
                                @if($cleanBeforeImport)
                                    <div style="font-size:11px;color:#dc2626;margin-top:2px;">{{ __('messages.import.clean_warning') }}</div>
                                @else
                                    <div style="font-size:11px;color:#92400e;margin-top:2px;">{{ __('messages.import.admin_preserved') }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Connection status --}}
                        @if($connectionStatus)
                            <div class="mt-3">
                                @if($connectionStatus === 'success')
                                    <span class="conn-badge conn-badge-ok">{{ __('messages.import.connection_success') }}</span>
                                @else
                                    <span class="conn-badge conn-badge-err">{{ __('messages.import.connection_failed') }}</span>
                                @endif
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" wire:click="testConnection" class="dev-btn dev-btn-outline"
                                    wire:loading.attr="disabled" wire:target="testConnection">
                                <span wire:loading.remove wire:target="testConnection">{{ __('messages.import.test_connection') }}</span>
                                <span wire:loading wire:target="testConnection">{{ __('messages.import.testing_connection') }}</span>
                            </button>
                            <button type="submit" class="dev-btn {{ $cleanBeforeImport ? 'dev-btn-red' : 'dev-btn-primary' }}"
                                    wire:loading.attr="disabled" wire:target="confirmImport,executeImport">
                                <span wire:loading.remove wire:target="confirmImport,executeImport">{{__('messages.import.import')}}</span>
                                <span wire:loading wire:target="confirmImport,executeImport">{{ __('messages.import.importing') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 2. Update Body Content --}}
            <div class="dev-card mb-4">
                <div class="card-header hdr-2">
                    <h3>{{ __('messages.import.update_body_content') }}</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="update_body_part">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5 col-md-6">
                                <label class="dev-label">{{ __('messages.import.old_text_part') }}</label>
                                <input wire:model="fromPart" type="text" class="dev-input"
                                       placeholder="{{ __('messages.import.old_text_placeholder') }}">
                            </div>
                            <div class="col-lg-5 col-md-6">
                                <label class="dev-label">{{ __('messages.import.new_text_part') }}</label>
                                <input wire:model="toPart" type="text" class="dev-input"
                                       placeholder="{{ __('messages.import.new_text_placeholder') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <button type="submit" class="dev-btn dev-btn-cyan w-100"
                                        wire:loading.attr="disabled" wire:target="update_body_part">
                                    <span wire:loading.remove wire:target="update_body_part">{{ __('messages.import.update') }}</span>
                                    <span wire:loading wire:target="update_body_part">...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 3. Change Category --}}
            <div class="dev-card mb-4">
                <div class="card-header hdr-3">
                    <h3>{{ __('messages.import.change_category') }}</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="changeCategorySubmit">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5 col-md-6">
                                <label class="dev-label">{{ __('messages.import.old_category') }}</label>
                                <select class="dev-input" wire:model="changeCatFrom">
                                    <option value="">-- {{ __('messages.import.select_category') }} --</option>
                                    @foreach($this->categories as $category)
                                        <option value="{{$category->category_title}}">{{$category->category_title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-5 col-md-6">
                                <label class="dev-label">{{ __('messages.import.new_category') }}</label>
                                <input wire:model="changeCatTo" type="text" class="dev-input"
                                       placeholder="{{ __('messages.import.new_category_placeholder') }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <button type="submit" class="dev-btn dev-btn-red w-100"
                                        wire:loading.attr="disabled" wire:target="changeCategorySubmit">
                                    <span wire:loading.remove wire:target="changeCategorySubmit">{{ __('messages.import.change') }}</span>
                                    <span wire:loading wire:target="changeCategorySubmit">...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 4. Update File Paths --}}
            <div class="dev-card mb-4">
                <div class="card-header hdr-4">
                    <h3>{{ __('messages.import.update_file_paths') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span>{{ __('messages.import.file_paths_info') }}</span>
                    </div>
                    <form wire:submit.prevent="updateFilePaths">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5 col-md-6">
                                <label class="dev-label">{{ __('messages.import.old_path_prefix') }}</label>
                                <input wire:model="oldPath" type="text" class="dev-input" placeholder="e.g., uploads/ or tayqan/uploads/">
                                <small class="text-muted" style="font-size:11px;">{{ __('messages.import.old_path_hint') }}</small>
                            </div>
                            <div class="col-lg-5 col-md-6">
                                <label class="dev-label">{{ __('messages.import.new_path_prefix') }}</label>
                                <input wire:model="newPath" type="text" class="dev-input" placeholder="e.g., tayqan/uploads/ or uploads/">
                                <small class="text-muted" style="font-size:11px;">{{ __('messages.import.new_path_hint') }}</small>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <button type="submit" class="dev-btn dev-btn-green w-100"
                                        wire:loading.attr="disabled" wire:target="updateFilePaths">
                                    <span wire:loading.remove wire:target="updateFilePaths">{{ __('messages.import.update') }}</span>
                                    <span wire:loading wire:target="updateFilePaths">...</span>
                                </button>
                            </div>
                        </div>
                        @if($oldPath || $newPath)
                            <div class="preview-strip">
                                <div class="d-flex align-items-center gap-1 mb-2"><small class="fw-semibold text-muted">Preview</small></div>
                                <div class="row g-2">
                                    <div class="col-md-6 pv-old"><small class="fw-bold text-danger">Before:</small><code>{{ $oldPath ?: '(empty)' }}/2025/11/file.jpg</code></div>
                                    <div class="col-md-6 pv-new"><small class="fw-bold text-success">After:</small><code>{{ $newPath ?: '(empty)' }}/2025/11/file.jpg</code></div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- 5. Fix Text Encoding (Mojibake) --}}
            <div class="dev-card mb-4">
                <div class="card-header hdr-3">
                    <h3>{{ __('messages.import.fix_encoding') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span>{{ __('messages.import.fix_encoding_info') }}</span>
                    </div>
                    <div class="preview-strip">
                        <div class="d-flex align-items-center gap-1 mb-2"><small class="fw-semibold text-muted">Preview</small></div>
                        <div class="row g-2">
                            <div class="col-md-6 pv-old"><small class="fw-bold text-danger">Before:</small><code>â€˜Forced into deathâ€™</code></div>
                            <div class="col-md-6 pv-new"><small class="fw-bold text-success">After:</small><code>‘Forced into death’</code></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" wire:click="fixMojibake" class="dev-btn dev-btn-red"
                                wire:loading.attr="disabled" wire:target="fixMojibake">
                            <span wire:loading.remove wire:target="fixMojibake">{{ __('messages.import.fix_encoding_button') }}</span>
                            <span wire:loading wire:target="fixMojibake">...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 6. Set / Fix Language --}}
            <div class="dev-card mb-4">
                <div class="card-header hdr-5">
                    <h3>{{ __('messages.import.set_default_language') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span>{{ __('messages.import.language_info') }}</span>
                    </div>
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5 col-md-6">
                            <label class="dev-label">{{ __('messages.import.default_language') }}</label>
                            <select class="dev-input" wire:model="language">
                                @foreach($languages as $lang => $title)
                                    <option value="{{$lang}}">{{ is_array($title) ? ($title['name'] ?? $lang) : $title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <button type="button" wire:click="setSystemRecordsDefaultLanguage" class="dev-btn dev-btn-outline w-100"
                                    wire:loading.attr="disabled" wire:target="setSystemRecordsDefaultLanguage">
                                <span wire:loading.remove wire:target="setSystemRecordsDefaultLanguage">{{__('messages.import.set_null_only')}}</span>
                                <span wire:loading wire:target="setSystemRecordsDefaultLanguage">...</span>
                            </button>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <button type="button" wire:click="fixImportedDataLanguage" class="dev-btn dev-btn-purple w-100"
                                    wire:loading.attr="disabled" wire:target="fixImportedDataLanguage">
                                <span wire:loading.remove wire:target="fixImportedDataLanguage">{{__('messages.import.fix_imported_language')}}</span>
                                <span wire:loading wire:target="fixImportedDataLanguage">...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@section('script')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('run-import-step', (event) => {
                setTimeout(() => {
                    @this.runNextImportStep();
                }, 100);
            });

            @this.on('show-import-confirmation', (data) => {
                const info = data[0] || data;
                Swal.fire({
                    title: '{{ __("messages.import.confirm_import") }}',
                    html: info.message,
                    icon: info.isClean ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("messages.import.confirm") }}',
                    cancelButtonText: '{{ __("messages.import.cancel") }}',
                    confirmButtonColor: info.isClean ? '#dc2626' : '#6366f1',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.executeImport();
                    }
                });
            });
        });
    </script>
@endsection
