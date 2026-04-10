@php use App\Models\SocialMedia; @endphp
@section('title')
    {{ config('system.site_name') ? config('system.site_name') : __('messages.launches.' . config('app.launch')) }} - {{ $state['title'] ?? 'تفاصيل الخبر' }}
@endsection
@section('style')
    <style>
        .hero-article {
            height: 300px;
            position: relative;
            background-image: url({{ file_url($state['image_name'] ?? '') }});
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

        body {
            background: #f2f5f8;
        }
    </style>
@endsection
<div>
    @php
        $authorId = $state['author_id'][0] ?? null;
        $categoryId = $state['category_id'][0] ?? null;
        $author = $authorId ? \App\Models\Participant::where('id', $authorId)
                    ->with(['files.file', 'participant_social_media'])
                    ->first() : null;
        $category = $categoryId ? \App\Models\Category::where('id', $categoryId)->first() : null;
    @endphp
    <main class="single-article container">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav ">
            <ol class="breadcrumb ">
                <li class="breadcrumb-item">
                    <a href="{{ route('main.'.config('app.launch').'.index') }}" class="breadcrumb-link">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">

                    <a href="{{ config('app.launch') === 'tayqan'
            ? route('main.tayqan.category_claim', ['category' => $category?->id])
            : route('main.'.config('app.launch').'.all_posts', ['type' => 'category', 'id' => $category?->id]) }}"
                       class="breadcrumb-link">
                        {{ $category?->category_title ?? 'تصنيف' }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $state['title'] ?? 'تفاصيل الخبر' }}</li>
            </ol>
        </nav>
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-12">
                <article class="post-content ">
                    <!-- Post Header -->
                    <h1 class="post-title mb-3">{{ $state['title'] ?? '' }}</h1>
                    @if(!empty($state['publish_date']))
                        <span class="date d-flex align-items-center gap-1">
                          <i class="fa-regular fa-calendar-days"></i>
                          {{ \Carbon\Carbon::parse($state['publish_date'])->isoFormat('D MMMM YYYY') }}
                        </span>
                    @endif
                    <!-- Featured Image -->
                    @if(!empty($state['image_name']))
                        <img src="{{ file_url($state['image_name']) }}"
                             alt="{{ $state['image_alt'] ?? $state['title'] ?? '' }}"
                             class="post-image mb-4"/>
                    @endif
                </article>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-9 mb-5">
                <div class="container-post-body">
                    <style>
                        .post-body, .post-body * {
                            font-family: Greta, Arial, serif !important
                        }

                        .post-body * {
                            text-align: start !important;
                        }
                    </style>

                    <div class="post-body">
                        {!! $state['body'] ?? '' !!}
                    </div>

                    <!-- Tags -->
                    @if(!empty($state['tags']) && is_array($state['tags']))
                        <div class="post-tags">
                            @foreach($state['tags'] as $tag)
                                <a>#{{ $tag }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <!-- Sidebar -->
            <aside class="post-sidebar col-lg-3">
                <div class="contact-widget mb-2 widget">
                    <header class="widget-title">تواصل معنا</header>
                    <div class="social-media d-flex gap-2">
                        @php $social_media = SocialMedia::with('icon')->where('position',\App\Enums\LinkPosition::AllPlaces->value)->orderByDesc('order')->get(); @endphp
                        @foreach($social_media as $social)
                            <a class="sm-icon" title="{{ $social?->name }}" href="{{ $social?->url }}" target="_blank">
                                <img width="20" src="{{ $social?->icon?->icon_path }}"
                                     alt="social_icon">
                            </a>
                        @endforeach
                    </div>
                </div>
                <!-- Author Info Widget -->
                @if($author)
                    <div class="author-widget mb-2 widget">
                        <div class="contact-widget mb-2">
                            <header class="widget-title">الكاتب</header>
                        </div>
                        <a
                            href="{{ config('app.launch') === 'tayqan'
            ? route('main.tayqan.writer', ['id' => $author?->id])
            : route('main.'.config('app.launch').'.writer', ['author_id' => $author?->id, 'name' => $author?->name]) }}">
                        <img
                                src="{{ file_url(optional($author->files->first()->file)->path) }}"
                                alt="{{ $author?->name }}" class="author-widget-img mb-3"/>
                            <h3 class="author-name mb-1">{{ $author?->name }} </h3>
                            <p class="author-role mb-2">{{ $author?->work }}</p>
                            <p class="author-bio mb-3">
                                {{ $author?->description }}
                            </p>
                        </a>
                        <div class="author-social d-flex justify-content-center gap-2">
                            @forelse($author->participant_social_media as $link)
                                <a href="{{ $link?->social_media_link }}" title="{{ $link?->social_media_name }}"
                                   target="_blank">
                                    <img width="40" src="{{ $link?->icon?->icon_path }}" alt="image"/></a>
                            @empty
                            @endforelse
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </main>
</div>
