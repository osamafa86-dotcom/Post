@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }} | {{$author?->name ?? 'الكاتب'}}
@endsection
<div>
    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">صفحة الكاتب</h4>

                    <ul class="list-breadcrumb">
                        <li>
                            الرئيسية
                        </li>
                        <li>
                            <span>|</span>
                        </li>
                        <li class="active">
                            <a href="#">{{$author?->name ?? 'الكاتب'}}</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="custom-author-card">
                    <div class="author-image-container">
                        <img src="{{file_url($author?->files()?->first()?->file?->path)}}" alt="صورة الكاتب"
                             class="author-image">
                    </div>

                    <div class="card-body">
                        <h2 class="card-title fw-bold mb-1"> {{$author?->name}}</h2>
                        <h5 class="text-muted mb-2">  {{$author?->work}} </h5>
                        <p class="card-text text-muted mb-2">
                            {{$author?->description}}
                        </p>
                        <hr>
                        <ul class="social-links info-card mb-4">
                            @foreach($author?->participant_social_media as $item)
                                <li class="social-links__item">
                                    <a href="{{ $item?->social_media_link }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="social-links__link">
                                        <img
                                            src="{{ $item->icon?->icon_path }}"
                                            alt="{{ $item?->social_media_name }} logo"
                                            class="social-links__icon"/>
                                        <span class="social-links__name">{{ $item?->social_media_name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <section class="all-works">
                    <div class="small-articles-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="title-side">
                                جميع الكتابات </h4>
                            <ul class="buttons-group-tabs">
                                @if(count($this->posts) > 0)
                                    <li data-tab="last-news" class="@if($currant_tab == 'مقالات' && count($this->posts) > 0) active @endif"
                                        wire:click="setTab('مقالات')">مقالات
                                    </li>
                                @endif
                                @if(count($this->quotes) > 0)
                                    <li data-tab="most-read" class="@if($currant_tab == 'أقتباسات' && count($this->quotes) > 0) active @endif"
                                        wire:click="setTab('أقتباسات')">أقتباسات
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane last-news"
                                 style="display: @if($currant_tab == 'مقالات') block @else none @endif">
                                <div class="row">
                                    @foreach($this->posts as $item)
                                        <div class="col-md-6">
                                            <div class="news-card">
                                                <div class="news-card-content">

                                                    <div class="news-card-text">

                                                        <div class="date-container">
                                                            <p class="date">
                                                                <i class="fa-regular fa-calendar"
                                                                   alt="Calendar icon"></i>
                                                                {{Carbon\Carbon::parse($item?->publish_date)->isoFormat('D MMMM YYYY')}}
                                                            </p>
                                                            {{--                                                            <livewire:main.palestine-post.share-post/>--}}
                                                        </div>
                                                        <h2>
                                                            {{$item?->title}}
                                                        </h2>
                                                        <p>
                                                            {{Str::limit($item?->description , 140 , '...')}}
                                                        </p>
                                                        @if(isset($item?->id) && isset($item?->slug))
                                                            <a href="{{route('main.palestine_post.show_post' , ['id'=>$item?->id , 'slug'=>$item?->slug])}}"
                                                               class=" btn btn btn-primary read- ">
                                                                اقرا المزيد
                                                                <i class="fa-solid fa-angles-left"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    {{$this->posts->links()}}
                                </div>
                            </div>

                            <div class="tab-pane most-read"
                                 style="display: @if($currant_tab == 'أقتباسات') block @else none @endif">
                                <div class="row">
                                    @foreach($this->quotes as $row)
                                        <div class="col-md-6 mb-2">
                                            <div class="Quotation-slider-content">
                                                <div class="share-container">
                                                    <i class="fa-solid fa-quote-right" alt="Quotation icon"></i>
                                                    <livewire:main.palestine-post.share-post/>
                                                </div>
                                                <h4 class="Quotation-title">{{$row?->quote_from}}</h4>
                                                <p class="Quotation-text">
                                                    {{$row?->quote_text}}
                                                </p>
                                                <div class="author-text">
                                                    <div class="author-img">
                                                        <img
                                                            src="{{file_url($row?->author?->files?->file?->path)}}"
                                                            alt="{{$row?->author?->relationable?->name}}"/>
                                                    </div>
                                                    <div class="author-details">
                                                        <p class="name">  {{$row?->author?->name}} </p>
                                                        <p class="job-title">{{$row?->author?->work}} </p>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    {{$this->quotes->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script>
        window.addEventListener('scroll-to-bottom', () => {
            window.scrollTo({top: document.body.scrollHeight, behavior: 'smooth'});
        });
    </script>
@endsection
