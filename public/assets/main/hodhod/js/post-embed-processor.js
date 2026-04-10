/**
 * Post Embed Processor - Optimized for performance
 * Handles auto-embedding of social media links and media files
 */
(function() {
    'use strict';
    
    const processEmbeds = () => {
        const containers = document.querySelectorAll(".article-body");
        const telegramSet = new Set();
        const siteHost = location.host.replace('www.', '');

        containers.forEach(container => {
            // First, convert social media iframes to proper embeds
            container.querySelectorAll('iframe').forEach(iframe => {
                const src = iframe.src || '';
                const twitterMatch = src.match(/(?:twitter\.com|x\.com)\/[^\/]+\/status\/(\d+)/i);
                if (twitterMatch) {
                    const blockquote = document.createElement('blockquote');
                    blockquote.className = 'twitter-tweet';
                    const link = document.createElement('a');
                    link.href = `https://twitter.com/i/status/${twitterMatch[1]}`;
                    blockquote.appendChild(link);
                    iframe.parentNode.replaceChild(blockquote, iframe);
                    return;
                }
                const instaMatch = src.match(/instagram\.com\/(?:[\w\-]+\/)?(p|reel|tv)\/([a-zA-Z0-9_-]+)/i);
                if (instaMatch) {
                    const blockquote = document.createElement('blockquote');
                    blockquote.className = 'instagram-media';
                    blockquote.setAttribute('data-instgrm-permalink', `https://www.instagram.com/${instaMatch[1]}/${instaMatch[2]}/`);
                    blockquote.setAttribute('data-instgrm-version', '14');
                    blockquote.style.cssText = 'width:100%; max-width:540px; margin:0 auto;';
                    iframe.parentNode.replaceChild(blockquote, iframe);
                    return;
                }
                const fbMatch = src.match(/facebook\.com/i);
                if (fbMatch && !src.includes('share/v/')) {
                    const fbDiv = document.createElement('div');
                    fbDiv.className = 'fb-post';
                    fbDiv.setAttribute('data-href', src);
                    fbDiv.setAttribute('data-show-text', 'true');
                    iframe.parentNode.replaceChild(fbDiv, iframe);
                    return;
                }
            });

            let html = container.innerHTML;

            // فك روابط داخل <a>
            html = html.replace(/<a[^>]*href="([^"]+)"[^>]*>\1<\/a>/gi, '$1');

            // التقاط كل الروابط للتعامل معها
            html = html.replace(/https?:\/\/[^\s<"]+/gi, (url, offset, fullText) => {
                // ✅ تجاهل الروابط داخل HTML tags مثل <a href="...">
                const before = fullText.substring(offset - 1, offset);
                if (/[<="'`]/.test(before)) return url;

                try {
                    const u = new URL(url);
                    const currentHost = u.host.replace('www.', '');

                    // ✅ روابط نفس الموقع
                    if (currentHost === siteHost) {
                        // لو PDF أو DOC/DOCX → نعرضه باستخدام Google Viewer
                        if (/\.(pdf|docx?|pptx?)$/i.test(url)) {
                            return `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                            style="width:100%; height:500px;" frameborder="0" loading="lazy"></iframe>`;
                        }
                        return url;
                    }

                    // ✅ YouTube
                    if (/youtube\.com\/watch\?[^<\s"]*v=|youtu\.be\//i.test(url)) {
                        const match = url.match(/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
                        if (match) {
                            return `<iframe width="100%" height="500"
    src="https://www.youtube.com/embed/${match[1]}"
    title="YouTube video player"
    frameborder="0"
    loading="lazy"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
    referrerpolicy="strict-origin-when-cross-origin"
    allowfullscreen></iframe>`;
                        }
                    }

                    // ✅ Shorts
                    if (/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/i.test(url)) {
                        const match = url.match(/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/);
                        if (match) {
                            return `<iframe width="100%" height="500"
    src="https://www.youtube.com/embed/${match[1]}"
    title="YouTube Shorts player"
    frameborder="0"
    loading="lazy"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
    referrerpolicy="strict-origin-when-cross-origin"
    allowfullscreen></iframe>`;
                        }
                    }

                    // ✅ Twitter/X
                    if (/^(https?:\/\/)?(www\.)?(twitter\.com|x\.com)\/[^\/]+\/status\/\d+/i.test(url)) {
                        const cleanUrl = url.split('?')[0].replace('x.com', 'twitter.com');
                        return `<blockquote class="twitter-tweet"><a href="${cleanUrl}"></a></blockquote>`;
                    }

                    // ✅ Instagram
                    if (/instagram\.com\/(?:[\w\-]+\/)?(p|reel|tv)\/[a-zA-Z0-9_-]+/i.test(url)) {
                        const match = url.match(/instagram\.com\/(?:[\w\-]+\/)?(p|reel|tv)\/([a-zA-Z0-9_-]+)/i);
                        if (match) {
                            return `<blockquote class="instagram-media"
                             data-instgrm-permalink="https://www.instagram.com/${match[1]}/${match[2]}/"
                             data-instgrm-version="14"
                             style="width:100%; max-width:540px;margin:0 auto;"></blockquote>`;
                        }
                    }

                    // ✅ Telegram
                    if (/t\.me\/[a-zA-Z0-9_]+\/\d+/.test(url)) {
                        const match = url.match(/t\.me\/([a-zA-Z0-9_]+)\/(\d+)/);
                        if (match) {
                            const key = `${match[1]}/${match[2]}`;
                            if (telegramSet.has(key)) return '';
                            telegramSet.add(key);
                            return `<div class="telegram-widget" data-channel="${match[1]}" data-post="${match[2]}"></div>`;
                        }
                    }

                    // ✅ Facebook
                    if (/(facebook\.com|fb\.watch)\/(?!share\/v\/)/i.test(url)) {
                        return `<div class="fb-post" data-href="${url}" data-show-text="true"></div>`;
                    }

                    // ✅ ملفات الفيديو .mp4
                    if (/\.mp4$/i.test(url)) {
                        return `
                        <video controls style="width:100%; max-width:800px; display:block; margin:20px auto;">
                            <source src="${url}" type="video/mp4">
                            متصفحك لا يدعم تشغيل الفيديو
                        </video>`;
                    }

                    // ✅ ملفات PDF / DOC / DOCX خارجية فقط
                    if (/\.(pdf|doc|docx)$/i.test(url)) {
                        return `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                        style="width:100%; height:500px;" frameborder="0" loading="lazy"></iframe>`;
                    }

                    // ❌ روابط غير مدعومة → كرابط قابل للنقر
                    return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
                } catch (e) {
                    return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
                }
            });
            
            // ---- WordPress [playlist] shortcode → HTML5 video ----
            html = html.replace(/\[playlist[^\]]*ids="(\d+)"[^\]]*\]/gi, function (shortcode, id) {
                const videoUrl = `/storage/videos/${id}.mp4`;
                return `
                <video controls style="width:100%; max-height:500px; display:block; margin:20px 0;">
                    <source src="${videoUrl}" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو
                </video>`;
            });

            // ---- WordPress [video] shortcode → HTML5 video ----
            html = html.replace(/\[video\s+([^\]]+?)\]\[\/video\]/gi, function (match, attrs) {
                let width = '100%';
                let height = 'auto';
                let mp4Url = '';
                
                // Extract attributes
                const widthMatch = attrs.match(/width="(\d+)"/);
                const heightMatch = attrs.match(/height="(\d+)"/);
                const mp4Match = attrs.match(/mp4="([^"]+)"/);
                
                if (widthMatch) width = widthMatch[1] + 'px';
                if (heightMatch) height = heightMatch[1] + 'px';
                if (mp4Match) mp4Url = mp4Match[1];
                
                if (!mp4Url) return match; // Return original if no video URL found
                
                return `
                <video controls style="width:${width}; height:${height}; max-width:100%; display:block; margin:20px auto;">
                    <source src="${mp4Url}" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو
                </video>`;
            });

            container.innerHTML = html;
        });

        // Telegram widget
        document.querySelectorAll('.telegram-widget').forEach(el => {
            const channel = el.dataset.channel;
            const postId = el.dataset.post;
            const script = document.createElement('script');
            script.setAttribute('async', '');
            script.src = 'https://telegram.org/js/telegram-widget.js?15';
            script.setAttribute('data-telegram-post', `${channel}/${postId}`);
            el.replaceWith(script);
        });

        // تفعيل التضمينات
        if (window.instgrm) window.instgrm.Embeds.process();
        if (window.FB) FB.XFBML.parse();
        if (window.twttr && window.twttr.widgets) {
            window.twttr.widgets.load();
        }
    };

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', processEmbeds);
    } else {
        processEmbeds();
    }
})();
