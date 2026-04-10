/**
 * CKEditor macOS optimization script
 * This script provides fixes for CKEditor content destruction issues on macOS
 */

document.addEventListener('DOMContentLoaded', function() {
    // Make sure CKEditor is initialized only once
    if (typeof CKEDITOR !== 'undefined') {
        // Safety check: first destroy any existing editor instance
        if (CKEDITOR.instances['ckeditor-content']) {
            CKEDITOR.instances['ckeditor-content'].destroy();
        }
        
        // Disable automatic editor creation to prevent conflicts
        CKEDITOR.disableAutoInline = true;
        
        // Fix macOS issues with the clipboard
        CKEDITOR.config.forcePasteAsPlainText = false;
        
        // Add event handlers for detecting macOS
        const isMacOS = navigator.platform.toLowerCase().indexOf('mac') >= 0;
        
        // Create the editor with macOS-optimized configuration
        const editor = CKEDITOR.replace('ckeditor-content', {
            extraPlugins: 'iframe',
            removePlugins: 'autoupdate',
            versionCheck: false,
            language: document.documentElement.lang === 'ar' ? 'ar' : 'en',
            height: 305,
            removeButtons: 'Save,Paste,PasteText,PasteFromWord,About',
            allowedContent: true,
            disallowedContent: '',
            
            // Critical settings for macOS
            startupFocus: false,
            
            // Fix for IME input on macOS
            entities: false,
            
            // Avoid custom keystrokes that might interfere with macOS
            keystrokes: [],
            
            // Critical for fixing content destruction on macOS
            enterMode: CKEDITOR.ENTER_P,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            
            // Fix cursor issues
            tabIndex: 1,
            
            // Disable automatic resizing which can trigger content loss
            autoGrow_onStartup: false,
            
            // Disable automatic spell checking which causes issues on macOS
            scayt_autoStartup: false,
            disableNativeSpellChecker: false
        });
        
        // When editor is ready, set its initial content from Livewire state
        editor.on('instanceReady', function() {
            try {
                // Get content from Livewire
                const content = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).get('state.body') || '';
                editor.setData(content);
                
                // Format any links in content
                let formattedContent = editor.getData();
                formattedContent = formattedContent.replace(/https:\/\/(www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/g, function(match, p1, p2) {
                    return `<iframe width="560" height="315" src="https://www.youtube.com/embed/${p2}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                });
                formattedContent = formattedContent.replace(/https:\/\/t\.me\/([a-zA-Z0-9_]+)/g, function(match, p1) {
                    return `<a href="https://t.me/${p1}" target="_blank">Telegram link</a>`;
                });
                formattedContent = formattedContent.replace(/https:\/\/wa\.me\/([0-9]+)/g, function(match, p1) {
                    return `<a href="https://wa.me/${p1}" target="_blank">WhatsApp link</a>`;
                });
                editor.setData(formattedContent);
            } catch (err) {
                console.error("Error setting initial editor data:", err);
            }
        });
        
        // Use a highly reliable update mechanism with debounce to prevent content loss
        let debounceTimer;
        let lastSavedContent = '';
        
        editor.on('change', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                try {
                    if (editor.status === 'ready') {
                        const data = editor.getData();
                        
                        // Only update if content has changed to reduce unnecessary Livewire calls
                        if (data !== lastSavedContent) {
                            lastSavedContent = data;
                            
                            // Update Livewire state
                            window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('state.body', data);
                        }
                    }
                } catch (err) {
                    console.error("Error updating Livewire:", err);
                }
            }, 500); // Longer debounce time helps on macOS
        });
        
        // Special handling for macOS
        if (isMacOS) {
            // Save content on blur to prevent loss
            editor.on('blur', function() {
                try {
                    const data = editor.getData();
                    if (data !== lastSavedContent) {
                        lastSavedContent = data;
                        window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('state.body', data);
                    }
                } catch (err) {
                    console.error("Error on blur:", err);
                }
            });
            
            // Add additional macOS-specific event handlers
            editor.on('contentDom', function() {
                var editable = editor.editable();
                
                // Snapshot management to prevent content loss
                editable.attachListener(editable, 'keydown', function(evt) {
                    // Create a snapshot before potentially destructive operations
                    if (evt.data.$.keyCode === 8 || evt.data.$.keyCode === 46) { // Backspace or Delete
                        editor.fire('saveSnapshot');
                    }
                });
                
                // Periodic auto-save for macOS
                const autoSaveInterval = setInterval(function() {
                    if (editor.status === 'ready') {
                        const data = editor.getData();
                        if (data !== lastSavedContent) {
                            lastSavedContent = data;
                            window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('state.body', data);
                        }
                    }
                }, 5000); // Auto-save every 5 seconds
                
                // Clean up interval on editor destroy
                editor.on('destroy', function() {
                    clearInterval(autoSaveInterval);
                });
            });
            
            // Fix for focus/blur cycles causing content loss on macOS
            editor.on('focus', function() {
                editor.fire('lockSnapshot');
            });
            
            editor.on('blur', function() {
                editor.fire('unlockSnapshot');
            });
        }
        
        // Handle media insertion
        document.addEventListener('livewire:init', function() {
            window.Livewire.on('insert-media-into-editor', (event) => {
                try {
                    const {type, url} = event[0];
                    let html = '';
                    
                    if (type === 'videos') {
                        html = `
                        <div style="text-align:center; margin: 10px 0;">
                            <video controls style="max-width: 100%;">
                                <source src="${url}" type="video/mp4">
                                متصفحك لا يدعم تشغيل الفيديو.
                            </video>
                        </div>`;
                    } else if (type === 'images') {
                        html = `<img src="${url}" style="max-width: 100%;" alt="">`;
                    } else if (type === 'audio') {
                        html = `<audio controls style="width: 100%; margin: 10px 0;"><source src="${url}" type="audio/mpeg">متصفحك لا يدعم تشغيل الصوت.</audio>`;
                    } else if (type === 'files') {
                        html = `<a href="${url}" target="_blank" style="display: block; margin: 10px 0;">${url}</a>`;
                    }
                    
                    if (editor.status === 'ready' && editor.mode === 'wysiwyg') {
                        editor.insertHtml(html);
                    } else {
                        console.warn('Editor not in WYSIWYG mode or not ready');
                    }
                } catch (err) {
                    console.error("Error inserting media:", err);
                }
            });
        });
        
        // Handle editor cleanup
        window.addEventListener('beforeunload', function() {
            if (editor) {
                try {
                    // Save content before navigating away
                    const data = editor.getData();
                    if (data !== lastSavedContent) {
                        window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('state.body', data);
                    }
                    editor.destroy();
                } catch (err) {
                    // Ignore any errors during cleanup
                }
            }
        });
    }
});
