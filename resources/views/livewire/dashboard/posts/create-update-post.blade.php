@php use Illuminate\Support\Carbon; @endphp
@section('title')
    {{config('system.site_name') . ' - '}}{{ $showEdit ? __('messages.posts.edit_post') : __('messages.posts.add_post') }}
@endsection
@section('style')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/47.1.0/ckeditor5.css" />
    <!-- Add if you use premium features. -->
    <!--  -->
    <style>
        .main-container {
            width: 795px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
    <style>
        .th-btn {
            cursor: pointer !important;
        }

        .select2-container {
            z-index: 900 !important;
        }

        .tagify__dropdown {
            z-index: 900 !important;
        }

        .cke_editable a {
            color: #0066cc !important;
            text-decoration: underline !important;
        }

        .cke_editable a:hover {
            color: #004499 !important;
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
                    {{!$showEdit ? __('messages.posts.add_post') : __('messages.posts.edit_post')}}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.main')}}"
                           class="text-muted text-hover-primary">{{__('messages.dashboard')}}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{route('dashboard.posts')}}"
                           class="text-muted text-hover-primary">{{__('messages.posts.posts')}}</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{!$showEdit ? __('messages.posts.add_post') : __('messages.posts.edit_post')}}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            {{--            @if ($errors->any())--}}
            {{--                <div style="position: fixed; top: 65px; left: 0; z-index: 9999;">--}}
            {{--                    @foreach ($errors->all() as $message)--}}
            {{--                        <div class="alert alert-danger alert-dismissible fade show auto-dismiss" role="alert">--}}
            {{--                            <div class="alert-text">{{ $message }}</div>--}}
            {{--                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>--}}
            {{--                        </div>--}}
            {{--                    @endforeach--}}
            {{--                </div>--}}
            {{--            @endif--}}



            <!--end::Page title-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->

    @if($translatingFromTitle)
    <div class="app-container container-fluid">
        <div class="alert alert-info d-flex align-items-center mb-5">
            <i class="bi bi-translate fs-3 me-3"></i>
            <div>
                <strong>{{ __('messages.translating_from', ['lang' => $translatingToLang]) }}</strong><br>
                <small class="text-muted">{{ __('messages.original_post') }}: {{ $translatingFromTitle }}</small>
            </div>
        </div>
    </div>
    @endif

        @include('components.modals.post_modals')
    
    <!--end::Content-->
