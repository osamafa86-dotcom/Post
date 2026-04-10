@props(['share_url', 'title', 'post', 'show_meta' => false,'as'])

@if($show_meta)
    @push('meta')
        @php
            $metaTitle = $title ?? config('system.site_name');
            $metaDescription = e(Illuminate\Support\Str::limit(strip_tags($post->description ?? ''), 200)) ?: config('system.footer_description');
               $image = $post?->files?->where('model_column', 'social_media_image')?->first() ?? $post?->files?->first();
            $metaImage = ($image && $image->file && $image->file->path)
            ? file_url($image->file->path)
            : asset('assets/main/default-social.png');
            $metaUrl = $share_url ?? url()->current();
        @endphp

            <!-- Open Graph / Facebook, Messenger, LinkedIn, Telegram -->
        <meta property="og:site_name" content="{{ config('system.site_name') }}"/>
        <meta property="og:type" content="article"/>
        <meta property="og:url" content="{{ $metaUrl }}"/>
        <meta property="og:title" content="{{ $metaTitle }}"/>
        <meta property="og:description" content="{{ $metaDescription }}"/>
        <meta property="og:locale" content="ar_AR"/>
        <meta property="og:image" content="{{ $metaImage }}"/>
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="630"/>
        <meta property="og:image:alt" content="{{ $metaTitle }}"/>

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:url" content="{{ $metaUrl }}"/>
        <meta name="twitter:title" content="{{ $metaTitle }}"/>
        <meta name="twitter:description" content="{{ $metaDescription }}"/>
        <meta name="twitter:image" content="{{ $metaImage }}"/>

        <!-- LinkedIn -->
        <meta name="linkedin:card" content="summary_large_image"/>
        <meta name="linkedin:title" content="{{ $metaTitle }}"/>
        <meta name="linkedin:description" content="{{ $metaDescription }}"/>
        <meta name="linkedin:image" content="{{ $metaImage }}"/>

        <!-- Telegram -->
        <meta name="telegram:title" content="{{ $metaTitle }}"/>
        <meta name="telegram:description" content="{{ $metaDescription }}"/>
        <meta name="telegram:image" content="{{ $metaImage }}"/>
    @endpush

@endif
@if($as !== 'li')
<div class="share-article">
    <h4 class="btn-share">
        شارك
        <i class="fa-solid fa-share"></i>
    </h4>
    <div class="share-social-icons social-icons">
        <ul>
            <li>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($share_url) }}"
                   target="_blank">
                    <img src="{{ asset('assets/main/palestine_post/imgs/64px-2021_Facebook_icon.svg.png') }}"
                         alt="Facebook"/>
                </a>
            </li>
            <li>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode($share_url) }}"
                   target="_blank">
                    <img src="{{ asset('assets/main/palestine_post/imgs/x-logo.svg') }}"
                         alt="X"/>
                </a>
            </li>
            <li>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($share_url) }}"
                   target="_blank">
                    <img src="{{ asset('assets/main/palestine_post/imgs/whatsapp.png') }}"
                         alt="WhatsApp"/>
                </a>
            </li>
            <li>
                <a href="https://t.me/share/url?url={{ urlencode($share_url) }}"
                   target="_blank">
                    <img src="{{ asset('assets/main/palestine_post/imgs/64px-Telegram_logo.svg.png') }}"
                         alt="Telegram"/>
                </a>
            </li>
            <li>
                <a href="#" onclick="copyToClipboard('{{ $share_url }}'); return false;"
                   class="copy-link rounded-circle p-1" style="color: black;">
                    <i class="fa-solid fa-link fs-8"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
@elseif($as === 'li')
    <li>
        <a class="dropdown-item text-primary"
           onclick="copyToClipboard('{{ $share_url }}')">
            {{ __('messages.copy_link') }}
            <i class="bi bi-share text-primary"></i>
        </a>
    </li>
@endif
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyToClipboard(text) {
            // Create a temporary input element
            const tempInput = document.createElement("input");
            tempInput.value = text;
            document.body.appendChild(tempInput);

            // Select and copy the text
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices

            try {
                // Try using the modern Clipboard API
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        showCopySuccess();
                    }).catch(err => {
                        fallbackCopy(text);
                    });
                } else {
                    // Fallback for older browsers
                    fallbackCopy(text);
                }
            } catch (err) {
                fallbackCopy(text);
            } finally {
                // Clean up
                document.body.removeChild(tempInput);
            }
        }

        function fallbackCopy(text) {
            try {
                // Use the deprecated execCommand for fallback
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopySuccess();
                } else {
                    throw new Error('Fallback copy failed');
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: @json(__('messages.link_copying_failed')),
                    text: @json(__('messages.failed_copying_try')) +err,
                    confirmButtonText: @json(__('messages.try_again')),
                    confirmButtonColor: '#ff3a3a'
                });
            }
        }

        function showCopySuccess() {
            Swal.fire({
                icon: 'success',
                title: @json(__('messages.link_copied')),
                text: @json(__('messages.link_copied_to_clipboard')),
                confirmButtonText: @json(__('messages.confirm')),
                confirmButtonColor: '#0c6331',
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'center'
            });
        }
    </script>
@endpush
