<!DOCTYPE html>
<html lang="ar" dir="rtl" class="js-loading">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#023b56" />
    <meta name="asset-url" content="{{ asset('') }}" />
    @php $__isHomepage = Route::currentRouteName() === 'main.palestine_post.index'; @endphp
    <meta name="author" content="{{ config('system.site_name') }}" />
    <title>@yield('title')</title>
    <meta name="title" content="@yield('title')"/>
    <meta name="description" content="@yield('description', config('system.footer_description'))"/>
    <link rel="canonical" href="{{ url()->current() }}"/>
    @if (!in_array(Route::currentRouteName(), ['main.palestine_post.show_post', 'main.palestine_post.share']))
        @php
            $metaTitle = trim($__env->yieldContent('title')) ?: config('system.site_name');
            $metaDescription = e(Illuminate\Support\Str::limit(strip_tags($__env->yieldContent('description')), 200)) ?: config('system.footer_description');
            $metaImage = config('system.favicon') ? file_url(config('system.favicon')) : asset('assets/main/palestine_post/imgs/favicon.png');
            $metaUrl = url()->current();
            $ogLocale = match (app()->getLocale()) {
                'en' => 'en_US',
                'tr' => 'tr_TR',
                default => 'ar_AR',
            };
        @endphp

            <!-- Open Graph / Facebook, Messenger, LinkedIn, Telegram -->
        <meta property="og:site_name" content="{{ config('system.site_name') }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="{{ $metaUrl }}"/>
        <meta property="og:title" content="{{ $metaTitle }}"/>
        <meta property="og:description" content="{{ $metaDescription }}"/>
        <meta property="og:locale" content="{{ $ogLocale }}"/>
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

        <!-- SEO Extras -->
        <meta name="description" content="{{ $metaDescription }}"/>
        <meta name="author" content="{{ config('system.site_name') }}"/>
    @endif

    <!-- Additional Meta Tags -->
    @stack('meta')
    <!-- Keywords -->
    <meta name="keywords" content="{{ config('system.tags')}}"/>
    <meta name="robots" content="index, follow"/>
    <!-- Structured Data: WebSite Schema -->
    <script type="application/ld+json">
        {!! json_encode([
            '@' . 'context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('system.site_name'),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    <!-- Structured Data: Organization Schema -->
    <script type="application/ld+json">
        {!! json_encode([
            '@' . 'context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('system.site_name'),
            'url' => url('/'),
            'logo' => config('system.favicon') ? file_url(config('system.favicon')) : asset('assets/main/palestine_post/imgs/favicon.png'),
            'sameAs' => [
                'https://facebook.com',
                'https://twitter.com',
                'https://instagram.com',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ config('system.favicon') ? file_url(config('system.favicon')) : asset('assets/main/palestine_post/imgs/favicon.png') }}"/>
    <!-- Styles -->
    @if(config('filesystems.default') === 's3' && config('app.aws_url'))
    <link rel="preconnect" href="{{ rtrim(config('app.aws_url'), '/') }}" crossorigin/>
    @endif
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin/>
    <link rel="dns-prefetch" href="https://unpkg.com"/>
    <link rel="dns-prefetch" href="https://connect.facebook.net"/>
    <link rel="dns-prefetch" href="https://platform.twitter.com"/>
    <link rel="dns-prefetch" href="https://www.instagram.com"/>
    <link rel="preconnect" href="{{ url('/') }}" crossorigin/>
    <link rel="preload" href="{{asset('assets/main/palestine_post/fonts/woff2/alexandria-arabic.woff2')}}" as="font" type="font/woff2" crossorigin/>
    <link rel="preload" href="{{asset('assets/main/palestine_post/fonts/woff2/cairo-arabic.woff2')}}" as="font" type="font/woff2" crossorigin/>
    <link rel="stylesheet" href="{{asset('assets/main/palestine_post/css/normalize.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/main/palestine_post/css/bootstrap.rtl.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/main/palestine_post/css/style.css?v='.filemtime(public_path('assets/main/palestine_post/css/style.css')))}}"/>
    <link rel="preload" href="{{asset('assets/main/palestine_post/css/all.min.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'"/>
    <noscript><link rel="stylesheet" href="{{asset('assets/main/palestine_post/css/all.min.css')}}"/></noscript>
    <link rel="preload" href="{{asset('assets/main/palestine_post/css/owl.carousel.min.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'"/>
    <noscript><link rel="stylesheet" href="{{asset('assets/main/palestine_post/css/owl.carousel.min.css')}}"/></noscript>
    <!-- Fonts (WOFF2, font-display:optional — no layout shift, no render block) -->
    <style>
        @font-face {
            font-family: "Alexandria-Regular";
            src: url('{{asset('assets/main/palestine_post/fonts/woff2/alexandria-arabic.woff2')}}') format('woff2');
            font-weight: 400;
            font-display: optional;
            unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFC, U+0000-00FF;
        }

        @font-face {
            font-family: "Alexandria-SemiMedium";
            src: url('{{asset('assets/main/palestine_post/fonts/woff2/alexandria-arabic.woff2')}}') format('woff2');
            font-weight: 500;
            font-display: optional;
            unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFC, U+0000-00FF;
        }

        @font-face {
            font-family: "Alexandria-SemiBold";
            src: url('{{asset('assets/main/palestine_post/fonts/woff2/alexandria-arabic.woff2')}}') format('woff2');
            font-weight: 600;
            font-display: optional;
            unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFC, U+0000-00FF;
        }

        @font-face {
            font-family: "Alexandria-Bold";
            src: url('{{asset('assets/main/palestine_post/fonts/woff2/alexandria-arabic.woff2')}}') format('woff2');
            font-weight: 700;
            font-display: optional;
            unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFC, U+0000-00FF;
        }

        @font-face {
            font-family: "cairo";
            src: url('{{asset('assets/main/palestine_post/fonts/woff2/cairo-arabic.woff2')}}') format('woff2');
            font-weight: 400;
            font-display: optional;
            unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFC, U+0000-00FF;
        }
    </style>
    <!-- Flatpickr CSS (JS loaded at bottom) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" media="print" onload="this.media='all'"/>

    {!! config('system.header_code') !!}
    @yield('style')
    @stack('style')
    <style>
        a {
            cursor: pointer !important;
        }
    </style>
</head>
<body>
<!-- Header -->
@include('components.layouts.main.palestine_post.header')
<!-- Main Content -->
<main>
    {{$slot}}
</main>
<!-- Footer -->
@include('components.layouts.main.palestine_post.footer')
@if($__isHomepage)
{{-- Homepage: hide wire:loading elements (Livewire CSS not injected) --}}
<style>[wire\:loading]{display:none!important}</style>
{{-- Homepage: prefetch only the lightweight vanilla JS --}}
<link rel="prefetch" href="{{ asset('js/homepage-lite.js') }}" as="script" />
<script>
(function(){
  var loaded=false;
  function load(){
    if(loaded)return;loaded=true;
    document.removeEventListener('pointerdown',load);
    document.removeEventListener('keydown',load);
    document.removeEventListener('scroll',onScroll);
    var s=document.createElement('script');
    s.src='{{ asset("js/homepage-lite.js") }}';
    document.head.appendChild(s);
  }
  function onScroll(){if(window.scrollY>40)load();}
  document.addEventListener('pointerdown',load,{once:true});
  document.addEventListener('keydown',load,{once:true});
  document.addEventListener('scroll',onScroll,{passive:true});
})();
</script>
@else
{{-- Other pages: full hydration chain with jQuery + Bootstrap + Livewire --}}
<link rel="prefetch" href="{{ asset('js/hydration-scheduler.js') }}" as="script" />
<link rel="prefetch" href="{{ asset('js/main-interactions.js') }}" as="script" />
<link rel="prefetch" href="{{ asset('assets/main/palestine_post/js/jquery-3.7.1.min.js') }}" as="script" />
<script>
(function(){
  var loaded=false;
  function load(){
    if(loaded)return;loaded=true;
    document.removeEventListener('pointerdown',load);
    document.removeEventListener('keydown',load);
    document.removeEventListener('scroll',onScroll);
    var s=document.createElement('script');
    s.src='{{ asset("js/hydration-scheduler.js") }}';
    s.onload=function(){
      var m=document.createElement('script');
      m.src='{{ asset("js/main-interactions.js") }}';
      document.head.appendChild(m);
    };
    document.head.appendChild(s);
  }
  function onScroll(){if(window.scrollY>40)load();}
  document.addEventListener('pointerdown',load,{once:true});
  document.addEventListener('keydown',load,{once:true});
  document.addEventListener('scroll',onScroll,{passive:true});
})();
</script>
@endif
<!-- Social SDKs (async, non-blocking — needed for embedded posts on article pages) -->
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v19.0"></script>
<script async src="https://www.instagram.com/embed.js"></script>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
{!! config('system.footer_code') !!}
@yield('script')
@stack('scripts')
<script>
document.addEventListener("DOMContentLoaded",function(){var d=document.documentElement;d.classList.remove("js-loading");d.classList.add("js-ready")});
window.addEventListener("load",function(){document.documentElement.classList.remove("js-ready")});
</script>
@if(!$__isHomepage)
<script>
(function(){
    // --- PHASE A: Static page → intercept & navigate ---
    var isStatic = !!document.querySelector('meta[name="x-static-page"]');

    if (isStatic) {
        var upgrading = false;

        function upgradeToDynamic(e) {
            if (upgrading) { e.preventDefault(); e.stopImmediatePropagation(); return; }
            upgrading = true;
            e.preventDefault();
            e.stopImmediatePropagation();

            // Save scroll position so we can restore after dynamic load
            try { sessionStorage.setItem('_static_scroll', String(window.scrollY || 0)); } catch(x){}

            // Real browser navigation — replaces history entry (no back-to-static)
            var url = new URL(location.href);
            url.searchParams.set('_upgrade', '1');
            location.replace(url.toString());
        }

        // Capture-phase listeners catch events before Livewire sees them
        document.addEventListener('click', function(e) {
            if (e.target.closest('[wire\\:click],[wire\\:submit],[wire\\:keydown]')) upgradeToDynamic(e);
        }, true);

        document.addEventListener('input', function(e) {
            if (e.target.closest('[wire\\:model],[wire\\:model\\.live],[wire\\:model\\.defer],[wire\\:model\\.debounce]')) upgradeToDynamic(e);
        }, true);

        document.addEventListener('submit', function(e) {
            if (e.target.closest('[wire\\:submit]')) upgradeToDynamic(e);
        }, true);

        return; // nothing else to do on static page
    }

    // --- PHASE B: Dynamic page after upgrade → cleanup ---
    var url = new URL(location.href);
    if (url.searchParams.has('_upgrade')) {
        // Clean _upgrade param from URL without triggering navigation
        url.searchParams.delete('_upgrade');
        history.replaceState(null, '', url.pathname + url.search + url.hash || '/');

        // Restore scroll position
        try {
            var scrollY = parseInt(sessionStorage.getItem('_static_scroll'), 10);
            sessionStorage.removeItem('_static_scroll');
            if (scrollY > 0) {
                // Wait for layout to stabilize before scrolling
                requestAnimationFrame(function(){ window.scrollTo(0, scrollY); });
            }
        } catch(x){}
    }
})();
</script>
@endif
</body>
</html>
