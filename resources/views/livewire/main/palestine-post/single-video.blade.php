@php use Illuminate\Support\Str; use App\Enums\VideoTypeEnum @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |    الفيديوهات

@endsection
<div>

    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">فيديو </h4>
                    <ul class="list-breadcrumb">
                        <li>الرئيسية</li>
                        <li><span>|</span></li>
                        <li class="active">
                            <a href="#">فيديو </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="podcast-listing-section py-5">
        <div class="container">
            <div class="row">

                <!-- Filter Column (Right Side) -->
                <div class="col-lg-3  mb-4">
                    <aside class="filter-sidebar p-3">
                        <!-- Filter Title -->
                        <h5 class="mb-3"><i class="fa-solid fa-filter me-2"></i>تصفية</h5>

                        <!-- Category Filter -->
                        <div class="mb-4">
                            <h6 class="mb-2">التصنيف</h6>
                            <ul class="list-unstyled mb-0">
                                @foreach($this->categories as $category)
                                    <li class="mb-2">
                                        <label class="d-flex align-items-center">
                                            <input wire:model.live="category_ids" type="checkbox"
                                                   value="{{ $category?->id }}" class="me-2"/>
                                            <span>{{ $category?->category_title}}</span>
                                            <span class="badge ms-auto">({{$category?->material_relation_count}})</span>
                                        </label>
                                    </li>
                                @endforeach


                            </ul>
                        </div>

                        <!-- Classification Filter -->
                        {{--                        <div class="mb-4">--}}
                        {{--                            <h6 class="mb-2">الفئة</h6>--}}
                        {{--                            <ul class="list-unstyled mb-0">--}}
                        {{--                                @foreach($this->albums as $album)--}}
                        {{--                                    <li class="mb-2">--}}
                        {{--                                        <label class="d-flex align-items-center">--}}
                        {{--                                            <input wire:model.live="album_ids" type="checkbox" value="{{ $album->id }}"--}}
                        {{--                                                   class="me-2"/>--}}
                        {{--                                            <span>{{ $album->name}}</span>--}}
                        {{--                                            <span class="badge ms-auto">({{$album->album_materials_count}})</span>--}}
                        {{--                                        </label>--}}
                        {{--                                    </li>--}}
                        {{--                                @endforeach--}}

                        {{--                            </ul>--}}
                        {{--                        </div>--}}

                        <!-- Sort Filter -->
{{--                        <div class="mb-4">--}}
{{--                            <h6 class="mb-2">ترتيب حسب</h6>--}}
{{--                            <select wire:model.live="sort" class="form-select">--}}
{{--                                <option value="latest">الأحدث</option>--}}
{{--                                <option>الأكثر مشاهدة</option>--}}
{{--                                <option>الأعلى تقييماً</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}

                        {{--                        <!-- Another Filter (Status) -->--}}
                        {{--                        <div class="mb-4">--}}
                        {{--                            <h6 class="mb-2">الحالة</h6>--}}
                        {{--                            <ul class="list-unstyled mb-0">--}}
                        {{--                                <li class="mb-2">--}}
                        {{--                                    <label class="d-flex align-items-center">--}}
                        {{--                                        <input type="radio" name="status" class="me-2" />--}}
                        {{--                                        <span>مفتوح</span>--}}
                        {{--                                        <span class="badge ms-auto">(2)</span>--}}
                        {{--                                    </label>--}}
                        {{--                                </li>--}}
                        {{--                                <li class="mb-2">--}}
                        {{--                                    <label class="d-flex align-items-center">--}}
                        {{--                                        <input type="radio" name="status" class="me-2" />--}}
                        {{--                                        <span>مغلق</span>--}}
                        {{--                                        <span class="badge ms-auto">(6)</span>--}}
                        {{--                                    </label>--}}
                        {{--                                </li>--}}
                        {{--                            </ul>--}}
                        {{--                        </div>--}}
                    </aside>
                </div>


                <!-- Podcasts Column (Left Side) -->
                <div class="col-lg-9">
                    <!-- Podcast Item #1 -->


                    <div class="row">

                        @foreach($this->videos as $item)
                            <div class="col-md-6">
                                <div class="latest-news-first-video-container">
                                    <div class="card">
                                        <div class="card-img-top position-relative" style="height: auto">

                                            @if($item->id == $video_id && $isPlaying)
                                                @if($item->video_type == VideoTypeEnum::YOUTUBE->value)
                                                    <iframe width="100%" height="500"
                                                            src="{{$item->link}}"
                                                            title="YouTube video player" frameborder="0"
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                            referrerpolicy="strict-origin-when-cross-origin"
                                                            allowfullscreen></iframe>
                                                @elseif($item->video_type == VideoTypeEnum::TELEGRAM->value)
                                                    <script async src="{{$item->link}}" data-telegram-post="telegram/249" data-width="100%"></script>
                                                @else
                                                    <video width="320" height="240" controls>
                                                        <source src="{{file_url($item?->files?->where('model_column','file')?->first()?->file?->path)}}" type="video/mp4">

                                                    </video>
                                                @endif
                                            @else
                                                <!-- صورة الغلاف -->
                                                <div class="position-relative Thumbnail" style="cursor: pointer; height: 260px"
                                                     wire:click="playVideo({{$item->id}})">
                                                    <img class="img-fluid"
                                                         src="{{ file_url($item->files?->where('model_column','image')?->first()?->file?->path) }}"
                                                         alt="Video Thumbnail">
                                                    <div class="play-icon">
                                                        <img
                                                            src="{{ asset('assets/main/palestine_post/imgs/video-icon.png') }}"
                                                            alt="Play icon" loading="lazy">
                                                    </div>
                                                </div>

                                            @endif


                                            <div class="podcast-contant ">
                                                <div
                                                    class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                    <div class="dot-title "></div>
                                                    <h4 class="card-title-with-dot white-text">{{$item->category?->relationable?->category_title}} </h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="share-date">
                                                <div class="date-article d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    {{Carbon\Carbon::parse($item?->publish_date)->isoFormat('D MMMM YYYY')}}
                                                </div>
                                                <livewire:main.palestine-post.share-post />
                                            </div>
                                            <a class="card-title" href="#">
                                                {{$item?->title}}
                                            </a>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>


                </div>
            </div>
        </div>
    </section>
</div>
@section('script')
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
                let iframe = document.querySelector("iframe");
                if (iframe) {
                    let src = iframe.src;
                    iframe.src = "";
                    iframe.src = src;
                }
            });
        });
    </script>
@endsection
