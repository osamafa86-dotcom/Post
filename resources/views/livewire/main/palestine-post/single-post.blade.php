@php use Illuminate\Support\Carbon;use Illuminate\Support\Str; @endphp
@php
    $authorRelation = $post?->author?->relationable;
    $similarPostsCollection = $sameCategoryPost;
@endphp

@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }} | {{$post?->title ?? 'تفاصيل الخبر'}}
@endsection

    @section('description')
        {{ strip_tags($post?->description) }}
    @endsection

    @section('og_image')
        @if($post?->files?->first()?->file?->path)
            <meta property="og:image" content="{{ file_url($post?->files?->first()?->file?->path) }}"/>
        @endif
    @endsection

    @section('twitter_image')
        @if($post?->files?->first()?->file?->path)
            <meta name="twitter:image" content="{{ file_url($post?->files?->first()?->file?->path) }}"/>
        @endif
    @endsection

    @push('meta')
    <script type="application/ld+json">
    {!! json_encode([
        '@' . 'context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $post?->title,
        'datePublished' => $post?->publish_date ? \Carbon\Carbon::parse($post->publish_date)->toIso8601String() : now()->toIso8601String(),
        'dateModified' => $post?->updated_at ? \Carbon\Carbon::parse($post->updated_at)->toIso8601String() : ($post?->publish_date ? \Carbon\Carbon::parse($post->publish_date)->toIso8601String() : now()->toIso8601String()),
        'description' => Str::limit(strip_tags($post?->description), 200),
        'image' => $post?->files?->first()?->file?->path ? [file_url($post->files->first()->file->path)] : [],
        'author' => [
            '@type' => 'Person',
            'name' => $post?->author?->relationable?->name ?? config('system.site_name'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('system.site_name'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => config('system.favicon') ? file_url(config('system.favicon')) : asset('assets/main/palestine_post/imgs/favicon.png'),
            ],
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => url()->current(),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endpush

    @section('style')
    {{-- Facebook SDK for embedded posts --}}
    <script async defer crossorigin="anonymous"
            src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v19.0"></script>

    {{-- Twitter/X SDK for embedded tweets --}}
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

    {{-- Instagram SDK for embedded posts --}}
    <script async src="https://www.instagram.com/embed.js"></script>
        <style>
            .hero-article {
                height: 300px;
                position: relative;
                background-image: url({{file_url($post?->files?->first()?->file?->path)}});
            }

            .hero-article:before {
                --tw-bg-opacity: 0.5;
                --tw-content: "";
                background-color: rgba(0, 30, 54, 0.5);
                background-color: rgba(0, 30, 54, var(--tw-bg-opacity));
                bottom: 0;
                content: "";
                content: var(--tw-content);
                left: 0;
                pointer-events: none;
                position: absolute;
                right: 0;
                top: 0;
                z-index: 1;
            }

            .font-size-1 {
                font-size: 24px;
            }

            .font-size-2 {
                font-size: 26px;
            }

            .font-size-3 {
                font-size: 28px;
            }
        </style>

    @endsection
<div>
    <header class="article-hero" style="background-image: url('{{ file_url($post?->files?->first()?->file?->path) }}');">
        <div class="article-hero-overlay"></div>
        <div class="container article-hero-content">
            <div class="row">
                <div class="col-lg-10 mx-auto text-center">
                    @if($post->category?->relationable)
                        <a href="#" class="hero-category">{{ $post->category->relationable->category_title }}</a>
                    @endif
                    <h1 class="hero-title">{{ $post?->title }}</h1>
                    <div class="hero-meta">
                        <span><i class="fa-solid fa-calendar-days"></i> {{ \Illuminate\Support\Carbon::parse($post?->publish_date)->isoFormat('D MMMM YYYY') }}</span>
                        @if($post?->author?->relationable)
                            <span>- بقلم: <a href="{{route('main.palestine_post.writer' , ['author_id' => $post?->author?->relationable?->id , 'author_name' => $post?->author?->relationable?->name])}}">{{ $post?->author?->relationable?->name }}</a></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="article-main-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <article class="article-body-section">
                        <div class="author-box">
                            @if($post?->author?->relationable)
                                <a href="{{route('main.palestine_post.writer' , ['author_id' => $post?->author?->relationable?->id , 'author_name' => $post?->author?->relationable?->name])}}" class="author-info">
                                    <img class="author-avatar" src="{{ file_url($post?->author?->relationable->files?->file?->path) }}" alt="{{ $post?->author?->relationable?->name }}">
                                    <div>
                                        <h4 class="author-name">{{ $post?->author?->relationable?->name }}</h4>
                                        <p class="author-title">{{ $post?->author?->relationable?->work }}</p>
                                    </div>
                                </a>
                            @else
                                <div></div>
                            @endif

                            <div class="article-controls">
                                <div class="font-controls">
                                    <button class="wrapper-icon zoom-in" aria-label="تكبير الخط"><i class="fa-solid fa-plus"></i></button>
                                    <button class="wrapper-icon zoom-out" aria-label="تصغير الخط"><i class="fa-solid fa-minus"></i></button>
                                </div>
                                <x-layouts.main.palestine_post.share-post
                                    :share_url="route('main.palestine_post.share', ['id' => $post?->id])"
                                    :title="$post?->title"
                                    :show_meta="true"
                                    :post="$post"
                                    as="div"
                                />
                            </div>
                        </div>

                        <div class="description article-text-content">
                            {!! $post?->body !!}
                        </div>

                        @if(!empty($post?->tags) && count($post?->tags) > 0)
                            <div class="tags-section">
                                <h5 class="tags-title">علامات</h5>
                                <ul class="tags-list">
                                    @foreach($post?->tags as $item2)
                                        <li>
                                            <a href="{{route('main.palestine_post.tag_news' , [ 'tag_name' => $item2?->relationable?->tag_name])}}">{{$item2?->relationable?->tag_name}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </article>
                </div>

                <div class="col-lg-4">
                    <aside class="sidebar">
                        <div class="sidebar-widget">
                            <h4 class="widget-title">أخبار مشابهة</h4>
                            <div class="similar-news-list">
                                @forelse($sameCategoryPost->take(3) as $item)
                                    <div class="similar-post-item">
                                        <a href="{{ route('main.palestine_post.show_post', ['id' => $item?->id, 'slug' => $item?->slug]) }}" class="similar-post-image">
                                            <img src="{{ file_url($item?->files?->first()?->file?->path) }}" loading="lazy" alt="{{ $item?->title }}">
                                        </a>
                                        <div class="similar-post-content">
                                            <a href="{{ route('main.palestine_post.show_post', ['id' => $item?->id, 'slug' => $item?->slug]) }}" class="similar-post-title">
                                                {{ \Illuminate\Support\Str::limit($item?->title, 55) }}
                                            </a>
                                            <span class="similar-post-date">{{ \Illuminate\Support\Carbon::parse($item?->publish_date)->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p>لا توجد أخبار مشابهة.</p>
                                @endforelse
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
</div>

{{--<div>--}}
{{--    <div class="hero-article">--}}
{{--    </div>--}}
{{--    <div class="article-page">--}}
{{--        <div class="container">--}}
{{--            <div class="row">--}}
{{--                <div class="col-md-8 offset-md-2 ">--}}
{{--                    <div class="article-content">--}}
{{--                        <div class="img-article">--}}
{{--                            @if(isset($post?->files) && $post?->files?->first()?->file?->path)--}}
{{--                                <img src="{{file_url($post?->files?->first()?->file?->path)}}"--}}
{{--                                     alt="{{$post?->title}}"--}}
{{--                                     class="img-fluid">--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                        <header>--}}
{{--                            <div class="d-flex align-items-center justify-content-between">--}}
{{--                                <div class="date-article d-flex align-items-center gap-2">--}}
{{--                                    <i class="fa-solid fa-calendar-days"></i>--}}
{{--                                    {{Carbon::parse($post?->publish_date)->isoFormat('D MMMM YYYY')}}--}}
{{--                                </div>--}}

{{--                                <div class="container-left-flex">--}}

{{--                                    <div class="icons-group controls">--}}
{{--                                        <div class="wrapper-icon zoom-in">--}}
{{--                                            <i class="fa-solid fa-plus"></i>--}}
{{--                                        </div>--}}
{{--                                        <div class="wrapper-icon zoom-out">--}}
{{--                                            <i class="fa-solid fa-minus"></i>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <x-layouts.main.palestine_post.share-post--}}
{{--                                        :share_url="route('main.palestine_post.share', ['id' => $post?->id])"--}}
{{--                                        :title="$post?->title"--}}
{{--                                        :show_meta="true"--}}
{{--                                        :post="$post"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <h2 class="title-article text-container ">--}}
{{--                                {{$post?->title}}--}}
{{--                            </h2>--}}
{{--                            @if($post?->author?->relationable)--}}
{{--                                <div class="article-details  d-flex align-items-center justify-content-between">--}}
{{--                                    <a href="{{route('main.palestine_post.writer' , ['author_id' => $post?->author?->relationable?->id , 'author_name' => $post?->author?->relationable?->name])}}">--}}

{{--                                        <div class="author-details d-flex align-items-center gap-2">--}}
{{--                                            <div class="avtar-author ">--}}
{{--                                                <img--}}
{{--                                                    src="{{file_url($post?->author?->relationable->files?->first()?->file?->path)}}"--}}
{{--                                                    alt="">--}}
{{--                                            </div>--}}
{{--                                            <div class="author-text flex-column  align-items-start gap-1">--}}
{{--                                                <h4 class="name">   {{$post?->author?->relationable?->name}} </h4>--}}
{{--                                                <p class="title-job">    {{$post?->author?->relationable?->work}}</p>--}}
{{--                                            </div>--}}

{{--                                        </div>--}}
{{--                                    </a>--}}
{{--                                    <ul class="social-links info-card ">--}}
{{--                                        @foreach($post->author?->relationable->participant_social_media as $social_media)--}}
{{--                                            <li class="social-links__item">--}}
{{--                                                <a--}}
{{--                                                    href="{{ $social_media->social_media_link }}"--}}
{{--                                                    class="social-links__link"--}}
{{--                                                    target="_blank"--}}
{{--                                                    rel="noopener noreferrer"--}}
{{--                                                    aria-label="Visit {{ $social_media->social_media_name }} profile"--}}
{{--                                                >--}}
{{--                                                    <img--}}
{{--                                                        src="{{ $social_media->icon->icon_path }}"--}}
{{--                                                        alt="{{ $social_media->social_media_name }} logo"--}}
{{--                                                        class="social-links__icon"--}}
{{--                                                    />--}}
{{--                                                    <span class="social-links__name">--}}
{{--          {{ $social_media->social_media_name }}--}}
{{--        </span>--}}
{{--                                                </a>--}}
{{--                                            </li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}

{{--                                </div>--}}
{{--                            @endif--}}
{{--                        </header>--}}
{{--                        <div class="body-article">--}}
{{--                            <div class="description">--}}
{{--                                {!! $post?->body !!}--}}
{{--                            </div>--}}
{{--                            <div class="d-flex  align-items-center gap-2 flex-sms-column mt-5">--}}
{{--                                <div class="title-with-circle title-with-circle d-flex  align-items-center gap-2">--}}
{{--                                    <div class="dot-title"></div>--}}
{{--                                    <h5>علامات</h5>--}}
{{--                                </div>--}}
{{--                                @if(!empty($post?->tags) && count($post?->tags) > 0)--}}
{{--                                    <ul class="tags d-flex  align-items-center gap-2 flex-wrap">--}}
{{--                                        @foreach($post?->tags as $item2)--}}
{{--                                            <li class="tag">--}}
{{--                                                <a href="{{route('main.palestine_post.tag_news' , [ 'tag_name' => $item2?->relationable?->tag_name])}}">{{$item2?->relationable?->tag_name}}</a>--}}
{{--                                            </li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <section class="similar-news">--}}
{{--                        <h4 class="title-side">--}}
{{--                            أخبار مشابهة--}}
{{--                        </h4>--}}

{{--                        <div class="similar-news-list">--}}
{{--                            @foreach($sameCategoryPost->take(2) as $item)--}}
{{--                                 عرض أول خبرين فقط--}}
{{--                                <div class="item mb-4">--}}
{{--                                    <div class="news-card">--}}
{{--                                        <div class="news-card-content">--}}
{{--                                            <div class="news-card-img">--}}
{{--                                                <a href="{{ route('main.palestine_post.show_post', ['id' => $item?->id, 'slug' => $item?->slug]) }}">--}}
{{--                                                    <img--}}
{{--                                                        src="{{ file_url($item?->files?->first()?->file?->path) }}"--}}
{{--                                                        loading="lazy"--}}
{{--                                                        class="img-fluid"--}}
{{--                                                        alt="{{ $item?->title }}"--}}
{{--                                                    />--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                            <div class="news-card-text">--}}
{{--                                                <div class="date-container mb-2">--}}
{{--                                                    <div--}}
{{--                                                        class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">--}}
{{--                                                        <div class="dot-title"></div>--}}
{{--                                                        <h4 class="card-title-with-dot">{{ $item->category?->relationable?->category_title }}</h4>--}}
{{--                                                    </div>--}}
{{--                                                    <p class="date">--}}
{{--                                                        <i class="fa-regular fa-calendar"></i>--}}
{{--                                                        {{ \Carbon\Carbon::parse($item?->publish_date)->isoFormat('D MMMM YYYY') }}--}}
{{--                                                    </p>--}}
{{--                                                </div>--}}
{{--                                                <a--}}
{{--                                                    class="card-title d-block mb-2"--}}
{{--                                                    href="{{ route('main.palestine_post.show_post', ['id' => $item?->id, 'slug' => $item?->slug]) }}"--}}
{{--                                                >--}}
{{--                                                    {{ $item?->title }}--}}
{{--                                                </a>--}}
{{--                                                <p class="mb-3">--}}
{{--                                                    {{ Str::words(strip_tags($item?->description), 20, '...') }}--}}
{{--                                                </p>--}}
{{--                                                <x-layouts.main.palestine_post.share-post--}}
{{--                                                    :share_url="route('main.palestine_post.share', ['id' => $item?->id])"--}}
{{--                                                    :title="$item?->title"--}}
{{--                                                    :show_meta="false"--}}
{{--                                                    :post="$item"/>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    </section>--}}

{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const articlePage = document.querySelector('.article-page');
            if (!articlePage) return;

            const titles = articlePage.querySelectorAll('.title-article');
            const textContainers = articlePage.querySelectorAll('.description');

            const zoomInBtn = articlePage.querySelector('.zoom-in');
            const zoomOutBtn = articlePage.querySelector('.zoom-out');

            const BASE_DESC_PX = 18;
            const STEP_PX = 2;
            const MIN_DESC_PX = 12;
            const MAX_DESC_PX = 28;
            const TITLE_OFFSET_PX = 6;
            const LINE_HEIGHT_RATIO_D = 1.6;
            const LINE_HEIGHT_RATIO_T = 1.2;

            const MIN_STEP = Math.ceil((MIN_DESC_PX - BASE_DESC_PX) / STEP_PX);
            const MAX_STEP = Math.floor((MAX_DESC_PX - BASE_DESC_PX) / STEP_PX);
            let currentStep = 0;

            zoomInBtn?.addEventListener('click', () => {
                if (currentStep < MAX_STEP) {
                    currentStep++;
                    updateStyles();
                }
            });

            zoomOutBtn?.addEventListener('click', () => {
                if (currentStep > MIN_STEP) {
                    currentStep--;
                    updateStyles();
                }
            });

            function updateStyles() {
                const rawDesc = BASE_DESC_PX + currentStep * STEP_PX;
                const descSize = Math.min(MAX_DESC_PX, Math.max(MIN_DESC_PX, rawDesc));
                const titleSize = descSize + TITLE_OFFSET_PX;

                const descLH = Math.round(descSize * LINE_HEIGHT_RATIO_D) + 'px';
                const titleLH = Math.round(titleSize * LINE_HEIGHT_RATIO_T) + 'px';

                // style container so all descendant text inherits
                textContainers.forEach(el => {
                    el.style.fontSize = descSize + 'px';
                    el.style.lineHeight = descLH;
                });

                // style the title separately
                titles.forEach(el => {
                    el.style.fontSize = titleSize + 'px';
                    el.style.lineHeight = titleLH;
                });
            }

            updateStyles();
        });


        document.addEventListener("DOMContentLoaded", function () {
            const processEmbeds = () => {
                const containers = document.querySelectorAll(".description");
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
                                    style="width:100%; height:500px;" frameborder="0"></iframe>`;
                                }

                                // غير هيك → رجع الرابط زي ما هو بدون تعديل
                                return url;
                            }

                            // ✅ YouTube
                            if (/youtube\.com\/watch\?[^<\s"]*v=|youtu\.be\//i.test(url)) {
                                const match = url.match(/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
                                if (match) {
                                    return `
<iframe width="100%" height="500"
    src="https://www.youtube.com/embed/${match[1]}"
    title="YouTube video player"
    frameborder="0"
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

                            // ✅ ملفات PDF / DOC / DOCX خارجية فقط
                            if (/\.(pdf|doc|docx)$/i.test(url)) {
                                return `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                                style="width:100%; height:500px;" frameborder="0"></iframe>`;
                            }

                            // ❌ روابط غير مدعومة → كرابط قابل للنقر
                            return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
                        } catch (e) {
                            return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
                        }
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

            processEmbeds();
        });
    </script>
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
@endsection
