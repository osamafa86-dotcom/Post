@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')}} - {{'خريطة الموقع'}}
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
    </style>
@endsection
<div>


    <!-- Sitemap Section Start -->
    <section class="sitemap-section">
        <div class="container">
            <!-- Sitemap Header -->
            <div class="sitemap-header">
                <h2>خريطة الموقع</h2>
                <p>استكشف جميع أقسام موقع فلسطين بوست والمحتوى الإعلامي الشامل</p>
            </div>

            <!-- Sitemap Search Bar -->
            <div class="sitemap-search">
                <div class="sitemap-input-group">
                    <input wire:model.live="search_text" type="text" class="sitemap-input"
                           placeholder="ابحث في خريطة الموقع..."
                           aria-label="Search Sitemap">
                    <button class="sitemap-search-btn" type="button">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Sitemap Cards Grid (Full Width Cards) -->
            <div class="sitemap-grid">
                <!-- Card 3: Podcast -->
                <div class="sitemap-card">
                    <div class="icon-wrapper">
                        <i class="fa-solid fa-microphone"></i>
                    </div>
                    <h3>الكتاب</h3>
                    <ul>
                        @foreach($this->authors as $item)
                            <li>

                                <a href="{{route('main.palestine_post.writer' , ['author_id' => $item->id , 'author_name' => $item->name   ])}}">    {{$item->name}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Card 1: Local News -->
                <div class="sitemap-card">
                    <div class="icon-wrapper">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <h3> التصنيفات</h3>

                    <ul>
                        @foreach($this->categories as $item)
                            <li>
                                <a href="{{route('main.palestine_post.category_news' , ['category_id' =>$item?->id , 'category_title' => $item?->category_title ])}}">
                                    {{$item->category_title}}
                                </a>
                            </li>
                        @endforeach
                    </ul>


                </div>

                <!-- Card 2: Reports -->
                <div class="sitemap-card">
                    <div class="icon-wrapper">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <h3> الوسوم</h3>

                    <ul>
                        @foreach($this->tags as $item)
                            @if(!empty($item->id)&&!empty($item->tag_name))
                                <li>
                                    <a href="{{route('main.palestine_post.tag_news' , ['tag_id' =>$item?->id , 'tag_name' => $item?->tag_name ])}}">
                                        {{$item->tag_name}}
                                    </a>
                                </li>
                            @endif
{{--                            <li>--}}
{{--                                <a href="{{route('main.palestine_post.tag_news' , ['tag_id' =>$item?->tags?->id , 'tag_name' => $item?->tags?->tag_name ])}}">--}}
{{--                                    {{$item?->tags?->tag_name}}--}}
{{--                                </a>--}}
{{--                            </li>--}}

                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- Sitemap Section End -->


</div>
@section('script')
@endsection
