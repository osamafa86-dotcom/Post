@php
    use App\Models\Category;
    use App\Models\NavbarLink;
    use App\Models\Post;
@endphp

<header>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-container">
                <!-- Logo -->
                <div class="logo">
                    <a href="{{ route('main.palestine_post.index') }}">
                        <img src="{{ config('system.site_logo') ? file_url(config('system.site_logo')) : asset('assets/main/palestine_post/imgs/footer_logo.png') }}"
                             alt="شعار الموقع" loading="lazy" width="150" height="50" />
                    </a>
                </div>

                <!-- Search -->
                <div class="top-bar-input">
                @if(Route::currentRouteName() === 'main.palestine_post.index')
                    <form action="{{ route('main.palestine_post.search_page') }}" method="GET" role="search">
                        <div class="form-group">
                            <input name="search" class="search-input" placeholder="بحث عن.." aria-label="البحث في الموقع" />
                            <button type="submit" class="top-bar-btn" aria-label="بحث">
                                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <livewire:main.palestine-post.search-input />
                @endif
                </div>
                <!-- Left Icons -->
                <div class="left-icons">
                    <!-- Currency Icon & Mega Menu -->
                    <div class="icon-container">
                        <img src="{{asset('assets/main/palestine_post/imgs/dollar-circle.svg')}}" alt="أيقونة العملات"
                             loading="lazy" width="32" height="32" role="img"/>
                        <div class="curancy-mega-menu mega-menu">
                            <div class="loading-overlay">
                                <div class="loading-spinner"></div>
                            </div>
                            <div class="mega-menu-content">
                                <h2 class="currency-mega-menu-title">العملات</h2>
                                <div class="currency-mega-menu">
                                    <!-- First Slider: الدولار اليوم -->
                                    <div class="background-currency">
                                        <header class="d-flex align-items-center gap-2 justify-content-between mb-2">
                                            <div
                                                class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                <div class="dot-title red-dot"></div>
                                                <h5>الدولار اليوم</h5>
                                            </div>
                                            <div class="date-slider"></div>
                                        </header>
                                        <div class="owl-carousel slider-usd owl-theme currency-slider"></div>
                                    </div>

                                    <!-- Second Slider: اليورو اليوم -->
                                    <div class="background-currency">
                                        <header class="d-flex align-items-center gap-2 justify-content-between mb-2">
                                            <div
                                                class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                <div class="dot-title red-dot"></div>
                                                <h5>اليورو اليوم</h5>
                                            </div>
                                            <div class="date-slider"></div>
                                        </header>
                                        <div class="owl-carousel slider-eur owl-theme currency-slider"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="v-line"></div>

                    <!-- Weather Icon & Mega Menu -->
                    <div class="icon-container">
                        <img class="sun" src="{{asset('assets/main/palestine_post/imgs/sun.png')}}" alt="أيقونة الطقس"
                             width="32" height="32"/>
                        <div class="weather-mega-menu curancy-mega-menu mega-menu">
                            <div class="loading-overlay">
                                <div class="loading-spinner"></div>
                            </div>
                            <div class="mega-menu-content">
                                <h2 class="weather-mega-menu-title currency-mega-menu-title">الطقس</h2>
                                <div class="weather-sliders">
                                    <!-- Slider for Weather Data -->
                                    <div class="owl-carousel weather-slider owl-theme">
                                        <!-- سيتم تعبئة البيانات ديناميكيًا هنا -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="v-line"></div>

                    {{-- الإشعارات --}}
                    <div class="icon-container" role="button" tabindex="0" aria-label="الإشعارات">
                        <img src="{{ asset('assets/main/palestine_post/imgs/notification.svg') }}" alt="أيقونة الإشعارات"
                             loading="lazy" width="32" height="32" />
                        <div class="notifications-mega-menu mega-menu">
                            <div class="mega-menu-content">
                                <div class="notifications-container">
                                    <h2 class="notifications-header currency-mega-menu-title">اشعارات</h2>
                                    @foreach(Post::with(['category.relationable','files.file'])->latest('publish_date')->take(5)->get() as $item)
                                        <div class="notification-item">
                                            <a href="{{ route('main.palestine_post.show_post', ['id' => $item->id, 'slug' => $item->slug]) }}">
                                                <div class="d-flex gap-3">
                                                    <div class="image-noti">
                                                        <img src="{{ file_url($item?->files?->first()?->file?->path) }}" alt="{{ $item->title ?? 'صورة الإشعار' }}" loading="lazy" width="60" height="60" />
                                                    </div>
                                                    <div class="text-noti">
                                                        <div class="notification-header">
                                                            <header class="d-flex align-items-center gap-2 justify-content-between mb-2">
                                                                <div class="title-boti">
                                                                    <div class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                                        <div class="dot-title red-dot"></div>
                                                                        <h5>{{ $item->category?->relationable?->category_title }}</h5>
                                                                    </div>
                                                                </div>
                                                            </header>
                                                            <div class="notification-text">
                                                                <h6>{{ $item->title }}</h6>
                                                            </div>
                                                            <p>{{ $item->description }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End of left icons -->
            </div>
        </div>
    </div>

    <!-- Navbar -->
    @php
        $navbar = NavbarLink::whereNull('parent_id')->with('children')->orderBy('link_order')->get();
        $childNames = $navbar->flatMap(fn($link) => $link->children->pluck('link_name'))->filter()->unique()->values()->all();
        $navCategories = !empty($childNames)
            ? Category::whereIn('category_title', $childNames)->with('post_relation.post')->get()->keyBy('category_title')
            : collect();
    @endphp

    <nav class="navbar navbar-expand-lg navbar-light bg-white" aria-label="القائمة الرئيسية">
        <div class="container">

            <div class="d-flex align-items-center py-3 gap-2 full-width-small">
                <?php if($navbar->count() > 0): ?>
                <button class="navbar-toggler pt-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="فتح قائمة التنقل">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <?php endif; ?>
                    <!-- Input field for mobile screens -->

                <div class="w-100 none-in-large-screen">
                    <livewire:main.palestine-post.search-input />
                </div>
            </div>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    @foreach($navbar as $link)
                        @if($link->children->isEmpty())
                            <li class="nav-item">
                                <a class="nav-link custom-hover" href="{{ $link->link_url }}">{{ $link->link_name }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link custom-hover" href="{{ $link->link_url }}">
                                    {{ $link->link_name }}
                                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                                </a>
                                <div class="mega-menu" id="local-news-slide-menu">
                                    <div class="menu-container">
                                        @foreach($link?->children ?? [] as $child)
                                            @php
                                                $posts = ($navCategories[$child?->link_name] ?? null)?->post_relation?->sortByDesc(fn($item) => $item->post?->publish_date)->take(2);
                                            @endphp

                                            @if(isset($posts) && $posts->isNotEmpty())
                                                <div class="menu-item">
                                                    <div class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                        <div class="dot-title red-dot"></div>
                                                        <a href="{{ route('main.palestine_post.category_news', ['category_id' => $child->id, 'category_title' => $child->link_name]) }}">
                                                            <h5>{{ $child->link_name }}</h5>
                                                        </a>
                                                    </div>
                                                    <ul>
                                                        @foreach($posts as $relation)
                                                            <li>
                                                                <a href="{{ route('main.palestine_post.category_news', ['category_id' => $child->id, 'category_title' => $child->link_name]) }}">
                                                                    {{ \Illuminate\Support\Str::limit($relation->post->title ?? '', 20) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @endforeach

                                    </div>
                                </div>
                            </li>
                        @endif
                    @endforeach
{{--                        <li class="nav-item dropdown">--}}
{{--                            <a class="nav-link custom-hover" href="#">--}}
{{--                                المزيد<i class="fa-solid fa-chevron-down" alt="Dropdown arrow"></i>--}}
{{--                            </a>--}}

{{--                            <div class="simple-dropdown-menu">--}}
{{--                                <ul>--}}
{{--                                    <li><a href="#">من نحن</a></li>--}}
{{--                                    <li><a href="#">اتصل بنا</a></li>--}}
{{--                                    <li><a href="#">أرسل خبر</a></li>--}}
{{--                                    <li><a href="#">سياسة الخصوصية</a></li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}
{{--                        </li>--}}
                </ul>
            </div>
        </div>
    </nav>
</header>

@include("components.layouts.main.palestine_post.breaking-news")
