<div>
    @php
        $tAudio      = __('messages.file_manager.audio') ?: 'Audio';
        $tVideo      = __('messages.file_manager.video') ?: 'Video';
        $tImage      = __('messages.file_manager.image') ?: 'Image';
        $tFile       = __('messages.file_manager.file')  ?: 'File';
        $tPdf        = __('messages.file_manager.pdf')   ?: 'PDF';
        $tCopy       = __('messages.copy_link')          ?: 'Copy link';
        $tPreview    = __('messages.preview')            ?: 'Preview';
        $tEdit       = __('messages.edit')               ?: 'Edit';
        $tCrop       = __('messages.crop')               ?: 'Crop';
        $tDelete     = __('messages.delete')             ?: 'Delete';
        $tPlay       = __('messages.play')               ?: 'Play';
        $tPause      = __('messages.pause')              ?: 'Pause';
        $tLinkCopied = __('messages.link_copied')        ?: 'Link copied';
        $tOpenNew    = __('messages.open_in_new_tab')    ?: 'Open in new tab';
        $tDownload   = __('messages.download_samples')           ?: 'Download';
        $tClose      = __('messages.close')              ?: 'Close';

        // Editor translations
        $tEditorRotate   = __('messages.editor.rotate')      ?: 'Rotate';
        $tEditorZoomIn   = __('messages.editor.zoom_in')     ?: 'Zoom In';
        $tEditorZoomOut  = __('messages.editor.zoom_out')    ?: 'Zoom Out';
        $tEditorFlipH    = __('messages.editor.flip_h')      ?: 'Flip H';
        $tEditorFlipV    = __('messages.editor.flip_v')      ?: 'Flip V';
        $tEditorReset    = __('messages.editor.reset')       ?: 'Reset';
        $tEditorFree     = __('messages.editor.free')        ?: 'Free';
        $tEditorRatio11  = __('messages.editor.ratio_1_1')   ?: '1:1';
        $tEditorRatio169 = __('messages.editor.ratio_16_9')  ?: '16:9';
        $tEditorSave     = __('messages.editor.save')        ?: 'Save';
        $tEditorSaveNew  = __('messages.editor.save_as_new') ?: 'Save as New';
    @endphp

        <!-- File Library Modal (stays as bootstrap modal) -->
    <div class="modal custom-xl-modal fade" id="fileLibraryModal" data-bs-backdrop="static" data-bs-keyboard="false"
         tabindex="-1" aria-labelledby="fileLibraryModalLabel" aria-hidden="true" style="z-index:1060;"
         wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header align-items-center">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="fileLibraryModalLabel">
                        <i class="bi bi-collection"></i> {{ __('messages.file_manager.file_library') }}
                    </h5>
                    <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-dismiss="modal"
                            aria-label="Close" wire:click.prevent="pureLoad">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body position-relative">
                    <div class="row g-4">
                        <!-- Sidebar -->
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="library-sidebar p-3 rounded border sticky-md">
                                <!-- Search -->
                                <form id="library_search_form" role="form" class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="text" class="form-control"
                                               placeholder="{{ __('messages.file_manager.search_word') }}"
                                               wire:model.live="search">
                                    </div>
                                </form>

                                <!-- Types -->
                                <h6 class="text-muted mb-2 d-flex align-items-center gap-2">
                                    <i class="bi bi-folder2-open"></i> {{ __('messages.file_manager.folders') }}
                                </h6>
                                @php
                                    $types = [
                                        'all'    => __('messages.file_manager.all'),
                                        'images' => __('messages.file_manager.images'),
                                        'videos' => __('messages.file_manager.videos'),
                                        'files'  => __('messages.file_manager.files'),
                                        'audio'  => __('messages.file_manager.audio'),
                                    ];
                                @endphp
                                <ul class="folder-list list-unstyled m-0">
                                    @foreach($types as $type => $label)
                                        @if(!$hideSideBarOptions || $showType === $type)
                                            <li>
                                                <a wire:click.prevent="showSpecificType('{{ $type === 'all' ? 'all' : $type }}')"
                                                   class="d-flex align-items-center gap-2 px-2 py-2 rounded {{ $showType === $type ? 'selected bg-primary-subtle fw-semibold' : '' }}">
                                                    <i class="bi {{ $showType === $type ? 'bi-folder-fill' : 'bi-folder' }}"></i>
                                                    <span class="text-truncate">{{ $label }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>

                                @if($editFile)
                                    <div class="mt-4">
                                        <form wire:submit.prevent="editPhoto()">
                                            <label
                                                class="form-label mb-1">{{ __('messages.file_manager.name') }}</label>
                                            <input type="text" class="form-control mb-3"
                                                   placeholder="{{ __('messages.file_manager.name') }}"
                                                   wire:model="file_name">

                                            <div class="mb-3 small">
                                                <label
                                                    class="form-label mb-1">{{ __('messages.file_manager.url') }}</label><br>
                                                <a href="{{ file_url($editingFile->path) }}" target="_blank"
                                                   class="text-break">
                                                    {{ file_url($editingFile->path) }}
                                                </a>
                                            </div>

                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="bi bi-save2 me-1"></i>{{ __('messages.file_manager.save') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                @if($multipleSelection && !empty($selectedFile) && !$pureLoad)
                                    <div class="mt-4">
                                        <button class="btn btn-success w-100"
                                                wire:click.prevent="submitMultipleSelection">
                                            <i class="bi bi-check2-circle me-1"></i>{{ __('messages.choose') }}
                                        </button>
                                    </div>
                                @endif

                                @php
                                    function formatSizeUnits($bytes){
                                        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
                                        if ($bytes >= 1048576)   return number_format($bytes / 1048576, 2) . ' MB';
                                        if ($bytes >= 1024)      return number_format($bytes / 1024, 2) . ' KB';
                                        if ($bytes > 1)          return $bytes . ' bytes';
                                        if ($bytes == 1)         return $bytes . ' byte';
                                        return '0 bytes';
                                    }
                                @endphp

                                @if($pureLoad)
                                    @if(isset($fileDetails))
                                        <div class="mt-4 small">
                                            <div class="border rounded p-3">
                                                <div class="mb-1">
                                                    <strong>{{ __('messages.file_manager.name') }}</strong>: {{ $fileDetails?->file_name }}
                                                </div>
                                                <div class="mb-1">
                                                    <strong>{{ __('messages.file_manager.file_size') }}</strong>: {{ formatSizeUnits($fileDetails?->size) }}
                                                </div>
                                                <div class="mb-1">
                                                    <strong>{{ __('messages.file_manager.uploaded_at') }}</strong>: {{ $fileDetails?->created_at }}
                                                </div>
                                                <div class="mb-1">
                                                    <strong>{{ __('messages.file_manager.extension') }}</strong>: {{ $fileDetails?->extension }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty($selectedFile))
                                        @php
                                            $typeExtensions = [
                                                 'images' => ['jpg','jpeg','png','svg','gif','webp'],
                                                 'videos' => ['mp4','mov','webm','ogg'],
                                                 'files'  => ['pdf','doc','docx','txt','rtf','odt'],
                                                 'audio  '=> ['mp3','ogg','wav'],
                                             ];
                                        @endphp
                                        <div class="mt-4">
                                            @forelse($this->pageColumns as $column)
                                                @php
                                                    $columnType = $column['type'];
                                                    $allowedExtensions = $typeExtensions[$columnType] ?? [];
                                                    $isValid = empty(array_diff($selectedFileType, $allowedExtensions));
                                                @endphp
                                                @if(!$column['isMultiple'] && count($selectedFile) === 1)
                                                    @if($isValid)
                                                        <button class="btn btn-success w-100 mb-2"
                                                                wire:click.prevent="manualSelectImage('{{ $column['selectName'] }}')">
                                                            {{ __('messages.index.addTo') }} : {{ $column['name'] }}
                                                        </button>
                                                    @endif
                                                @elseif($column['isMultiple'])
                                                    @if($isValid)
                                                        <button class="btn btn-success w-100 mb-2"
                                                                wire:click.prevent="manualSelectImage('{{ $column['selectName'] }}')">
                                                            {{ __('messages.index.addTo') }} : {{ $column['name'] }}
                                                        </button>
                                                    @endif
                                                @endif
                                            @empty @endforelse
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="col-12 col-md-8 col-lg-9">
                            <style>
                                /* ===========================
                                   File Library Styles Only
                                   =========================== */
                                .modal-loading-overlay {
                                    position: absolute;
                                    inset: 0;
                                    width: 100%;
                                    height: 100%;
                                    background: rgba(255, 255, 255, .7);
                                    z-index: 2000
                                }

                                [data-bs-theme="dark"] .modal-loading-overlay {
                                    background: rgba(15, 23, 42, .55)
                                }

                                /* File Library Grid Items */

                                .file-item {
                                    position: relative;
                                    border: 1px solid var(--ui-card-border, #e5e7eb);
                                    border-radius: .9rem;
                                    padding: .55rem;
                                    background: var(--ui-card-bg, #fff);
                                    transition: .2s;
                                    height: 100%;
                                    display: flex;
                                    flex-direction: column
                                }

                                [data-bs-theme="dark"] .file-item {
                                    background: #1e293b;
                                    border-color: #26344b
                                }

                                .file-item:hover {
                                    box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
                                    transform: translateY(-2px)
                                }

                                .file-item.selected {
                                    outline: 2px solid var(--ui-accent, #3b82f6);
                                    box-shadow: 0 0 0 4px color-mix(in srgb, var(--ui-accent, #3b82f6) 20%, transparent)
                                }

                                .image-container {
                                    position: relative;
                                    overflow: hidden;
                                    cursor: pointer;
                                    border-radius: .7rem;
                                    height: 120px
                                }

                                .pinned-indicator {
                                    position: absolute;
                                    top: 6px;
                                    right: 6px;
                                    background: rgba(96, 94, 88, 0.9);
                                    color: #000;
                                    border-radius: 50%;
                                    width: 26px;
                                    height: 26px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 15px;
                                    box-shadow: 0 0 4px rgba(0, 0, 0, 0.3);
                                    z-index: 5;
                                }

                                .custom-thumb {
                                    width: 100%;
                                    height: 120px;
                                    object-fit: cover;
                                    border-radius: .7rem;
                                    transition: transform .25s ease;
                                    background: #e5e7eb
                                }

                                .video-thumb-wrapper {
                                    position: relative;
                                    width: 100%;
                                    height: 120px;
                                    border-radius: .7rem;
                                    overflow: hidden;
                                }

                                .video-play-overlay {
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    background: rgba(0, 0, 0, 0.6);
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    pointer-events: none;
                                    transition: all 0.3s ease;
                                }

                                .image-container:hover .video-play-overlay {
                                    background: rgba(0, 0, 0, 0.8);
                                    transform: translate(-50%, -50%) scale(1.1);
                                }

                                .video-play-overlay i {
                                    color: white;
                                    font-size: 1.5rem;
                                    margin-left: 3px;
                                }

                                [data-bs-theme="dark"] .custom-thumb {
                                    background: #0f172a
                                }

                                .image-container:hover .custom-thumb {
                                    transform: scale(1.03)
                                }

                                .nonvisual-thumb {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 100%;
                                    height: 120px;
                                    border-radius: .7rem;
                                    position: relative;
                                    overflow: hidden;
                                    color: #fff
                                }

                                .type-audio {
                                    background: radial-gradient(1200px 360px at 10% 110%, rgba(34, 197, 94, .85), rgba(6, 95, 70, .95)), linear-gradient(135deg, #10b981, #16a34a)
                                }

                                .type-file {
                                    background: linear-gradient(135deg, #6366f1, #3b82f6)
                                }

                                .type-pdf {
                                    background: linear-gradient(135deg, #ef4444, #b91c1c)
                                }

                                .type-doc {
                                    background: linear-gradient(135deg, #0ea5e9, #0284c7)
                                }

                                .type-image {
                                    background: linear-gradient(135deg, #9333ea, #2563eb)
                                }

                                .type-video {
                                    background: radial-gradient(800px 240px at 10% 110%, rgba(59, 130, 246, .65), rgba(2, 6, 23, .9)), linear-gradient(135deg, #1d4ed8, #0ea5e9)
                                }

                                .center-icon {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    gap: .35rem
                                }

                                .center-icon i {
                                    font-size: 2rem;
                                    color: #fff;
                                    opacity: .95
                                }

                                .nonvisual-title {
                                    position: absolute;
                                    bottom: 6px;
                                    left: 10px;
                                    right: 10px;
                                    font-size: .75rem;
                                    opacity: .9;
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    display: none
                                }

                                .eq-icon {
                                    display: flex;
                                    gap: 3px;
                                    align-items: flex-end
                                }

                                .eq-icon span {
                                    width: 4px;
                                    border-radius: 2px;
                                    background: #fff;
                                    display: inline-block;
                                    animation: bar 1.2s ease-in-out infinite;
                                    animation-play-state: paused
                                }

                                .is-playing .eq-icon span {
                                    animation-play-state: running
                                }

                                .eq-icon span:nth-child(1) {
                                    height: 10px;
                                    animation-delay: .1s
                                }

                                .eq-icon span:nth-child(2) {
                                    height: 16px;
                                    animation-delay: .25s
                                }

                                .eq-icon span:nth-child(3) {
                                    height: 22px;
                                    animation-delay: .4s
                                }

                                .eq-icon span:nth-child(4) {
                                    height: 14px;
                                    animation-delay: .55s
                                }

                                @keyframes bar {
                                    0%, 100% {
                                        transform: scaleY(.6)
                                    }
                                    50% {
                                        transform: scaleY(1.2)
                                    }
                                }

                                .file-emblem {
                                    position: absolute;
                                    inset: auto 10px 10px auto;
                                    background: rgba(0, 0, 0, .45);
                                    color: #fff;
                                    border-radius: .5rem;
                                    padding: .2rem .45rem;
                                    font-weight: 700;
                                    font-size: .75rem
                                }

                                :dir(rtl) .file-emblem {
                                    right: auto;
                                    left: 10px
                                }

                                .type-badge {
                                    position: absolute;
                                    left: 8px;
                                    bottom: 8px;
                                    display: flex;
                                    align-items: center;
                                    gap: 6px;
                                    background: rgba(0, 0, 0, .55);
                                    color: #fff;
                                    border-radius: 999px;
                                    padding: 4px 8px;
                                    font-size: .75rem;
                                    z-index: 5
                                }

                                :dir(rtl) .type-badge {
                                    left: auto;
                                    right: 8px
                                }

                                .type-badge i {
                                    color: #fff
                                }

                                .file-options {

                                    backdrop-filter: blur(2px);
                                    padding: 4px 0;
                                    border-radius: 10px;
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 4px;
                                    transition: .25s;
                                    z-index: 10
                                }

                                .image-container:hover .file-options {
                                    opacity: 1
                                }

                                .file-options a {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 24px;
                                    height: 24px;
                                    border-radius: 4px;
                                    background: #eee
                                }

                                [data-bs-theme="dark"] .file-options a {
                                    background: var(--brand-gray-300);
                                }

                                .file-options a:hover {
                                    background: rgba(255, 255, 255, .2)
                                }

                                .file-options i {
                                    color: var(--brand-sidebar-fg);
                                    font-size: .9rem
                                }

                                .checkmark-overlay {
                                    display: none;
                                    position: absolute;
                                    inset: 0;
                                    background: rgba(0, 0, 0, .25);
                                    z-index: 2;
                                    justify-content: center;
                                    align-items: center;
                                    border-radius: .7rem
                                }

                                .file-item.selected .checkmark-overlay {
                                    display: flex
                                }

                                .checkmark-overlay::after {
                                    content: '✓';
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    width: 40px;
                                    height: 40px;
                                    background: #22c55e;
                                    border-radius: 50%;
                                    font-size: 1.25rem;
                                    color: #fff;
                                    font-weight: 700;
                                    box-shadow: 0 0 8px rgba(0, 0, 0, .25)
                                }

                                /* Dropzone styles moved to /public/css/dropzone-custom.css */

                                .inline-audio {
                                    display: none !important
                                }

                                /* ===========================
                                   Fullscreen Overlay Viewer
                                   =========================== */
                                .dfm-overlay {
                                    position: fixed;
                                    inset: 0;
                                    z-index: 2000;
                                    display: none;
                                    background: rgba(2, 6, 23, .85);
                                    backdrop-filter: saturate(130%) blur(2px)
                                }

                                .dfm-overlay.is-open {
                                    display: block
                                }

                                .dfm-overlay__inner {
                                    position: absolute;
                                    inset: 0;
                                    display: flex;
                                    flex-direction: column
                                }

                                .dfm-overlay__bar {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    padding: .5rem .75rem;
                                    background: linear-gradient(90deg, rgba(59, 130, 246, .16), transparent);
                                    color: #e5e7eb
                                }

                                .dfm-overlay__bar .btn {
                                    border-radius: .65rem
                                }

                                .dfm-overlay__stage {
                                    flex: 1;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    padding: 10px
                                }

                                .dfm-overlay__stage img, .dfm-overlay__stage video {
                                    max-width: 100%;
                                    max-height: 90vh;
                                    border-radius: 12px;
                                    box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
                                    object-fit: contain
                                }

                                .dfm-overlay__frame {
                                    width: 100%;
                                    height: 90vh;
                                    border: 0;
                                    border-radius: 12px;
                                    background: #0b1220;
                                    box-shadow: 0 10px 30px rgba(0, 0, 0, .35)
                                }

                                .dfm-body-lock {
                                    overflow: hidden !important
                                }
                            </style>

                            <div wire:loading.class.remove="d-none"
                                 wire:target="selectImage,editingPhoto,submitMultipleSelection,deletePhoto"
                                 class="modal-loading-overlay d-flex justify-content-center align-items-center d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">{{ __('messages.loading') }}</span>
                                </div>
                            </div>

                            <!-- Uploader -->
                            <form action="{{ route('upload-files-to-library') }}" method="POST" class="dropzone mb-4"
                                  id="uploadForm">
                                @csrf
                                <div id="library" class="dz-clickable">
                                    <div class="dz-default dz-message">
                                        <span class="m-auto d-inline-flex align-items-center gap-2">
                                            <i class="bi bi-cloud-arrow-up"></i>{{ __('messages.file_manager.drop_files_to_upload') }}
                                        </span>
                                    </div>
                                </div>
                            </form>

                            <!-- Grid -->
                            <div class="row g-3">
                                @php
                                    $imageIcons = [
                                        'video' => ['mp4','mov','webm','ogg'],
                                        'image' => ['jpg','jpeg','png','svg','gif','webp'],
                                        'pdf'   => ['pdf'],
                                        'audio' => ['mp3','ogg','wav'],
                                        'doc'   => ['doc','docx','txt','rtf','odt'],
                                    ];
                                @endphp

                                @foreach($this->images as $image)
                                    @php
                                        $ext = strtolower($image->extension);
                                        $isImage = in_array($ext, $imageIcons['image']);
                                        $isVideo = in_array($ext, $imageIcons['video']);
                                        $isAudio = in_array($ext, $imageIcons['audio']);
                                        $isPdf   = in_array($ext, $imageIcons['pdf']);
                                        $isDoc   = in_array($ext, $imageIcons['doc']);
                                        $isGenericFile = !$isImage && !$isVideo && !$isAudio && !$isPdf && !$isDoc;
                                        $genericIcon = 'bi-file-earmark';
                                        $limitedName = \Illuminate\Support\Str::limit($image->file_name ?? $image->original_name, 26);
                                        $fullUrl = file_url($image->path);
                                    @endphp

                                    <div class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-2"
                                         wire:key="file-{{ $image->id }}">
                                        <div
                                            class="file-item {{ in_array($image->id, $selectedFile) ? 'selected' : '' }}">
                                            <div class="image-container"
                                                 wire:click.prevent="selectImage('{{ $image->id }}','{{ $image->extension }}')">
                                                @if($image?->isPinned)
                                                    <div class="pinned-indicator" title="Pinned">
                                                        <i class="bi bi-pin-fill"></i>
                                                    </div>
                                                @endif
                                                <div class="checkmark-overlay"></div>

                                                @if($isImage)
                                                    <img src="{{ $fullUrl }}" alt="file"
                                                         class="custom-thumb fill-image">
                                                    <div class="type-badge" title="{{ $tImage }}"
                                                         aria-label="{{ $tImage }}">
                                                        <i class="bi bi-image"></i><span>{{ $tImage }}</span>
                                                    </div>
                                                    <span class="file-emblem">{{ strtoupper($ext) }}</span>

                                                @elseif($isVideo)
                                                    <div class="video-thumb-wrapper">
                                                        <video
                                                            class="custom-thumb js-video-thumb"
                                                            data-video-src="{{ $fullUrl }}"
                                                            data-video-id="{{ $image->id }}"
                                                            data-badge-label="{{ $tVideo }}"
                                                            data-title="{{ $limitedName }}"
                                                            data-ext="{{ strtoupper($ext) }}"
                                                            preload="metadata"
                                                            muted
                                                            playsinline
                                                        >
                                                            <source src="{{ $fullUrl }}#t=0.5" type="video/{{ $ext }}">
                                                        </video>
                                                        <div class="video-play-overlay">
                                                            <i class="bi bi-play-fill"></i>
                                                        </div>
                                                    </div>
                                                    <div class="type-badge" title="{{ $tVideo }}"
                                                         aria-label="{{ $tVideo }}">
                                                        <i class="bi bi-play-fill"></i><span>{{ $tVideo }}</span>
                                                    </div>
                                                    <span class="file-emblem">{{ strtoupper($ext) }}</span>

                                                @elseif($isAudio)
                                                    <div class="nonvisual-thumb type-audio">
                                                        <div class="eq-icon" aria-hidden="true">
                                                            <span></span><span></span><span></span><span></span>
                                                        </div>
                                                        <div class="nonvisual-title">{{ $limitedName }}</div>
                                                        <div class="type-badge" title="{{ $tAudio }}"
                                                             aria-label="{{ $tAudio }}">
                                                            <i class="bi bi-soundwave"></i><span>{{ $tAudio }}</span>
                                                        </div>
                                                        <span class="file-emblem">{{ strtoupper($ext) }}</span>
                                                    </div>

                                                @else
                                                    <div
                                                        class="nonvisual-thumb {{ $isPdf ? 'type-pdf' : ($isDoc ? 'type-doc' : 'type-file') }}">
                                                        <i class="bi {{ $isPdf ? 'bi-filetype-pdf' : ($isDoc ? 'bi-file-earmark-text' : $genericIcon) }}"
                                                           style="font-size:2rem;"></i>
                                                        <div class="nonvisual-title">{{ $limitedName }}</div>
                                                        <div class="type-badge" title="{{ $isPdf ? $tPdf : $tFile }}"
                                                             aria-label="{{ $isPdf ? $tPdf : $tFile }}">
                                                            <i class="bi {{ $isPdf ? 'bi-filetype-pdf' : 'bi-file-earmark' }}"></i>
                                                            <span>{{ $isPdf ? $tPdf : $tFile }}</span>
                                                        </div>
                                                        <span class="file-emblem">{{ strtoupper($ext) }}</span>
                                                    </div>
                                                @endif


                                            </div>

                                            <div class="file-name text-center px-2 mt-2">
                                                <div style="min-height:20px;margin-bottom:3px;">
                                                    <span class="d-block text-truncate title">
                                                        {{ \Illuminate\Support\Str::limit($image->file_name ?? $image->original_name, 30) }}
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block">
                                                    @php
                                                        if ($image->size >= 1073741824)      echo round($image->size / 1073741824, 2) . ' GB';
                                                        elseif ($image->size >= 1048576)     echo round($image->size / 1048576, 2) . ' MB';
                                                        else                                 echo round($image->size / 1024, 2) . ' KB';
                                                    @endphp
                                                </small>

                                                @if($isAudio)
                                                    <div class="inline-audio" data-stop-prop>
                                                        <audio preload="none">
                                                            <source src="{{ $fullUrl }}" type="audio/{{ $ext }}">
                                                            Your browser does not support audio.
                                                        </audio>
                                                    </div>
                                                @endif


                                                <!-- Actions -->
                                                <div class="file-options">
                                                    {{-- Open unified overlay (image/video/pdf) --}}
                                                    <a wire:click.prevent="pinFile('{{$image?->id}}')"
                                                       class="text-decoration-none dfm-open-preview"
                                                       data-stop-prop>
                                                        @if($image?->isPinned)
                                                            <i class="bi bi-pin-fill"></i>
                                                        @else
                                                            <i class="bi bi-pin"></i>
                                                        @endif
                                                    </a>
                                                    @if($isImage || $isVideo)
                                                        <a href="javascript:void(0)"
                                                           class="text-decoration-none dfm-open-preview"
                                                           data-stop-prop
                                                           data-dfm-type="{{ $isImage ? 'image' : 'video' }}"
                                                           data-dfm-url="{{ $fullUrl }}"
                                                           data-dfm-title="{{ $limitedName }}"
                                                           title="{{ $tPreview }}" aria-label="{{ $tPreview }}">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if($isPdf)
                                                        <a href="javascript:void(0)"
                                                           class="text-decoration-none dfm-open-preview"
                                                           data-stop-prop
                                                           data-dfm-type="pdf"
                                                           data-dfm-url="{{ $fullUrl }}"
                                                           data-dfm-title="{{ $limitedName }}"
                                                           title="{{ $tPreview }}" aria-label="{{ $tPreview }}">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Docs: open in new tab --}}
                                                    @if($isDoc || $isGenericFile)
                                                        <a href="{{ $fullUrl }}" target="_blank" rel="noopener"
                                                           class="text-decoration-none" data-stop-prop
                                                           title="{{ $tOpenNew }}" aria-label="{{ $tOpenNew }}">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Audio play/pause --}}
                                                    @if($isAudio)
                                                        <a href="javascript:void(0)"
                                                           class="text-decoration-none js-audio-toggle"
                                                           data-audio-src="{{ $fullUrl }}"
                                                           data-stop-prop
                                                           title="{{ $tPlay }}" aria-label="{{ $tPlay }}">
                                                            <i class="bi bi-play-fill"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Copy link --}}
                                                    <a href="javascript:void(0)"
                                                       class="text-decoration-none js-copy-link"
                                                       data-url="{{ config('app.url') . $fullUrl }}"
                                                       data-stop-prop
                                                       title="{{ $tCopy }}" aria-label="{{ $tCopy }}">
                                                        <i class="bi bi-link-45deg"></i>
                                                    </a>

                                                    {{-- Edit / Crop (images only) --}}
                                                    @if(in_array($image->extension,['jpg','png','jpeg','svg','webp']))
                                                        <a wire:click.prevent="openEditor('{{ $image->id }}')"
                                                           class="text-decoration-none" data-stop-prop
                                                           title="{{ $tCrop }}" aria-label="{{ $tCrop }}">
                                                            <i class="bi bi-crop"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Rename --}}
                                                    <a wire:click.prevent="editingPhoto('{{ $image->id }}')"
                                                       class="text-decoration-none" data-stop-prop
                                                       title="{{ $tEdit }}" aria-label="{{ $tEdit }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    {{-- Delete --}}
                                                    <a wire:confirm="{{ __('messages.icons.confirm_delete_file') }}"
                                                       wire:click.prevent="deletePhoto('{{ $image->id }}', '{{ $image->original_name }}')"
                                                       class="text-decoration-none" data-stop-prop
                                                       title="{{ $tDelete }}" aria-label="{{ $tDelete }}">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($this->images->count() >= $perPage)
                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-primary" wire:click="loadMore">
                                        <span class="d-inline-flex align-items-center gap-2" wire:loading.class="d-none"
                                              wire:target="loadMore">
                                            <i class="bi bi-arrow-down-circle"></i>{{ __('messages.load_more') ?? 'Load more' }}
                                        </span>
                                        <span class="spinner-grow spinner-grow-sm d-none"
                                              wire:loading.class.remove="d-none" wire:target="loadMore" role="status"
                                              aria-hidden="true"></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    @if($pureLoad && $multipleSelection && count($selectedFile) > 1)
                        <div class="dropdown">
                            <button class="btn btn-danger dropdown-toggle" type="button"
                                    id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"
                                    wire:key="{{ uniqid() }}">
                                {{ __('messages.bulk_actions') }} {{ count($selectedFile) }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" style="z-index: 10;">

                                <form id="exportForm" action="{{ route('export-files-from-library') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="selectedFile" id="selectedFile">
                                </form>

                                <li>
                                    <a class="dropdown-item text-success" wire:click.prevent="bulkExport">
                                        <i class="bi bi-upload text-success"></i>
                                        <span class="ml-2">{{__('messages.export')}}</span>
                                    </a>
                                </li>
                                @script
                                <script>
                                    Livewire.on('exportBulk', (data) => {
                                        let selected = data.selectedFile;
                                        document.getElementById('selectedFile').value = JSON.stringify(selected);
                                        document.getElementById('exportForm').submit();
                                    });
                                </script>
                                @endscript

                                <li>
                                    <a class="dropdown-item text-danger" wire:click.prevent="bulkDeletePhoto"
                                       wire:key="{{ uniqid() }}"
                                       wire:confirm="{{ __('messages.icons.confirm_delete_file') }}">
                                        <i class="bi bi-trash text-danger"></i>
                                        {{ __('messages.delete') }}
                                    </a>
                                </li>

                            </ul>
                        </div>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click.prevent="pureLoad">
                        <i class="bi bi-x-circle me-1"></i>{{ $tClose }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Editor Modal (kept as is; backend-safe) -->
    <div class="modal custom-xl-modal fade" id="imageEditor" data-bs-backdrop="static" data-bs-keyboard="false"
         tabindex="-2" aria-labelledby="imageEditorLabel" aria-hidden="true" style="z-index:1061;" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                {{--                <div class="modal-header align-items-center">--}}
                {{--                    <h6 class="modal-title d-flex align-items-center gap-2" id="imageEditorLabel">--}}
                {{--                        <i class="bi bi-scissors"></i> {{ $tEdit }}--}}
                {{--                    </h6>--}}
                {{--                    <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-dismiss="modal"--}}
                {{--                            aria-label="Close">--}}
                {{--                        <i class="bi bi-x-lg"></i>--}}
                {{--                    </button>--}}
                {{--                </div>--}}
                <div class="modal-body">
                    <div class="text-center">
                        <img id="editorImage" src="" style="max-width:100%;max-height:600px;" alt="edit image"
                             crossorigin="anonymous"/>
                    </div>
                    <div class="p-3 text-center d-flex flex-wrap justify-content-center gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="rotateImage()"><i
                                class="bi bi-arrow-clockwise me-1"></i>{{ $tEditorRotate }}</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="zoomIn()"><i
                                class="bi bi-zoom-in me-1"></i>{{ $tEditorZoomIn }}</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="zoomOut()"><i
                                class="bi bi-zoom-out me-1"></i>{{ $tEditorZoomOut }}</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="flipHorizontal()"><i
                                class="bi bi-symmetry-horizontal me-1"></i>{{ $tEditorFlipH }}</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="flipVertical()"><i
                                class="bi bi-symmetry-vertical me-1"></i>{{ $tEditorFlipV }}</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="resetCropper()"><i
                                class="bi bi-arrow-counterclockwise me-1"></i>{{ $tEditorReset }}</button>
                        <button class="btn btn-sm btn-outline-secondary"
                                onclick="setAspectRatio(NaN)">{{ $tEditorFree }}</button>
                        <button class="btn btn-sm btn-outline-secondary"
                                onclick="setAspectRatio(1)">{{ $tEditorRatio11 }}</button>
                        <button class="btn btn-sm btn-outline-secondary"
                                onclick="setAspectRatio(16/9)">{{ $tEditorRatio169 }}</button>
                        <button class="btn btn-sm btn-outline-dark" onclick="downloadCropped()"><i
                                class="bi bi-download me-1"></i>{{ $tDownload }}</button>
                        <button class="btn btn-sm btn-primary" onclick="cropAndUploadAsNew()"><i
                                class="bi bi-file-earmark-plus me-1"></i>{{ $tEditorSaveNew }}</button>
                        <button class="btn btn-sm btn-success" onclick="cropAndUpload()"><i
                                class="bi bi-check-lg me-1"></i>{{ $tEditorSave }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== DFM Fullscreen Overlay ========== -->
    <div id="dfmOverlay" class="dfm-overlay" aria-hidden="true">
        <div class="dfm-overlay__inner">
            <div class="dfm-overlay__bar">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-eye"></i>
                    <span id="dfmOverlayTitle">{{ $tPreview }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a id="dfmOverlayOpenNew" class="btn btn-light btn-sm" target="_blank" rel="noopener"
                       title="{{ $tOpenNew }}">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <a id="dfmOverlayDownload" class="btn btn-light btn-sm" download title="{{ $tDownload }}">
                        <i class="bi bi-download"></i>
                    </a>
                    <button id="dfmOverlayClose" type="button" class="btn btn-light btn-sm" title="{{ $tClose }}">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="dfm-overlay__stage" id="dfmOverlayStage">
                <!-- Dynamic content goes here -->
            </div>
        </div>
    </div>

    <!-- Cropper -->
    <script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
    <link href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet"/>

    <!-- Custom Dropzone Styles -->
    <link href="{{ asset('css/dropzone-custom.css') }}" rel="stylesheet"/>
</div>

@script
<script>
    // Keep legacy Livewire event (won't be used now but safe for compatibility)
    Livewire.on('previewImage', () => {
        // Old modal removed in favor of overlay; we could map it to overlay if still triggered.
        // $('#imagePreview').modal('show'); // intentionally disabled
    });
</script>
@endscript

<script>
    /**
     * ===========================
     * DFM Frontend Layer (no backend changes)
     * - Fullscreen overlay preview (image/video/pdf) to avoid stacked modals
     * - Scoped classes "dfm-*"
     * - Professional comments for maintainability
     * ===========================
     */

        // -----------------------------
        // Dropzone configuration (unchanged)
        // -----------------------------
        // Polished preview animations and styles
    const dfmDropzoneStyles = `
        <style>
        .dz-preview.dz-file-preview {
            transition: transform .28s cubic-bezier(.2,.9,.3,1), box-shadow .28s ease, opacity .3s ease;
            transform-origin: center top;
            border-radius: .6rem;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        }

        .dz-preview.dz-uploading { transform: translateY(-2px); }

        .dz-progress-wrap { position: relative; height: 8px; background: rgba(0,0,0,0.06); border-radius: 6px; overflow: hidden; margin-top:8px; }
        .dz-upload { height: 100%; width: 0%; background: linear-gradient(90deg,#4f46e5,#06b6d4); transition: width .45s cubic-bezier(.2,.9,.3,1); display:block; }
        .dz-upload-text { position:absolute; right:8px; top:-20px; font-size:0.75rem; color:var(--bs-body-color); }

        .dz-success-mark, .dz-error-mark { transition: transform .32s cubic-bezier(.2,.9,.3,1), opacity .28s; transform: scale(.6); opacity:0; }
        .dz-preview.dz-success .dz-success-mark { transform: scale(1); opacity:1; }
        .dz-preview.dz-error .dz-error-mark { transform: scale(1); opacity:1; }

        .dz-thumb-overlay { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background: linear-gradient(180deg, rgba(0,0,0,0.0), rgba(0,0,0,0.24)); opacity:0; transition: opacity .22s; }
        .dz-preview:hover .dz-thumb-overlay { opacity:1; }

        .dz-preview .dz-filename { font-size:0.8rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        </style>
    `;
    document.head.insertAdjacentHTML('beforeend', dfmDropzoneStyles);
    Dropzone.options.uploadForm = {
        init: function () {
            const dz = this;

            dz.on("addedfile", function (file) {
                // تحديد نوع الملف وإضافة data-attribute
                const imageContainer = file.previewElement.querySelector('.dz-image');
                if (imageContainer) {
                    const ext = file.name.split('.').pop().toLowerCase();
                    const videoExts = ['mp4', 'mov', 'webm', 'ogg', 'avi', 'mkv'];
                    const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

                    if (videoExts.includes(ext)) {
                        imageContainer.setAttribute('data-file-type', 'video');
                    } else if (!imageExts.includes(ext)) {
                        imageContainer.setAttribute('data-file-type', 'file');
                    }
                }
            });

            dz.on("sending", function (file, xhr, formData) {
                if (this.options.chunking) {
                    formData.append("dzuuid", file.upload.uuid);
                    formData.append("dzchunkindex", file.upload.chunks.length - 1);
                    formData.append("dztotalchunkcount", file.upload.totalChunkCount);
                    formData.append("chunk", 1);
                }
            });

            dz.on("success", (file, response) => {
                const el = file.previewElement;
                if (response?.success && (response.fileId || response.message === 'File uploaded successfully')) {
                    const successMark = el.querySelector('.dz-success-mark');
                    if (successMark) successMark.style.opacity = '1';
                    el.classList.remove('dz-uploading');
                    el.classList.add('dz-success');

                    setTimeout(() => {
                        dz.removeFile(file);
                        if (dz.getUploadingFiles().length === 0 && dz.getQueuedFiles().length === 0) {
                            Livewire?.dispatch('refreshData');
                        }
                    }, 900);
                } else {
                    const errorMessage = response?.message || 'Upload failed';
                    const errorElement = el.querySelector('.dz-error-message');
                    if (errorElement) {
                        errorElement.textContent = errorMessage;
                        errorElement.style.display = 'block';
                    }
                    const errorMark = el.querySelector('.dz-error-mark');
                    if (errorMark) errorMark.style.opacity = '1';
                    el.classList.remove('dz-uploading');
                    el.classList.add('dz-error');
                }
            });

            dz.on("error", (file, response) => {
                let message = 'Upload failed';
                if (typeof response === 'string') message = response;
                else if (response?.message) message = response.message;
                else if (response?.xhr?.response) {
                    try {
                        const parsed = JSON.parse(response.xhr.response);
                        if (parsed.message) message = parsed.message;
                    } catch {
                    }
                }
                const el = file.previewElement;
                const errorElement = el?.querySelector('.dz-error-message');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
                const errorMark = el?.querySelector('.dz-error-mark');
                if (errorMark) errorMark.style.opacity = '1';
                el?.classList.remove('dz-uploading');
                el?.classList.add('dz-error');
            });
        },
        maxFilesize: 5000000,
        chunking: true,
        chunkSize: 5 * 1024 * 1024,
        parallelUploads: 2,
        timeout: 0,
        retryChunks: true,
        retryChunksLimit: 5,
        forceChunking: true,
        maxFiles: 15,
        uploadMultiple: false,
        acceptedFiles: ".webp,.jpg,.jpeg,.png,.mp4,.mov,.webm,.ogg,.pdf,.mp3,.wav,.ogg,.doc,.docx,.txt,.rtf,.odt",
        addRemoveLinks: true,
        dictDefaultMessage: "{{ __('messages.file_manager.drop_files_to_upload') }}",
        dictRemoveFile: "{{ __('messages.file_manager.remove') }}",
        dictCancelUpload: "{{ __('messages.file_manager.cancel') }}",
        dictFileTooBig: "{{ __('messages.file_manager.file_too_big') }}",
        dictInvalidFileType: "{{ __('messages.file_manager.invalid_file_type') }}",
        previewTemplate: `
            <div class="dz-preview dz-file-preview">
                <a data-dz-remove class="dz-remove" href="javascript:undefined;">{{ __('messages.file_manager.cancel') }}</a>
                <div class="dz-image position-relative" style="height:84px;">
                    <img data-dz-thumbnail class="img-fluid" style="width:100%;height:84px;object-fit:cover;border-radius:.45rem;" />
                    <div class="dz-thumb-overlay"><i class="bi bi-eye-fill text-white fs-4"></i></div>
                </div>
                <div class="dz-details p-2">
                    <div class="dz-filename"><span data-dz-name></span></div>
                    <div class="dz-size"><span data-dz-size></span></div>
                    <div class="dz-progress-wrap">
                        <span class="dz-upload" data-dz-uploadprogress></span>
                    </div>
                    <div class="dz-upload-text">0%</div>
                </div>
                <div class="dz-success-mark">
                    <svg width="54" height="54" viewBox="0 0 54 54" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="27" cy="27" r="25" stroke="white" stroke-width="2" fill="#28a745"/>
                        <path fill="white" d="M22.5 38l-11-11 3-3 8 8 16-16 3 3z"></path>
                    </svg>
                </div>
                <div class="dz-error-mark">
                    <svg width="54" height="54" viewBox="0 0 54 54" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="27" cy="27" r="25" stroke="white" stroke-width="2" fill="#be2626"/>
                        <path fill="white" d="M36 18L18 36M18 18L36 36" stroke="white" stroke-width="2"></path>
                    </svg>
                </div>
                <div class="dz-error-message"><span data-dz-errormessage></span></div>
            </div>
        `,
        uploadprogress: function (file, progress) {
            if (!file.previewElement) return;
            const bar = file.previewElement.querySelector(".dz-upload");
            const pct = Math.max(1, Math.min(100, Math.round(progress)));
            if (bar) {
                file.previewElement.classList.add('dz-uploading');
                bar.style.width = pct + "%";
            }
            const label = file.previewElement.querySelector(".dz-upload-text");
            if (label) label.textContent = `${pct}%`;
        }
    };

    // -----------------------------
    // Video thumbnails - تحسين لإظهار لقطة من الفيديو
    // -----------------------------
    function initVideoThumbs(forceReload = false) {
        const videoThumbs = document.querySelectorAll('video.js-video-thumb[data-video-src]');
        videoThumbs.forEach(video => {
            // إذا كان forceReload = true، نعيد تحميل جميع الفيديوهات
            // وإلا نتخطى الفيديوهات الجاهزة
            if (!forceReload && video.dataset.thumbReady === '1') return;

            // تعليم الفيديو كجاري التحميل
            video.dataset.thumbReady = '0';

            // إعادة تحميل الفيديو لضمان عرض اللقطة الصحيحة
            const videoSrc = video.getAttribute('data-video-src');
            const source = video.querySelector('source');
            if (source && videoSrc) {
                // إضافة timestamp لتجنب الكاش
                const timestamp = new Date().getTime();
                source.src = videoSrc + '#t=0.5';
                video.load();
            }

            // عند تحميل البيانات الوصفية، سيظهر الفريم الأول تلقائياً
            const handleLoadedData = function () {
                video.dataset.thumbReady = '1';
                // تعيين الوقت إلى نصف ثانية لإظهار لقطة أفضل
                if (video.duration > 0.5) {
                    video.currentTime = 0.5;
                }
            };

            // إزالة المستمع القديم إذا كان موجوداً وإضافة واحد جديد
            video.removeEventListener('loadeddata', handleLoadedData);
            video.addEventListener('loadeddata', handleLoadedData, {once: true});

            // في حالة حدوث خطأ، نحاول تحميل الفيديو
            const handleError = function (e) {
                console.warn('Video thumbnail load error:', e);
                video.dataset.thumbReady = '1'; // تعليم كمكتمل لتجنب إعادة المحاولة
            };

            video.removeEventListener('error', handleError);
            video.addEventListener('error', handleError, {once: true});
        });
    }

    // -----------------------------
    // Clipboard helper
    // -----------------------------
    function copyTextToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
            } catch {
            }
            document.body.removeChild(ta);
        }
        const toast = document.createElement('div');
        toast.textContent = @json($tLinkCopied);
        toast.style.cssText = 'position:fixed;bottom:16px;left:50%;transform:translateX(-50%);background:#111;color:#fff;padding:.45rem .7rem;border-radius:.5rem;z-index:3000;opacity:.95';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 1400);
    }

    // -----------------------------
    // Audio controls
    // -----------------------------
    function pauseAllAudios(exceptEl) {
        document.querySelectorAll('.inline-audio audio').forEach(a => {
            if (a !== exceptEl) {
                a.pause();
                const card = a.closest('.file-item');
                card?.querySelector('.nonvisual-thumb.type-audio')?.classList.remove('is-playing');
                const icon = card?.querySelector('.file-options .js-audio-toggle i');
                if (icon) {
                    icon.classList.remove('bi-pause-fill');
                    icon.classList.add('bi-play-fill');
                }
            }
        });
    }

    // -----------------------------
    // Overlay viewer (image/video/pdf)
    // -----------------------------
    const DFM_OVERLAY_ID = 'dfmOverlay';

    function dfmOpenOverlay(kind, url, title) {
        // Lock body scroll while overlay is open
        document.documentElement.classList.add('dfm-body-lock');
        document.body.classList.add('dfm-body-lock');

        const el = document.getElementById(DFM_OVERLAY_ID);
        const stage = document.getElementById('dfmOverlayStage');
        const titleEl = document.getElementById('dfmOverlayTitle');
        const openBtn = document.getElementById('dfmOverlayOpenNew');
        const downBtn = document.getElementById('dfmOverlayDownload');
        const closeBtn = document.getElementById('dfmOverlayClose');

        // Reset stage
        stage.innerHTML = '';

        // Build content by kind
        if (kind === 'image') {
            const img = document.createElement('img');
            img.src = url;
            img.alt = title || 'Preview';
            stage.appendChild(img);
        } else if (kind === 'video') {
            const vid = document.createElement('video');
            vid.controls = true;
            vid.playsInline = true;
            const src = document.createElement('source');
            const ext = (url.split('?')[0].split('.').pop() || '').toLowerCase();
            src.type = ext ? `video/${ext}` : 'video/mp4';
            src.src = url;
            vid.appendChild(src);
            stage.appendChild(vid);
        } else if (kind === 'pdf') {
            const hint = '#view=FitH&toolbar=0&navpanes=0&scrollbar=1';
            const iframe = document.createElement('iframe');
            iframe.className = 'dfm-overlay__frame';
            iframe.title = title || 'PDF preview';
            iframe.src = url + (url.includes('#') ? '' : hint);
            stage.appendChild(iframe);
        }

        // Toolbar bindings
        titleEl.textContent = title || @json($tPreview);
        openBtn.href = url;
        downBtn.href = url;

        // Show overlay
        el.classList.add('is-open');

        // Close actions
        function closeOverlay() {
            el.classList.remove('is-open');
            stage.innerHTML = '';
            document.documentElement.classList.remove('dfm-body-lock');
            document.body.classList.remove('dfm-body-lock');
            document.removeEventListener('keydown', onEsc);
            closeBtn.removeEventListener('click', closeOverlay);
            el.removeEventListener('click', backdropClose);
        }

        function onEsc(e) {
            if (e.key === 'Escape') closeOverlay();
        }

        function backdropClose(e) {
            if (e.target.id === DFM_OVERLAY_ID) closeOverlay();
        }

        closeBtn.addEventListener('click', closeOverlay);
        document.addEventListener('keydown', onEsc);
        el.addEventListener('click', backdropClose);
    }

    // -----------------------------
    // DOM bindings
    // -----------------------------
    function initLibraryInteractions() {
        document.addEventListener('click', function (e) {
            // Stop propagation for any element with data-stop-prop
            if (e.target.closest('[data-stop-prop]')) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Copy link
            const copyBtn = e.target.closest('.js-copy-link');
            if (copyBtn) {
                e.stopPropagation();
                navigator.clipboard.writeText(copyBtn.dataset.url);
                const toast = document.createElement('div');
                toast.textContent = @json($tLinkCopied);
                toast.style.cssText = 'position:fixed;bottom:16px;left:50%;transform:translateX(-50%);background:#111;color:#fff;padding:.45rem .7rem;border-radius:.5rem;z-index:3000;opacity:.95';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 1400);
                return;
            }

            // Audio toggle
            const audioBtn = e.target.closest('.js-audio-toggle');
            if (audioBtn) {
                handleAudioToggle(audioBtn);
                return;
            }

            // Overlay preview
            const previewBtn = e.target.closest('.dfm-open-preview');
            if (previewBtn) {
                const type = previewBtn.dataset.dfmType;
                const url = previewBtn.dataset.dfmUrl;
                const title = previewBtn.dataset.dfmTitle || '';
                if (type && url) dfmOpenOverlay(type, url, title);
                return;
            }
        });

        async function handleAudioToggle(btn) {
            const card = btn.closest('.file-item');
            const audio = card?.querySelector('.inline-audio audio');
            const icon = btn.querySelector('i');
            const thumb = card?.querySelector('.nonvisual-thumb.type-audio');
            if (!audio || !thumb) return;

            if (!audio.paused) {
                audio.pause();
                icon.classList.replace('bi-pause-fill', 'bi-play-fill');
                thumb.classList.remove('is-playing');
            } else {
                document.querySelectorAll('audio').forEach(a => a.pause());
                await audio.play().catch(() => {
                });
                icon.classList.replace('bi-play-fill', 'bi-pause-fill');
                thumb.classList.add('is-playing');
            }
        }

    }

    // Initialize on page and on Livewire re-render
    document.addEventListener('DOMContentLoaded', () => {
        initVideoThumbs();
        initLibraryInteractions();
    });

    document.addEventListener('livewire:init', () => {
        Livewire.hook('message.processed', () => {
            // استخدام requestAnimationFrame للتأكد من اكتمال تحديث DOM
            requestAnimationFrame(() => {
                setTimeout(() => {
                    initVideoThumbs(false);
                    initLibraryInteractions();
                }, 50);
            });
        });

        // الاستماع لحدث refreshData الذي يُطلق بعد رفع الملفات
        Livewire.on('refreshData', () => {
            // إزالة علامة thumbReady من جميع الفيديوهات
            document.querySelectorAll('video.js-video-thumb').forEach(v => {
                delete v.dataset.thumbReady;
            });

            // استخدام requestAnimationFrame + setTimeout لضمان تحديث DOM
            requestAnimationFrame(() => {
                setTimeout(() => {
                    initVideoThumbs(true);
                }, 100);
            });
        });
    });

    // مراقبة إضافة عناصر فيديو جديدة للـ DOM
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver((mutations) => {
            let hasNewVideos = false;
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) { // Element node
                        if (node.classList?.contains('js-video-thumb') ||
                            node.querySelector?.('video.js-video-thumb')) {
                            hasNewVideos = true;
                        }
                    }
                });
            });

            if (hasNewVideos) {
                setTimeout(() => initVideoThumbs(false), 100);
            }
        });

        // مراقبة التغييرات في منطقة عرض الملفات
        document.addEventListener('DOMContentLoaded', () => {
            const fileGrid = document.querySelector('.modal-body .row.g-3');
            if (fileGrid) {
                observer.observe(fileGrid, {
                    childList: true,
                    subtree: true
                });
            }
        });
    }

    // -----------------------------
    // Cropper helpers (fixed)
    // -----------------------------
    let cropper = null;
    let currentFileId = null;
    let currentImageUrl = null;

    document.addEventListener('livewire:init', function () {
        Livewire.on('editorOpened', (data) => {
            currentFileId = data.file.id;
            currentImageUrl = data.file.full_url;

            const img = document.getElementById('editorImage');
            const modalEl = document.getElementById('imageEditor');
            const modal = new bootstrap.Modal(modalEl, {backdrop: 'static', keyboard: false});
            modal.show();

            // Wait until modal is visible
            modalEl.addEventListener('shown.bs.modal', () => {
                if (!currentImageUrl) return;
                img.src = currentImageUrl;
            }, {once: true});

            // Wait until image is fully loaded before creating cropper
            img.onload = () => {
                if (cropper) cropper.destroy();
                requestAnimationFrame(() => {
                    cropper = new Cropper(img, {
                        aspectRatio: NaN,
                        viewMode: 1,
                        responsive: true,
                        rotatable: true,
                        zoomable: true,
                        scalable: true,
                        autoCrop: true
                    });
                });
            };

            // Clean up when modal closes
            modalEl.addEventListener('hidden.bs.modal', () => {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                img.src = '';
                currentImageUrl = null;
            }, {once: true});
        });
    });

    function rotateImage() {
        if (cropper) cropper.rotate(90);
    }

    function zoomIn() {
        if (cropper) cropper.zoom(0.1);
    }

    function zoomOut() {
        if (cropper) cropper.zoom(-0.1);
    }

    function flipHorizontal() {
        if (cropper) {
            const x = cropper.getData().scaleX || 1;
            cropper.scaleX(-x);
        }
    }

    function flipVertical() {
        if (cropper) {
            const y = cropper.getData().scaleY || 1;
            cropper.scaleY(-y);
        }
    }

    function resetCropper() {
        if (cropper) cropper.reset();
    }

    function setAspectRatio(r) {
        if (cropper) cropper.setAspectRatio(r);
    }

    function downloadCropped() {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas();
        const link = document.createElement('a');
        link.download = 'cropped-image.png';
        link.href = canvas.toDataURL();
        link.click();
    }

    function cropAndUpload() {
        if (!cropper) return;
        cropper.getCroppedCanvas().toBlob((blob) => {
            const formData = new FormData();
            formData.append('image', blob);
            formData.append('fileId', currentFileId);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('file-manger.crop') }}', {method: 'POST', body: formData})
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        bootstrap.Modal.getInstance(document.getElementById('imageEditor'))?.hide();
                        Livewire.dispatch('refreshData');
                    } else {
                        alert('Failed to save image');
                    }
                });
        }, 'image/png');
    }

    function cropAndUploadAsNew() {
        if (!cropper) return;
        cropper.getCroppedCanvas().toBlob((blob) => {
            const formData = new FormData();
            formData.append('image', blob);
            formData.append('fileId', currentFileId);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('file-manger.crop-as-new') }}', {method: 'POST', body: formData})
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        bootstrap.Modal.getInstance(document.getElementById('imageEditor'))?.hide();
                        Livewire.dispatch('refreshData');
                    } else {
                        alert('Failed to save image as new');
                    }
                });
        }, 'image/png');
    }


    // Compute a sensible sticky top based on modal header height + spacing
    function setSidebarStickyTop(modalEl) {
        try {
            const header = modalEl.querySelector('.modal-header');
            const body = modalEl.querySelector('.modal-body');
            // header height + modal body padding-top + small gap
            const headerH = header ? header.getBoundingClientRect().height : 0;
            const bodyStyles = body ? getComputedStyle(body) : null;
            const bodyPT = bodyStyles ? parseFloat(bodyStyles.paddingTop || '0') : 0;
            const gap = 12;
            const top = Math.max(16, Math.round(headerH + bodyPT + gap));
            modalEl.style.setProperty('--sidebar-sticky-top', `${top}px`);
        } catch (e) { /* no-op */
        }
    }

    // When modal opens or window resizes inside it, recompute
    document.addEventListener('shown.bs.modal', function (evt) {
        setSidebarStickyTop(evt.target);
    });
    window.addEventListener('resize', function () {
        document.querySelectorAll('.modal.show').forEach(setSidebarStickyTop);
    });

    // If you open this content dynamically without Bootstrap events, you can call:
    // setSidebarStickyTop(document.querySelector('#yourModalId'));
</script>