</div>
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{--    <script src="https://cdn.ckeditor.com/ckeditor5/47.1.0/ckeditor5.umd.js"></script>--}}

    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Make sure CKEditor is initialized only once
            if (typeof CKEDITOR !== 'undefined') {
                // Add inline styles to links when created via Link dialog
                CKEDITOR.on('dialogDefinition', function (ev) {
                    var dialogName = ev.data.name;
                    var dialogDefinition = ev.data.definition;

                    if (dialogName === 'link') {
                        var onOk = dialogDefinition.onOk;
                        dialogDefinition.onOk = function (e) {
                            var result = onOk ? onOk.call(this, e) : true;
                            var editor = this.getParentEditor();
                            setTimeout(function() {
                                var links = editor.document.getElementsByTag('a');
                                for (var i = 0; i < links.count(); i++) {
                                    var link = links.getItem(i);
                                    if (!link.getAttribute('style') || link.getAttribute('style').indexOf('color') === -1) {
                                        link.setStyle('color', '#0066cc');
                                        link.setStyle('text-decoration', 'underline');
                                    }
                                }
                            }, 100);
                            return result;
                        };
                    }

                    // Intercept iframe dialog to use our custom modal for video iframes
                    if (dialogName === 'iframe') {
                        var onShow = dialogDefinition.onShow;
                        dialogDefinition.onShow = function() {
                            var editor = this.getParentEditor();
                            var selection = editor.getSelection();
                            var element = selection && selection.getStartElement();

                            // Check if editing an existing iframe with video data
                            if (element && element.getName() === 'iframe') {
                                var iframe = element.$;
                                var videoUrl = iframe.getAttribute('data-video-url');

                                // If it's a video iframe, use our custom modal instead
                                if (videoUrl) {
                                    setTimeout(function() {
                                        CKEDITOR.dialog.getCurrent().hide();
                                        window.editVideoSize(iframe);
                                    }, 100);
                                    return;
                                }
                            }

                            // Otherwise, use default iframe dialog
                            if (onShow) {
                                onShow.call(this);
                            }
                        };
                    }
                });

                // Safety check: first destroy any existing editor instance
                if (CKEDITOR.instances['post-ckeditor-content']) {
                    CKEDITOR.instances['post-ckeditor-content'].destroy();
                }

                // Disable automatic editor creation to prevent conflicts
                CKEDITOR.disableAutoInline = true;

                // Get current language
                const currentLang = '{{ Auth::user()->default_dashboard_language }}';
                const isArabic = currentLang === 'ar';

                // Register custom plugins with CKEditor-style dialogs
                CKEDITOR.plugins.add('pasteFromWord', {
                    icons: 'pasteFromWord',
                    init: function(editor) {
                        editor.addCommand('pasteFromWord', new CKEDITOR.dialogCommand('pasteFromWordDialog'));
                        editor.ui.addButton('PasteFromWord', {
                            label: isArabic ? 'لصق من Word' : 'Paste from Word',
                            command: 'pasteFromWord',
                            toolbar: 'clipboard',
                            icon: 'https://cdn-icons-png.flaticon.com/512/888/888883.png'
                        });
                    }
                });

                CKEDITOR.plugins.add('pasteFromWeb', {
                    icons: 'pasteFromWeb',
                    init: function(editor) {
                        editor.addCommand('pasteFromWeb', new CKEDITOR.dialogCommand('pasteFromWebDialog'));
                        editor.ui.addButton('PasteFromWeb', {
                            label: isArabic ? 'لصق من موقع آخر' : 'Paste from Web',
                            command: 'pasteFromWeb',
                            toolbar: 'clipboard',
                            icon: 'https://cdn-icons-png.flaticon.com/512/54/54702.png'
                        });
                    }
                });

                CKEDITOR.plugins.add('mediaLibrary', {
                    icons: 'mediaLibrary',
                    init: function(editor) {
                        editor.addCommand('openMediaLibrary', {
                            exec: function(editor) {
                                // Save the current cursor position/selection before opening modal
                                try {
                                    const selection = editor.getSelection();
                                    if (selection) {
                                        const ranges = selection.getRanges();
                                        if (ranges && ranges.length > 0) {
                                            window.savedCKEditorBookmark = ranges[0].createBookmark(true);
                                        }
                                    }
                                } catch (e) {
                                    console.log('Could not save bookmark:', e);
                                }

                                Livewire.dispatch('isMultiple', { active: true });
                                Livewire.dispatch('columnDefined', { column: 'create_update_post_ck_editor' });
                                Livewire.dispatch('showAllTypes');
                                $('#fileLibraryModal').modal('show');
                            }
                        });
                        editor.ui.addButton('MediaLibrary', {
                            label: isArabic ? 'إضافة من المكتبة' : 'Add from Library',
                            command: 'openMediaLibrary',
                            toolbar: 'insert',
                            icon: 'https://cdn-icons-png.flaticon.com/512/3767/3767084.png'
                        });
                    }
                });

                // Define CKEditor-style dialogs
                CKEDITOR.dialog.add('pasteFromWordDialog', function(editor) {
                    return {
                        title: isArabic ? 'لصق من Word' : 'Paste from Word',
                        minWidth: 650,
                        minHeight: 450,
                        contents: [{
                            id: 'tab1',
                            label: isArabic ? 'لصق المحتوى' : 'Paste Content',
                            elements: [{
                                type: 'html',
                                html: '<div style="margin-bottom: 10px; font-weight: bold; color: #333;">' +
                                      (isArabic ? 'الصق المحتوى من Word هنا (سيتم الحفاظ على التنسيق)' : 'Paste your Word content here (formatting will be preserved)') +
                                      '</div>' +
                                      '<div id="wordContentCK" contenteditable="true" style="border: 2px solid #d1d5db; border-radius: 4px; padding: 12px; width: 100%; height: 350px; overflow-y: scroll; overflow-x: auto; background: #ffffff; box-sizing: border-box; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6;"></div>'
                            }]
                        }],
                        onOk: function() {
                            const content = document.getElementById('wordContentCK').innerHTML;
                            editor.insertHtml(content);
                            document.getElementById('wordContentCK').innerHTML = '';
                        },
                        onCancel: function() {
                            document.getElementById('wordContentCK').innerHTML = '';
                        }
                    };
                });

                CKEDITOR.dialog.add('pasteFromWebDialog', function(editor) {
                    return {
                        title: isArabic ? 'لصق من موقع آخر' : 'Paste from Web',
                        minWidth: 650,
                        minHeight: 450,
                        contents: [{
                            id: 'tab1',
                            label: isArabic ? 'لصق المحتوى' : 'Paste Content',
                            elements: [{
                                type: 'html',
                                html: '<div style="margin-bottom: 10px; font-weight: bold; color: #333;">' +
                                      (isArabic ? 'الصق المحتوى من الموقع هنا (سيتم الحفاظ على التنسيق)' : 'Paste your web content here (formatting will be preserved)') +
                                      '</div>' +
                                      '<div id="webContentCK" contenteditable="true" style="border: 2px solid #d1d5db; border-radius: 4px; padding: 12px; width: 100%; height: 350px; overflow-y: scroll; overflow-x: auto; background: #ffffff; box-sizing: border-box; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6;"></div>'
                            }]
                        }],
                        onOk: function() {
                            const content = document.getElementById('webContentCK').innerHTML;
                            editor.insertHtml(content);
                            document.getElementById('webContentCK').innerHTML = '';
                        },
                        onCancel: function() {
                            document.getElementById('webContentCK').innerHTML = '';
                        }
                    };
                });

                // Disable CKEditor security notifications
                CKEDITOR.config.versionCheck = false;
                if (window.CKEDITOR && CKEDITOR.env) {
                    CKEDITOR.on('instanceReady', function(ev) {
                        ev.editor.on('notificationShow', function(evt) {
                            if (evt.data && evt.data.notification && evt.data.notification.message &&
                                evt.data.notification.message.indexOf('not secure') > -1) {
                                evt.cancel();
                            }
                        });
                    });
                }

                // Create the editor with careful configuration
                const editor = CKEDITOR.replace('post-ckeditor-content', {
                    extraPlugins: 'iframe,pasteFromWord,pasteFromWeb,mediaLibrary',
                    removePlugins: ['autoupdate', 'exportpdf', 'flash', 'forms', 'smiley', 'pagebreak', 'templates', 'print', 'save', 'newpage', 'preview', 'about'],
                    removeButtons: 'Copy,Paste,PasteText',
                    versionCheck: false,
                    language: '{{ Auth::user()->default_dashboard_language === "ar" ? "ar" : "en" }}',
                    height: 305,
                    extraAllowedContent: 'iframe[*]{*}(*)',
                    toolbar: [
                        { name: 'document', items: [ 'Source', '-', 'Undo', 'Redo' ] },
                        { name: 'clipboard', items: [ 'Cut', '-', 'PasteFromWord', 'PasteFromWeb', '-', 'SelectAll', 'RemoveFormat' ] },
                        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                        { name: 'insert', items: [ 'Image', 'Iframe', 'Table', 'HorizontalRule', 'SpecialChar', '-', 'MediaLibrary' ] },
                        '/',
                        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
                        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
                        '/',
                        { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                        { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
                    ],
                    allowedContent: {
                        iframe: {
                            attributes: 'src,width,height,frameborder,allowfullscreen,allow,data-video-url,data-video-mime,style'
                        },
                        img: {
                            attributes: 'src,alt,width,height,style'
                        },
                        video: {
                            attributes: 'src,controls,style,width,height'
                        },
                        audio: {
                            attributes: 'src,controls,style,width,height'
                        },
                        a: {
                            attributes: 'href,target,style'
                        },
                        $1: {
                            elements: CKEDITOR.dtd,
                            styles: true,
                            classes: true
                        }
                    },
                    disallowedContent: '',
                    startupFocus: false,
                    throttle: 300
                });

                // Function to update word and paragraph counts
                function updateCounts() {
                    try {
                        const html = editor.getData();
                        const wordEl = document.querySelector('[data-word-count]');
                        const paraEl = document.querySelector('[data-paragraph-count]');

                        if (!html) {
                            if (wordEl) wordEl.textContent = '0';
                            if (paraEl) paraEl.textContent = '0';
                            return;
                        }

                        // Strip HTML tags
                        const tmp = document.createElement('div');
                        tmp.innerHTML = html;
                        const text = (tmp.textContent || tmp.innerText || '').replace(/\u00A0/g, ' ').trim();

                        // Count words
                        const words = text ? text.split(/\s+/).filter(Boolean).length : 0;

                        // Count paragraphs
                        const normalized = html.replace(/<br\s*\/?>/gi, '\n').replace(/<\/(p|div|li|h[1-6])>/gi, '\n\n');
                        const tmpPara = document.createElement('div');
                        tmpPara.innerHTML = normalized;
                        const paraText = (tmpPara.textContent || tmpPara.innerText || '').trim();
                        const paragraphs = paraText ? paraText.split(/\n+/).map(s => s.trim()).filter(Boolean).length : 0;

                        if (wordEl) wordEl.textContent = words;
                        if (paraEl) paraEl.textContent = paragraphs;
                    } catch (err) {
                        console.error("Error updating counts:", err);
                    }
                }

                // When editor is ready, set its initial content from Livewire state
                editor.on('instanceReady', function () {
                    try {
                        // Set editor data with the initial Livewire state value
                        const content = @this.get('state.body') || '';
                        editor.setData(content);

                        // Format any links in content
                        let formattedContent = editor.getData();
                        formattedContent = formattedContent.replace(/https:\/\/(www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/g, function (match, p1, p2) {
                            return `<iframe width="560" height="315" src="https://www.youtube.com/embed/${p2}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                        });
                        formattedContent = formattedContent.replace(/https:\/\/t\.me\/([a-zA-Z0-9_]+)/g, function (match, p1) {
                            return `<a href="https://t.me/${p1}" target="_blank">Telegram link</a>`;
                        });
                        formattedContent = formattedContent.replace(/https:\/\/wa\.me\/([0-9]+)/g, function (match, p1) {
                            return `<a href="https://wa.me/${p1}" target="_blank">WhatsApp link</a>`;
                        });
                        editor.setData(formattedContent);

                        // Update counts on initial load
                        updateCounts();
                    } catch (err) {
                        console.error("Error setting initial editor data:", err);
                    }
                });

                // Listen for editor changes to update Livewire state and counts
                let debounceTimer;
                editor.on('change', function () {
                    try {
                        // Update counts immediately (no debounce for better UX)
                        updateCounts();

                        // Clear previous timeout
                        clearTimeout(debounceTimer);

                        // Use a debounce to prevent excessive updates on typing
                        debounceTimer = setTimeout(function () {
                            // Only update if the editor is ready
                            if (editor.status === 'ready') {
                                const data = editor.getData();
                                formattedContent = data.replace(
                                    /<iframe([^>]+src=["']https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)[^"']*["'][^>]*)><\/iframe>/gi,
                                    function (match, attrs, videoId) {
                                        const newSrc = `https://www.youtube.com/embed/${videoId}`;
                                        return `<iframe${attrs.replace(/src=["'][^"']*["']/, `src="${newSrc}"`)}></iframe>`;
                                    }
                                );
                                // Update Livewire state
                            @this.set('state.body', formattedContent)
                                ;
                            }
                        }, 300);
                    } catch (err) {
                        console.error("Error updating Livewire:", err);
                    }
                });

                // Handle media insertion with queue to prevent race conditions
                document.addEventListener('livewire:init', function () {
                    // Queue to handle multiple media insertions sequentially
                    const insertionQueue = [];
                    let isProcessing = false;

                    // Helper to get correct MIME type
                    const getMimeType = (ext, type) => {
                        const mimeTypes = {
                            // Video
                            'mp4': 'video/mp4',
                            'mov': 'video/quicktime',
                            'mkv': 'video/x-matroska',
                            'webm': 'video/webm',
                            // Audio
                            'mp3': 'audio/mpeg',
                            'ogg': 'audio/ogg',
                            'wav': 'audio/wav',
                        };
                        return mimeTypes[ext] || (type === 'videos' ? 'video/mp4' : 'audio/mpeg');
                    };

                    // Process insertion queue one at a time
                    const processQueue = async () => {
                        if (isProcessing || insertionQueue.length === 0) {
                            return;
                        }

                        isProcessing = true;
                        const {type, url, extension, id} = insertionQueue.shift();

                        console.log('🎬 Processing media insertion:', {type, url, extension, id, queueLength: insertionQueue.length});

                        let html = '';

                        if (type === 'videos') {
                            const mimeType = getMimeType(extension, type);

                            // Insert video directly with default dimensions
                            html = `<p><iframe src="${url}" width="640px" height="360px" style="max-width: 100%;" frameborder="0" allowfullscreen allow="autoplay; encrypted-media" data-video-url="${url}" data-video-mime="${mimeType}"></iframe></p>`;
                            console.log('✅ Generated video iframe HTML');
                        } else if (type === 'images') {
                            html = `<img src="${url}" style="max-width: 100%;" alt="">`;
                            console.log('✅ Generated image HTML');
                        } else if (type === 'audio') {
                            const mimeType = getMimeType(extension, type);
                            html = `
                            <div style="margin: 10px 0;">
                                <audio controls src="${url}" style="width: 100%;">
                                    <source src="${url}" type="${mimeType}">
                                    متصفحك لا يدعم تشغيل الصوت.
                                </audio>
                            </div>`;
                            console.log('✅ Generated audio HTML with MIME type:', mimeType);
                        } else if (type === 'files') {
                            html = `<a href="${url}" target="_blank" style="display: block; margin: 10px 0;">${url}</a>`;
                            console.log('✅ Generated file link HTML');
                        }

                        console.log('📝 Generated HTML:', html);

                        // Wait for editor to be ready and insert
                        const insertWhenReady = () => {
                            return new Promise((resolve) => {
                                const attemptInsert = () => {
                                    if (editor && editor.status === 'ready' && editor.mode === 'wysiwyg') {
                                        try {
                                            // Focus the editor first
                                            editor.focus();

                                            // Try to restore the saved bookmark/cursor position
                                            let insertedAtSavedPosition = false;

                                            if (window.savedCKEditorBookmark) {
                                                try {
                                                    const range = editor.createRange();
                                                    range.moveToBookmark(window.savedCKEditorBookmark);
                                                    editor.getSelection().selectRanges([range]);
                                                    insertedAtSavedPosition = true;
                                                    console.log('✅ Restored cursor to saved position');
                                                } catch (bookmarkErr) {
                                                    console.log('⚠️ Could not restore bookmark, will insert at current position:', bookmarkErr);
                                                }
                                            }

                                            // Insert the HTML at the current cursor position (saved or current)
                                            editor.insertHtml(html);

                                            // Clear the saved bookmark after insertion
                                            window.savedCKEditorBookmark = null;

                                            console.log('✅ HTML inserted into editor successfully', insertedAtSavedPosition ? 'at saved position' : 'at current position');

                                            // Add extra delay to ensure editor processes the insertion fully
                                            setTimeout(resolve, 200);
                                        } catch (insertErr) {
                                            console.error('❌ Error during HTML insertion:', insertErr);
                                            resolve();
                                        }
                                    } else {
                                        console.log('⏳ Editor not ready yet, waiting...', {
                                            status: editor?.status,
                                            mode: editor?.mode
                                        });
                                        setTimeout(attemptInsert, 100);
                                    }
                                };
                                attemptInsert();
                            });
                        };

                        try {
                            await insertWhenReady();
                        } catch (err) {
                            console.error("❌ Error inserting media:", err);
                        } finally {
                            isProcessing = false;
                            // Process next item in queue
                            if (insertionQueue.length > 0) {
                                processQueue();
                            }
                        }
                    };

                    // Listen for media insertion events and add to queue
                    Livewire.on('insert-media-into-editor', (event) => {
                        try {
                            const {type, url, extension, id} = event[0];
                            console.log('📥 Queuing media insertion:', {type, url, extension, id});

                            // Add to queue
                            insertionQueue.push({type, url, extension, id});

                            // Start processing if not already running
                            processQueue();
                        } catch (err) {
                            console.error("❌ Error queuing media:", err);
                        }
                    });
                });

                // Handle editor cleanup
                window.addEventListener('beforeunload', function () {
                    if (editor) {
                        try {
                            editor.destroy();
                        } catch (err) {
                            // Ignore any errors during cleanup
                        }
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('livewire:init', () => {

                const statusClasses = {
                    'success': 'btn-success',
                    'error': 'btn-danger',
                    'warning': 'btn-warning',
                    'info': 'btn-info'
                };

                function handleSwal(eventData) {
                    const data = Array.isArray(eventData) ? eventData[0] : eventData;
                    const buttonClass = statusClasses[data?.status] || 'btn-primary';

                    Swal.fire({
                        title: data?.title || 'Default Title',
                        text: data?.message || 'Default message',
                        icon: data?.status || 'info',
                        confirmButtonText: 'OK',
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                        customClass: {
                            confirmButton: 'btn ' + buttonClass
                        }
                    }).then(() => {
                        if (data?.status === 'success') {
                            window.location.href = "/dashboard/posts"; // إعادة توجيه
                        }
                    });
                }

                Livewire.on('post-create', handleSwal);
                Livewire.on('post-edit', handleSwal);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('postCreated', function (event) {
                let status = event.detail.status;
                let message = event.detail.message;
                let redirectUrl = '';

                if (status === "تم النشر") {
                    redirectUrl = '/dashboard/posts';
                    message = 'تم حفظ المنشور بنجاح';
                } else if (status === "معلقة") {
                    redirectUrl = '/dashboard/pending_actions';
                    message = 'تم حفظ المنشور بنجاح';
                } else if (status === "مسودة") {
                    redirectUrl = '/dashboard/posts/draft_posts';
                    message = 'تم حفظ بالمسودة بنجاح';
                }

                if (redirectUrl !== '') {
                    Swal.fire({
                        title: '{{__("messages.alert_message.success")}}',
                        text: message,
                        icon: 'success',
                        confirmButtonText: '{{__("messages.alert_message.done")}}',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then(() => {
                        window.location.href = redirectUrl;
                    });
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('livewire:init', function () {
                var input = document.querySelector('#tags');
                var tagify = new Tagify(input, {
                    whitelist: @json($this->tags), // Pass the tags array to Tagify
                    dropdown: {
                        maxItems: 5,           // Show 20 suggestions at most
                        classname: "tags-look", // Custom dropdown class
                        enabled: 0,             // Show suggestions immediately
                        closeOnSelect: false    // Keep the dropdown open after selecting a suggestion
                    }
                });

                tagify.addTags(@json($state['tags'] ?? []));

                tagify.on('add', function (e) {
                @this.set('state.tags', tagify.value.map(tag => tag.value).join(','))
                    ;
                });

                tagify.on('remove', function (e) {
                @this.set('state.tags', tagify.value.map(tag => tag.value).join(','))
                    ;

                });
                Livewire.on('addTagToInput', tag => {
                    tagify.addTags([tag]);
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('livewire:init', function () {
                const select2Elements = [
                    {selector: '#special_file_id', wireModel: 'state.special_file_id'},
                    {selector: '#type_id', wireModel: 'state.type_id'},
                    {selector: '#category_id', wireModel: 'state.category_id'},
                    {selector: '#author_id', wireModel: 'state.author_id'},
                    {selector: '#publisher_id', wireModel: 'state.publisher_id'},
                    {selector: '#resource_id', wireModel: 'state.resource_id'},

                ];

                function initializeSelect2(selector, selectedValue) {
                    let options = {
                        placeholder: $(selector).data('placeholder'),
                        val: selectedValue || '',
                    };

                    // If it's the 'type_id' selector, ensure it's single select
                    if (selector === '#special_file_id') {
                        options.multiple = false;
                    }

                    $(selector).select2(options);
                }

                function setupChangeEvent(selector, wireModel) {
                    $(selector).on('change', function (e) {
                        var data = $(this).val();
                    @this.set(wireModel, data)
                        ;
                    });
                }

                // Initialize Select2 for all elements
                select2Elements.forEach(function (element) {
                    setTimeout(function () {
                        initializeSelect2(element.selector, @this.get(element.wireModel)); // Pass the initial value to select2
                        setupChangeEvent(element.selector, element.wireModel);
                    }, 100)
                });

                // Reinitialize Select2 after Livewire updates
                Livewire.hook('message.processed', (message, component) => {
                    select2Elements.forEach(function (element) {
                        initializeSelect2(element.selector, @this.get(element.wireModel)); // Update with live data
                    });
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('open-preview-tab', event => {
                window.open(event.detail.url, '_blank');
            });
        });

        // Function to edit video size on double click (for iframe)
        window.editVideoSize = function(iframeElement) {
            console.log('🎬 editVideoSize called with:', iframeElement);

            const url = iframeElement.getAttribute('data-video-url') || iframeElement.getAttribute('src');
            const mimeType = iframeElement.getAttribute('data-video-mime') || '';

            // Get current dimensions
            const currentWidth = iframeElement.getAttribute('width') || iframeElement.style.width || '640px';
            const currentHeight = iframeElement.getAttribute('height') || iframeElement.style.height || '360px';

            console.log('🎬 Editing video iframe:', {
                url,
                mimeType,
                currentWidth,
                currentHeight,
                'data-video-url': iframeElement.getAttribute('data-video-url'),
                'src': iframeElement.getAttribute('src')
            });

            // Set values in modal
            document.getElementById('videoWidth').value = currentWidth;
            document.getElementById('videoHeight').value = currentHeight;
            document.getElementById('pendingVideoUrl').value = url;
            document.getElementById('videoUrlDisplay').value = url;
            document.getElementById('pendingVideoMimeType').value = mimeType;

            console.log('✅ Modal fields updated, showing modal...');

            // Store reference to the iframe element for updating
            window.currentEditingVideo = iframeElement;

            // Show modal
            $('#videoDimensionsModal').modal('show');
        };

        // Add double-click listener to iframes in CKEditor
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.CKEDITOR && CKEDITOR.instances['post-ckeditor-content']) {
                    const editor = CKEDITOR.instances['post-ckeditor-content'];
                    editor.on('contentDom', function() {
                        const editable = editor.editable();
                        editable.attachListener(editable, 'dblclick', function(evt) {
                            const element = evt.data.getTarget();
                            if (element.getName() === 'iframe') {
                                evt.data.preventDefault();
                                window.editVideoSize(element.$);
                            }
                        });
                    });
                }
            }, 1000);
        });
    </script>
@endsection
