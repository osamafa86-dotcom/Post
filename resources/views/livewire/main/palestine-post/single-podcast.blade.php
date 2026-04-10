@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |  البودكاست

@endsection
<div>

    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">بودكاست </h4>
                    <ul class="list-breadcrumb">
                        <li>الرئيسية</li>
                        <li><span>|</span></li>
                        <li class="active">
                            <a href="#">بودكاست </a>
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
                                                   value="{{ $category->id }}" class="me-2"/>
                                            <span>{{ $category->category_title}}</span>
                                            <span class="badge ms-auto">({{$category->material_relation_count}})</span>
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
                <div class="col-lg-9 ">
                    <!-- Podcast Item #1 -->
                    <div class="row gy-4">
                        @foreach($this->podcasts as $item)
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-img-top position-relative card-podcast">
                                        <img class="img-fluid"
                                             style="max-height: 500px!important; object-fit: cover!important;"
                                             src="{{file_url($item->files?->where('model_column','image')?->first()?->file?->path)}}" alt="podcast-image">
                                        <div class="phone-img">
                                            <img
                                                src="{{asset('assets/main/palestine_post/imgs/microphone.png')}}"
                                                alt="Microphone icon"/>
                                        </div>


                                        <div class="podcast-contant ">
                                            <div
                                                class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                <div class="dot-title "></div>
                                                <h4 class="card-title-with-dot">{{$item?->category?->relationable?->category_title}} </h4>
                                            </div>
                                            <livewire:main.palestine-post.share-post :key="'podcastsSinglePodcast'.date('Y-m-d').$loop->index" :title="$item?->title" :image="file_url($item->files?->where('model_column','image')?->first()?->file?->path)"/>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <a class="card-title" href="#">
                                            {{$item?->title}}
                                        </a>
                                        <div class="audio-player">
                                            <div class="buttons-group d-flex gap-2">

                                                <!-- Forward Button -->
                                                <button class="forward audio-button">
                                                    <i class="fas fa-forward"></i>
                                                </button>

                                                <!-- Play/Pause Button -->
                                                <button class="play-pause audio-button">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                <!-- Rewind Button -->
                                                <button class="rewind audio-button">
                                                    <i class="fas fa-backward"></i>
                                                </button>
                                            </div>
                                            <div class="audio-group  d-flex gap-2 w-100">
                                                <!-- Waveform Container with audio source -->
                                                <div class="waveform-ph" data-audio="{{file_url($item?->files?->where('model_column','file')?->first()?->file?->path)}}"></div>
                                                <!-- Time Display -->
                                                <span class="time-display audio-time">0:00</span>
                                            </div>
                                            <!-- Volume Control Container (RTL order: slider then icon) -->
                                            <div class="volume-control"
                                                 style="display: inline-flex; align-items: center; gap: 5px;">

                                                <!-- Volume Slider -->
                                                <input type="range" class="volume-slider" min="0" max="1" step="0.01"
                                                       value="1"/>
                                                <!-- Volume Icon with the specified color -->
                                                <span class="volume-icon" style="color: #00a2b9;">
                      <i class="fas fa-volume-up"></i>
                    </span>
                                            </div>
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
@endsection
