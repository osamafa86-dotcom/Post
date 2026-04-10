@php use Illuminate\Support\Str; @endphp
@section('title')

    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |  {{$category_title ?? 'جميع الأخبار'}}
@endsection
<div>
    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">جميع الأخبار</h4>

                    <ul class="list-breadcrumb">
                        <li class="active">
                            <a href="{{route('main.palestine_post.index')}}">الرئيسية</a>
                        </li>
                        <li>
                            <span>|</span>
                        </li>
                        <li>
                            {{$category_title ?? 'الكل'}}
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>
    <section class="filter-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <!-- Search Input -->
                    <div class="filter-item">
                        <input type="text" wire:model.live="search_query.search_text" class="filter-input"
                               placeholder="بحث عن النتائج"/>
                        <i class="fa fa-search"></i>
                    </div>

                    <!-- Select Category -->
                    {{--                    <div class="filter-item">--}}
                    {{--                        <select class="filter-select"  wire:model.live="search_query.category_id">--}}
                    {{--                            <option value="all">اختر التصنيف</option>--}}
                    {{--                            @foreach($this->categories as $item)--}}
                    {{--                                <option value="{{$item->id}}">{{$item->category_title}}</option>--}}
                    {{--                            @endforeach--}}
                    {{--                        </select>--}}
                    {{--                    </div>--}}

                    {{--                    <!-- Date From -->--}}
                    {{--                    <div class="filter-item">--}}
                    {{--                        <input type="date"  wire:model.live="search_query.date" class="filter-date" placeholder="التاريخ من" />--}}
                    {{--                    </div>--}}

                    {{--                    <!-- Date To -->--}}
                    {{--                    <div class="filter-item">--}}
                    {{--                        <input type="date" wire:model.live="search_query.to_date" class="filter-date" placeholder="التاريخ إلى" />--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>
    </section>
    <div class="container all-articles">
        <div class="row">
            @foreach($this->posts as $item)

                <div class="col-md-4 d-flex align-items-stretch mb-4">
                    <div class="news-card h-100">
                        <div class="news-card-content">
                            <a href="{{route('main.palestine_post.show_post' , ['id'=>$item->id , 'slug'=>$item->slug])}}">
                                <div class="news-card-img">
                                    <!-- Added alt text for SEO and accessibility -->
                                    <img src="{{file_url($item->files()?->first()?->file?->path)}}" loading="lazy"
                                         class="img-fluid"
                                         alt="Image of Israeli military officer statement"/>
                                </div>
                            </a>
                            <div class="news-card-text">
                                <div class="date-container">
                                    <div
                                        class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                        <div class="dot-title "></div>

                                        <h4 class="card-title-with-dot"><a
                                                href="{{route('main.palestine_post.category_news' , ['category_title' => $item->category?->relationable?->category_title ])}}"> {{$item->category?->relationable?->category_title}} </a>
                                        </h4>

                                    </div>
                                    <p class="date">
                                        <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                        {{Carbon\Carbon::parse($item->publish_date)->isoFormat('D MMMM YYYY')}}
                                    </p>

                                </div>
                                <a class="card-title"
                                   href="{{route('main.palestine_post.show_post' , ['id'=>$item->id , 'slug'=>$item->slug])}}">
                                    {{$item->title}}
                                </a>

                                <p>
                                    {{Str::words($item->description, 15, '...')}}
                                </p>

                                @if($item->author)
                                    <div class="article-tags">
                                        <strong> الكاتب : </strong>
                                        <a href="{{route('main.palestine_post.writer' , ['author_id' => $item->author?->relationable?->id , 'author_name' => $item->author?->relationable?->name   ])}}">
                                            {{$item->author?->relationable?->name}}

                                        </a>

                                    </div>
                                @endif
                                {{--                            <div class="share">--}}
                                {{--                                <h4>شارك</h4>--}}
                                {{--                                <img src="/imgs/share-arrow.png" alt="Share icon" loading="lazy" class="share-arrow" />--}}
                                {{--                                <div class="share-social-icons social-icons">--}}
                                {{--                                    <a href="https://facebook.com" target="_blank">--}}
                                {{--                                        <img src="./imgs/64px-2021_Facebook_icon.svg.png" alt="Facebook" />--}}
                                {{--                                    </a>--}}
                                {{--                                    <a href="https://twitter.com" target="_blank">--}}
                                {{--                                        <img src="./imgs/twitter.png" alt="Twitter" />--}}
                                {{--                                    </a>--}}
                                {{--                                    <a href="https://whatsapp.com" target="_blank">--}}
                                {{--                                        <img src="./imgs/whatsapp.png" alt="WhatsApp" />--}}
                                {{--                                    </a>--}}
                                {{--                                    <a href="https://Telegram.com" target="_blank">--}}
                                {{--                                        <img src="./imgs/64px-Telegram_logo.svg.png" alt="Telegram" />--}}
                                {{--                                    </a>--}}
                                {{--                                </div>--}}
                                {{--                            </div>--}}
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach

        </div>
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="Page navigation example">
                    <ul class="pagination-container">
                        <div>
                            {{$this->posts->links()}}
                        </div>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@section('script')
@endsection
