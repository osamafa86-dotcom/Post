@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |  البحث

@endsection
@section('style')
    <style>
        /* ========================================
       Sitemap Section Container
       ======================================== */
        .sitemap-section {
            padding: 60px 20px;
            background-color: #f9f9f9;
        }


        /* ----------------------------------------
       Sitemap Header (Title & Subtitle)
       ---------------------------------------- */
        .sitemap-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .sitemap-header h2 {
            font-size: 36px;
            color: #023b56;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .sitemap-header p {
            font-size: 18px;
            color: #757575;
        }

        /* ----------------------------------------
       Sitemap Search Bar
       ---------------------------------------- */
        .sitemap-search {
            text-align: center;
            margin-bottom: 50px;
        }

        .sitemap-input-group {
            display: inline-flex;
            width: 100%;
            max-width: 500px;
            border: 1px solid #ddd;
            border-radius: 50px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .sitemap-input {
            flex: 1;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            outline: none;
            color: #333;
        }

        .sitemap-search-btn {
            background-color: #33b3c0;
            border: none;
            color: #fff;
            padding: 0 20px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .sitemap-search-btn:hover {
            background-color: #023b56;
        }

        /* ========================================
       Sitemap Card (Full Width Layout)
       ======================================== */
        .sitemap-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
            text-align: center;
        }

        .sitemap-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* Icon wrapper styled as a centered circle */
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e6f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 40px;
            color: #33b3c0;
        }

        /* Card Title */
        .sitemap-card h3 {
            font-size: 28px;
            color: #023b56;
            margin-bottom: 40px;
            font-weight: bold;
        }

        /* ----------------------------------------
       Sitemap Links with Multi-Column Layout
       ---------------------------------------- */
        .sitemap-card ul {
            list-style: disc;
            padding: 0;
            margin: 0 auto;
            /* Use CSS columns to split the list into multiple columns */
            column-count: 4;
            column-gap: 30px;
            text-align: start;
            padding-right: 2rem;
        }

        /* Adjust column-count based on available width */
        @media (max-width: 768px) {
            .sitemap-card ul {
                column-count: 1;
            }
        }

        .sitemap-card ul li {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .sitemap-card ul li a {
            color: #757575;
            transition: color 0.3s ease;
        }

        .sitemap-card ul li a:hover {
            color: #33b3c0;
        }

        /* ========================================
       Extra Resources Section
       ======================================== */
        .sitemap-extra {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        .sitemap-extra h3 {
            font-size: 28px;
            color: #023b56;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .sitemap-extra p {
            font-size: 16px;
            color: #757575;
            max-width: 700px;
            margin: 0 auto 40px;
            line-height: 1.5;
        }

        .sitemap-extra-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .sitemap-extra-links a {
            padding: 12px 24px;
            background-color: #33b3c0;
            color: #fff;
            border-radius: 25px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .sitemap-extra-links a:hover {
            background-color: #023b56;
        }

        /* ========================================
       Responsive Adjustments
       ======================================== */
        @media (max-width: 576px) {
            .sitemap-header h2 {
                font-size: 28px;
            }

            .sitemap-header p {
                font-size: 16px;
            }

            .sitemap-card {
                padding: 20px 15px;
            }

            .sitemap-card ul {
                column-count: 2;
                column-gap: 20px;
            }
        }

        /* Podcast Card Container Styling */
        .podcast-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            display: block;
        }

        /* Icon Wrapper: Displays a circular background for the microphone icon */
        .podcast-card .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e6f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 40px;
            color: #33b3c0;
        }

        /* Podcast Card Title */
        .podcast-card h3 {
            font-size: 28px;
            color: #023b56;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Container for Podcast Items */
        .podcast-items {
            display: flex;
            flex-direction: column-reverse;
            gap: 16px;
            text-align: left;
        }

        /* Each Podcast Item */
        .podcast-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        /* Remove bottom border from last podcast item */
        .podcast-item:last-child {
            border-bottom: none;
        }

        /* Podcast Thumbnail Styling */
        .podcast-thumbnail img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Podcast Details */
        .podcast-details {
            flex: 1;
        }

        /* Podcast Album Name: Clickable and Bold */
        .podcast-album {
            font-size: 20px;
            font-weight: bold;
            color: #023b56;
            margin: 0 0 4px;
        }

        /* Podcast Title (Description) */
        .podcast-title {
            font-size: 16px;
            color: #757575;
            margin: 0;
        }

        /* Video Card Container Styling */
        .video-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
        }

        /* Icon Wrapper: Displays a circular background for the play icon */
        .video-card .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e6f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 40px;
            color: #33b3c0;
        }

        /* Video Card Title */
        .video-card h3 {
            font-size: 28px;
            color: #023b56;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Container for Video Items */
        .video-items {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
        }

        /* Each Video Item */
        .video-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        /* Remove bottom border for the last video item */
        .video-item:last-child {
            border-bottom: none;
        }

        /* Video Thumbnail Styling */
        .video-thumbnail img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Video Details */
        .video-details {
            flex: 1;
        }

        /* Video Album Name: Clickable and Bold */
        .video-album {
            font-size: 20px;
            font-weight: bold;
            color: #023b56;
            margin: 0 0 4px;
        }

        /* Video Title */
        .video-title {
            font-size: 16px;
            color: #757575;
            margin: 0;
        }

    </style>
@endsection

<div>


    <!-- Sitemap Section Start -->
    <section class="sitemap-section">
        <div class="container">
            <!-- Sitemap Header -->
            <div class="sitemap-header">
                <h2> بحث عن ..</h2>
                <p>استكشف جميع أقسام موقع فلسطين بوست والمحتوى الإعلامي الشامل</p>
            </div>

            <!-- Sitemap Search Bar -->
            {{--            <div class="sitemap-search">--}}
            {{--                <div class="sitemap-input-group">--}}
            {{--                    <input type="text" class="sitemap-input" placeholder="ابحث في خريطة الموقع..."--}}
            {{--                           aria-label="Search Sitemap">--}}
            {{--                    <button class="sitemap-search-btn" type="button">--}}
            {{--                        <i class="fa-solid fa-search"></i>--}}
            {{--                    </button>--}}
            {{--                </div>--}}
            {{--            </div>--}}

            <!-- Sitemap Cards Grid (Full Width Cards) -->


            <div class="sitemap-grid">
                <!-- Card 1: Local News -->
                @if(!empty($posts) && $posts->isNotEmpty())
                    <div class="sitemap-card">
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <h3> الاخبار</h3>
                        @foreach($posts as $item)
                            <div class="news-card-result">
                                <a href="{{ route('main.palestine_post.show_post', ['id' => $item?->id, 'slug' => $item?->slug]) }}">
                                    <!-- صورة الخبر -->
                                    <img
                                        src="{{ file_url($item->files?->first()?->file?->path) }}"
                                        alt="صورة الخبر"
                                    />

                                    <!-- تفاصيل الخبر -->
                                    <div class="news-details">

                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            @if(isset($item->category?->relationable))
                                                <div class="news-category">
                                                    {{ $item->category?->relationable?->category_title ?? 'بدون تصنيف' }}

                                                </div>
                                            @endif
                                            <div class="news-meta">
                                                <i class="fa-regular fa-calendar"></i>
                                                {{ $item?->created_at?->format('Y-m-d') }}
                                            </div>
                                        </div>
                                        <div class="news-title">
                                            {{ $item?->title }}
                                        </div>
                                        <p class="news-excerpt">
                                            {{ Str::words($item?->description, 20, '...') }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach


                    </div>
                    <div>
                        {{$posts->links()}}
                    </div>
                @endif
                <!-- Card 2: Reports -->
                <!-- Podcast Section Card (Podcast Design) -->
               {{-- @if(!empty($podcasts) && $podcasts->isNotEmpty())
                    <div class="sitemap-card podcast-card">
                        <!-- Icon wrapper: A microphone icon to represent the podcast section -->
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-microphone"></i>
                        </div>
                        <!-- Card Title -->
                        <h3>البودكاست</h3>
                        <!-- Container for all podcast items -->
                        <div class="podcast-items">
                            @foreach($podcasts as $podcast)
                                <!-- Each podcast item -->
                                <div class="podcast-item">
                                    <!-- Podcast Thumbnail -->
                                    <div class="podcast-thumbnail">
                                        <img src="{{ file_url($podcast?->files()?->first()?->file?->path) }}"
                                             alt="{{ $podcast?->material_album?->name }} Podcast Thumbnail"/>
                                    </div>
                                    <!-- Podcast Details: Album name (as a clickable link) and podcast title -->
                                    <div class="podcast-details">
                                        @if(isset($podcast?->material_album?->id) && isset($podcast?->material_album?->name))
                                            <a href="{{ route('main.palestine_post.podcast', ['podcast_album_id' => $podcast?->material_album?->id, 'album_name' => $podcast?->material_album?->name]) }}">
                                                <h4 class="podcast-album">{{ $podcast?->material_album?->name }}</h4>
                                            </a>
                                        @endif
                                        <p class="podcast-title">{{ $podcast?->title }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Pagination Links for Podcasts -->
                    <div class="pagination">
                        {{$podcasts->links()}}
                    </div>
                @endif
                @if(!empty($videos) && $videos->isNotEmpty())
                    <!-- Video Section Card (Video Design) -->
                    <div class="sitemap-card video-card">
                        <!-- Icon wrapper: A play icon to represent the video section -->
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-play"></i>
                        </div>
                        <!-- Card Title -->
                        <h3>الفيديوهات</h3>
                        <!-- Container for all video items -->
                        <div class="video-items">
                            @foreach($videos as $video)
                                <!-- Each video item displays a thumbnail and details -->
                                <div class="video-item">
                                    <!-- Video Thumbnail -->
                                    <div class="video-thumbnail">
                                        <img
                                            src="{{ file_url($video?->files()?->first()?->file?->path) }}"
                                            alt="{{ $video?->material_album?->name }} Video Thumbnail"/>
                                    </div>
                                    <!-- Video Details: Album name as a clickable link and the video title -->
                                    <div class="video-details">
                                        @if(isset($video?->material_album?->id) && isset($video?->material_album?->name))
                                        <a href="{{ route('main.palestine_post.single_video', ['video_album_id' => $video?->material_album?->id, 'album_name' => $video?->material_album?->name]) }}">
                                            <h4 class="video-album">{{ $video?->material_album?->name }}</h4>
                                        </a>
                                        @endif
                                        <p class="video-title">{{ $video?->title }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Pagination Links for Videos -->
                    <div class="pagination">
                        {{$videos->links()}}
                    </div>
                @endif--}}

            </div>
            <!-- Extra Resources Section -->
        </div>
    </section>
    <!-- Sitemap Section End -->


</div>
@section('script')
@endsection
