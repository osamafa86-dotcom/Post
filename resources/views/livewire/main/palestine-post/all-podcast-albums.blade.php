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
                                            <input wire:model.live="category_ids" type="checkbox" value="{{ $category->id }}"  class="me-2" />
                                            <span>{{ $category->category_title}}</span>
                                            <span class="badge ms-auto">({{$category->material_album_relation_count}})</span>
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
{{--                                            <input wire:model.live="album_ids" type="checkbox" value="{{ $album->id }}"  class="me-2" />--}}
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
{{--                            <select  wire:model.live="sort" class="form-select">--}}
{{--                                <option value="latest">الأحدث</option>--}}
{{--                                <option >الأكثر مشاهدة</option>--}}
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

                    @foreach($this->albums as $item)
                        <div class="podcast-item mb-4">
                            <div class="row g-0 align-items-center">
                                <!-- Image -->
                                <div class="col-md-4">
                                    <div class="podcast-img2">
                                        <a href="{{route('main.palestine_post.podcast' , ['podcast_album_id' => $item->id , 'album_name' => $item->name])}}">
                                        <img src="{{file_url($item->files()?->first()?->file?->path)}}" alt="Podcast 1"
                                             class="img-fluid rounded-start">
                                            </a>
                                    </div>
                                </div>
                                <!-- Content -->
                                <div class="col-md-8">
                                    <div class="podcast-content h-100 d-flex  justify-content-between">
                                        <div class="p-3">
                                            <h5 class="podcast-title mb-2">
                                                <a href="{{route('main.palestine_post.podcast' , ['podcast_album_id' => $item->id , 'album_name' => $item->name])}}">
                                                    {{$item->name}}
                                                </a>

                                            </h5>
                                            <p class="podcast-desc mb-3">
                                                {{$item->description}}
                                            </p>
                                            <div class="podcast-details text-muted mb-3">
                                            <span class="text-muted small me-3">
                                                <i class="fa-solid fa-calendar-days me-1"></i>   {{Carbon\Carbon::parse($item->created_at)->isoFormat('D MMMM YYYY')}}
                                            </span>
                                                {{--                                            <span class="me-3"><i class="fa-solid fa-user me-1"></i>اسم المقدم: أحمد--}}
                                                {{--                                                يوسف</span>--}}
                                                {{--                                            <span class="me-3"><i class="fa-solid fa-clock me-1"></i>المدة: 35--}}
                                                {{--                                                دقيقة</span>--}}
                                                {{--                                            <span><i class="fa-solid fa-eye me-1"></i>مشاهدات: 120</span>--}}

                                            </div>
                                        </div>
                                        <div class="p-3 d-flex justify-content-between align-items-center">

                                            <a href="{{route('main.palestine_post.podcast' , ['podcast_album_id' => $item->id , 'album_name' => $item->name])}}" class="btn btn-play">
                                                <i class="fa-solid fa-play me-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach


                </div>
            </div>
        </div>
    </section>
</div>
@section('script')
@endsection
