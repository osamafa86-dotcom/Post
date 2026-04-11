@php use App\Enums\ImageSizeTypeEnum;use App\Enums\MaterialTypeEnum;use App\Enums\VideoTypeEnum;use App\Models\Material;use App\Models\Post;use App\Models\Quote;use App\Models\SocialMedia;use App\Models\SpecialFile;use Illuminate\Support\Carbon;use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |  {{config('system.light_site_name')}}

@endsection
@section('style')
    <style>
        .latest-news-first-video-container iframe,
        .latest-news-first-video-container script {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* 🔴 ستايل يوتيوب */
        .youtube-style {
            width: 100%;
            height: 460px;
            background: black;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .youtube-style iframe {
            width: 100% !important;
            height: 100% !important;
        }

        /* 🔵 ستايل تليجرام */
        .telegram-style {
            width: 100%;
            max-width: 400px; /* يمكن التعديل حسب الحاجة */
            margin: auto;
            overflow: hidden;
        }

        .telegram-style iframe {
            width: 100% !important;
            height: 460px !important;
            border: none !important;
        }

        /* 🟢 ستايل الفيديوهات المحلية */
        .local-style {
            width: 100%;
            height: 460px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: black;
        }

        .local-style video {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
        }
    </style>
@endsection
<div>
    <section class="hero-news">
        <img
            src="@if(!empty($main_post?->files?->where('model_column', 'image')?->first()?->file?->path)) {{file_url($main_post?->files?->where('model_column', 'image')?->first()?->file?->path)}} @else {{asset('assets/main/palestine_post/images/5Qe3h.webp')}} @endif"
            fetchpriority="high"
            width="1200" height="350"
            alt="{{$main_post?->image_alt ?? $main_post?->title ?? 'صورة الخبر الرئيسي'}}"/>
        <div class="card-news-hero">
            <div class="container" style="height: 100%">
                <div class="landing-content" style="height: 100%">
                    @if(!empty($main_post))
                        <div class="landing-right-text">
                            <h3 class="post-title text-white text-lg sm:text-22 font-bold sm:leading-10 line-clamp-3 hover:text-primary">
                                <a class="post-link text-current transition-colors"
                                   href="{{route('main.palestine_post.show_post', ['id'=>$main_post?->id, 'slug'=>$main_post?->slug])}}">
                                    <span
                                        style="color: #f4516c;">{{$main_post?->category?->relationable?->category_title ?? 'تقرير'}}</span>
                                    {{$main_post?->title ?? 'عنوان'}}
                                </a>
                            </h3>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <a class="text-white text-sm hover:text-primary m-0"
                                   href="{{route('main.palestine_post.category_news', [ 'category_title' => $main_post?->category?->relationable?->category_title ?? 'تصنيف'])}}"
                                   style="font-size: .875rem;">
                                    {{$main_post?->category?->relationable?->category_title ?? 'تصنيف'}}
                                </a>
                                <span
                                    class="text-white text-sm">{{ Carbon::parse($main_post->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY') }}</span>
                            </div>
                        </div>
                    @endif
                    @if($this->sub_posts->isNotEmpty())
                        <div class="landing-left-text">
                            @foreach($this->sub_posts as $post)
                                <a class="left-text-card"
                                   href="{{route('main.palestine_post.show_post', ['id'=>$post?->id, 'slug'=>$post?->slug])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div
                                            class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                            <h5 class="left-span">{{$post?->category?->relationable?->category_title ?? 'تصنيف'}}</h5>
                                        </div>
                                    </div>
                                    <h2 class="mt-2">
                                        {{Str::limit($post?->title, 100) ?? 'عنوان'}}
                                    </h2>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
    @include("components.layouts.main.palestine_post.latest-news")
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-9">
                <section class="right-section">
                    <div class="hide-right-section">
                        @php
                            $sidePostBuffer = ''; // لتخزين الأخبار مؤقتًا
                            $sidePostCount = 0; // عداد الأخبار
                            $pairedMaterials = []; // لتتبع الأخبار التي تم استخدامها
                            $shownSpecialFiles = [];
                        @endphp
                        @php
                            // تجميع المواد التي تحتوي على special_file
                            $groupedSpecialFiles = $this->materials
                                ->filter(fn($m) => $m->sortable?->special_file !== null)
                                ->groupBy(fn($m) => $m->sortable?->special_file?->id);
                            // استخراج مادة واحدة من كل مجموعة special_file لترتيب العرض
                            $specialFileMaterials = $groupedSpecialFiles->map(function ($group) {
                                // نحصل على المادة ذات أعلى order_number لتمثيل المجموعة
                                return $group->sortByDesc(fn($m) => $m->order_number)->first();
                            });

                            // استخراج المواد التي لا تحتوي على special_file
                            $regularMaterials = $this->materials
                                ->filter(fn($m) => $m->sortable?->special_file === null);

                            // دمج المواد وترتيبها حسب order_number تنازليًا
                            $mergedMaterials = $specialFileMaterials->merge($regularMaterials)
                                ->sortByDesc(fn($m) => $m->order_number);
                        @endphp
                        @foreach($mergedMaterials as $material)
                            @if($material->sortable?->special_file?->relationable?->id && !in_array($material->sortable?->special_file?->relationable?->id, $shownSpecialFiles))
                                @php
                                    $specialFile = $material->sortable->special_file->relationable;
                                    $materials = $groupedSpecialFiles[$material->sortable?->special_file?->id] ?? collect();
                                @endphp
                                {{-- ✅ عرض مجموعة special_file --}}
                                <div class="fixed-accordion ">
                                    <div class="fb-accordion-item fb-active">
                                        <div class="fb-accordion-title">
                                            <i class="fa-solid fa-square"></i>
                                            {{ $specialFile?->file_name }}
                                        </div>
                                        <div class="fb-accordion-content">
                                            <div class="image">
                                                <img src="{{ file_url($specialFile?->files?->file?->path) }}"
                                                     loading="lazy" alt="special_file_image">
                                            </div>
                                            <div class="text">
                                                <h3>{{ $specialFile?->file_name }}</h3>
                                                <p>{{ $specialFile?->file_description }}</p>
                                                <ul>
                                                    @foreach($materials as $innerMaterial)
                                                        @if(isset($innerMaterial?->sortable?->id) && isset($innerMaterial?->sortable?->slug))
                                                            <li>
                                                                <a href="{{ route('main.palestine_post.show_post', ['id' => $innerMaterial->sortable->id, 'slug' => $innerMaterial->sortable->slug]) }}">
                                                                    {{ $innerMaterial->sortable->title ?? 'عنوان غير متوفر' }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($material->sortable_type == Post::class)
                                @if($material?->sortable?->image_size == ImageSizeTypeEnum::LARGE_IMAGE->value)
                                    <div class="news-card " style="width: 100%; ">
                                        <div class="news-card-content">
                                            <div class="news-card-img">
                                                <img
                                                    src="{{$material?->sortable?->files?->first()?->file?->path ? file_url($material?->sortable?->files?->first()?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.material.large_image') . '/dddddd/000000/'}}"
                                                    loading="lazy"
                                                    alt="{{$material?->sortable?->image_alt ?? 'وصف الصورة'}}"/>
                                            </div>
                                            <div class="news-card-text line-horizontal-blue">
                                                <div class="date-container">
                                                    <div
                                                        class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                        <div class="dot-title"></div>
                                                        @php
                                                            $categoryTitle = $material?->sortable?->category?->relationable?->category_title;
                                                        @endphp

                                                        @if($categoryTitle)
                                                            <a href="{{ route('main.palestine_post.category_news', ['category_title' => $categoryTitle]) }}">
                                                                <h4 class="card-title-with-dot">{{ $categoryTitle }}</h4>
                                                            </a>
                                                        @else
                                                            <h4 class="card-title-with-dot">تصنيف</h4>
                                                        @endif
                                                    </div>
                                                    <p class="date">
                                                        <i class="fa-regular fa-calendar"></i>
                                                        {{Carbon::parse($material->sortable->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY')}}
                                                    </p>
                                                </div>
                                                <a class="card-title"
                                                   href="{{route('main.palestine_post.show_post',['id'=>$material?->sortable?->id,'slug'=>$material?->sortable?->slug])}}">
                                                    <div>
                                                        @if($material?->sortable?->type?->type_name)
                                                            <span
                                                                class="text-danger m-0 p-0">{{$material?->sortable?->type?->type_name . ' '}}</span>
                                                        @endif
                                                        {{$material?->sortable?->title ?? 'عنوان'}}
                                                    </div>
                                                </a>
                                                <p>{{$material->sortable->description ?? 'وصف'}}</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($material?->sortable?->image_size ==ImageSizeTypeEnum::MID_IMAGE->value)
                                    <div class="local-news-menu">
                                        <div class="local-news card-hrizontal-en">
                                            <div class="local-news-text line-blue-to-img">
                                                <div class="date-container">
                                                    <div
                                                        class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                        <div class="dot-title"></div>
                                                        @if($material?->sortable?->category?->relationable)
                                                            <a href="{{route('main.palestine_post.category_news' , ['category_title' => $material?->sortable?->category?->relationable?->category_title ])}}">
                                                                <h4 class="card-title-with-dot">{{$material?->sortable?->category?->relationable?->category_title ?? 'تصنيف'}}</h4>
                                                            </a>
                                                        @else
                                                            <h4 class="card-title-with-dot">تصنيف</h4>
                                                        @endif
                                                    </div>
                                                    <div class="date-article d-flex align-items-center gap-2">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                        {{Carbon::parse($material?->sortable?->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY')}}
                                                    </div>
                                                </div>
                                                <a class="card-title "
                                                   href="{{route('main.palestine_post.show_post',['id'=>$material?->sortable?->id,'slug'=>$material?->sortable?->slug])}}">
                                                    <div>
                                                        @if($material?->sortable?->type?->type_name)
                                                            <span
                                                                class="text-danger m-0 p-0">{{$material?->sortable?->type?->type_name . ' '}}</span>
                                                        @endif
                                                        {{$material?->sortable?->title ?? 'عنوان'}}
                                                    </div>
                                                </a>
                                                <p>{{$material?->sortable?->description ?? 'وصف'}}</p>
                                            </div>
                                            <div class="local-news-img">
                                                <img
                                                    src="{{$material?->sortable?->files[0]?->file?->path ? file_url($material?->sortable?->files[0]?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.material.large_image') . '/dddddd/000000/'}}"
                                                    loading="lazy"
                                                    alt="{{$material?->sortable?->image_alt ?? 'وصف الصورة'}}"/>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($material?->sortable?->image_size == ImageSizeTypeEnum::SMALL_IMAGE->value)
                                    <div class="local-news-menu">
                                        <div class="local-news-second-section">
                                            <div class="local-news-second-section-text">
                                                <div class="date-container">
                                                    <div
                                                        class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                        <div class="dot-title"></div>
                                                        @if(isset($material?->sortable?->category?->relationable))
                                                            <a href="{{route('main.palestine_post.category_news' , [ 'category_title' => $material?->sortable?->category?->relationable?->category_title ])}}">
                                                                <h4 class="card-title-with-dot">{{$material?->sortable?->category?->relationable?->category_title ?? 'تصنيف'}}</h4>
                                                            </a>
                                                        @else
                                                            <h4 class="card-title-with-dot">تصنيف</h4>
                                                        @endif
                                                    </div>
                                                    <div class="date-article d-flex align-items-center gap-2">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                        {{Carbon::parse($material?->sortable?->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY')}}
                                                    </div>
                                                </div>
                                                <a class="card-title"
                                                   href="{{route('main.palestine_post.show_post',['id'=>$material?->sortable?->id,'slug'=>$material?->sortable?->slug])}}">
                                                    <div>
                                                        @if($material?->sortable?->type?->type_name)
                                                            <span
                                                                class="text-danger m-0 p-0">{{$material?->sortable?->type?->type_name . ' '}}</span>
                                                        @endif
                                                        {{$material?->sortable?->title ?? 'عنوان'}}
                                                    </div>
                                                </a>
                                                <p>{{$material?->sortable?->description ?? 'وصف'}}</p>
                                            </div>
                                            <div class="local-news-second-section-img">
                                                <img
                                                    src="{{$material?->sortable?->files[0]?->file?->path ? file_url($material?->sortable?->files[0]?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.material.large_image') . '/dddddd/000000/'}}"
                                                    loading="lazy"
                                                    alt="{{$material?->sortable?->image_alt ?? 'وصف الصورة'}}"/>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($material?->sortable?->image_size == ImageSizeTypeEnum::SIDE_POST->value)
                                    @if(!in_array($material->id, $pairedMaterials))
                                        @php
                                            // إيجاد أقدم خبر لم يتم استخدامه بعد
                                            $oldMaterial = $this->materials->whereNotIn('id', $pairedMaterials)
                                                                            ->where('id', '!=', $material->id)
                                                                            ->where('sortable.image_size', ImageSizeTypeEnum::SIDE_POST->value)
                                                                            ->first();

                                            // إضافة الخبر الجديد إلى قائمة المستخدمين
                                            $pairedMaterials[] = $material->id;
                                            if ($oldMaterial) {
                                                $pairedMaterials[] = $oldMaterial->id; // إضافة الأقدم حتى لا يتم استخدامه مرة أخرى
                                            }
                                        @endphp
                                        <div class="local-news-double-card">
                                            {{-- الخبر الجديد على اليمين --}}
                                            <div class="news-card" style="width: 50%;">
                                                <div class="news-card-content">
                                                    <div class="news-card-img">
                                                        <img
                                                            src="{{$material->sortable?->files[0]?->file?->path ? file_url($material->sortable?->files[0]?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.material.large_image') . '/dddddd/000000/'}}"
                                                            loading="lazy"
                                                            alt="{{$material->sortable->image_alt ?? 'وصف الصورة'}}"/>
                                                    </div>
                                                    <div class="news-card-text">
                                                        <div class="date-container">
                                                            <div
                                                                class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                                <div class="dot-title"></div>
                                                                @php
                                                                    $categoryTitle = $material?->sortable?->category?->relationable?->category_title;
                                                                @endphp

                                                                @if($categoryTitle)
                                                                    <a href="{{ route('main.palestine_post.category_news', ['category_title' => $categoryTitle]) }}">
                                                                        <h4 class="card-title-with-dot">{{ $categoryTitle }}</h4>
                                                                    </a>
                                                                @else
                                                                    <h4 class="card-title-with-dot">تصنيف</h4>
                                                                @endif
                                                            </div>
                                                            <p class="date">
                                                                <i class="fa-regular fa-calendar"></i>
                                                                {{Carbon::parse($material->sortable->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY')}}
                                                            </p>
                                                        </div>
                                                        <a class="card-title"
                                                           href="{{route('main.palestine_post.show_post',['id'=>$material?->sortable?->id,'slug'=>$material?->sortable?->slug])}}">
                                                            <div>
                                                                @if($material?->sortable?->type?->type_name)
                                                                    <span
                                                                        class="text-danger m-0 p-0">{{$material?->sortable?->type?->type_name . ' '}}</span>
                                                                @endif
                                                                {{$material?->sortable?->title ?? 'عنوان'}}
                                                            </div>
                                                        </a>
                                                        <p>{{$material->sortable->description ?? 'وصف'}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- إذا وجد خبر قديم، يتم عرضه على اليسار --}}
                                            @if($oldMaterial)
                                                <div class="news-card" style="width: 50%;">
                                                    <div class="news-card-content">
                                                        <div class="news-card-img">
                                                            <img
                                                                src="{{$oldMaterial?->sortable?->files[0]?->file?->path ? file_url($oldMaterial?->sortable?->files[0]?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.material.large_image') . '/dddddd/000000/'}}"
                                                                loading="lazy"
                                                                alt="{{$oldMaterial?->sortable?->image_alt ?? 'وصف الصورة'}}"/>
                                                        </div>
                                                        <div class="news-card-text">
                                                            <div class="date-container">
                                                                <div
                                                                    class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                                    <div class="dot-title"></div>
                                                                    @if($oldMaterial?->sortable?->category)
                                                                        <a href="{{route('main.palestine_post.category_news' , [ 'category_title' => $oldMaterial?->sortable?->category?->relationable?->category_title ])}}">
                                                                            <h4 class="card-title-with-dot">{{$oldMaterial?->sortable?->category?->relationable?->category_title ?? 'تصنيف'}}</h4>
                                                                        </a>
                                                                    @else
                                                                        <h4 class="card-title-with-dot">تصنيف</h4>
                                                                    @endif
                                                                </div>
                                                                <p class="date">
                                                                    <i class="fa-regular fa-calendar"></i>
                                                                    {{Carbon::parse($oldMaterial?->sortable?->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY')}}
                                                                </p>
                                                            </div>
                                                            <a class="card-title"
                                                               href="{{route('main.palestine_post.show_post',['id'=>$oldMaterial?->sortable?->id,'slug'=>$oldMaterial?->sortable?->slug])}}">
                                                                <div>
                                                                    @if($oldMaterial?->sortable?->type?->type_name)
                                                                        <span
                                                                            class="text-danger m-0 p-0">{{$oldMaterial?->sortable?->type?->type_name . ' '}}</span>
                                                                    @endif
                                                                    {{$oldMaterial?->sortable?->title ?? 'عنوان'}}
                                                                </div>
                                                            </a>
                                                            <p>{{$oldMaterial->sortable->description ?? 'وصف'}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @elseif($material?->sortable?->image_size == ImageSizeTypeEnum::COVER_ARTICLE->value)
                                    <div class="article-section">
                                        <div class="article-header">
                                            <div
                                                class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                <div class="dot-title "></div>
                                                @php
                                                    $categoryTitle = $material?->sortable?->category?->relationable?->category_title;
                                                @endphp

                                                @if($categoryTitle)
                                                    <a href="{{ route('main.palestine_post.category_news', ['category_title' => $categoryTitle]) }}">
                                                        <h4 class="card-title-with-dot">{{ $categoryTitle }}</h4>
                                                    </a>
                                                @else
                                                    <h4 class="card-title-with-dot">تصنيف</h4>
                                                @endif
                                            </div>
                                            <div class="date-container">
                                                <div class="date-article d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    {{Carbon::parse($material?->sortable?->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY')}}
                                                </div>

                                            </div>
                                        </div>
                                        <div class="author-text">
                                            <div class="author-img">
                                                <img
                                                    src="{{$material?->sortable?->author?->relationable?->files?->file?->path ? file_url($material?->sortable?->author?->relationable?->files?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.material.avater_author') . '/dddddd/000000/'}}"
                                                    loading="lazy" alt="author_image"/>
                                            </div>
                                            <div class="author-details">
                                                @php
                                                    $author = $material?->sortable?->author;
                                                @endphp

                                                @if($author && $author?->relationable?->id && $author?->relationable?->name)
                                                    <a href="{{ route('main.palestine_post.writer', ['author_id' => $author?->relationable?->id, 'author_name' => Str::slug($author?->relationable?->name)]) }}">
                                                        <p class="name">{{ $author?->relationable?->name }}</p>
                                                    </a>
                                                @else
                                                    <p class="name">كاتب</p>
                                                @endif
                                                <p class="job-title">{{$material?->sortable?->author?->relationable?->work}}</p>
                                            </div>
                                        </div>

                                        <a class="card-title article-title"
                                           href="{{route('main.palestine_post.show_post',['id'=>$material?->sortable?->id,'slug'=>$material?->sortable?->slug])}}">
                                            <h3>{{$material?->sortable?->title ?? 'عنوان'}} </h3>
                                        </a>
                                        <p class="article-text">
                                            {{$material?->sortable?->description ?? 'وصف'}}
                                        </p>
                                        {{--                                        <livewire:main.palestine-post.share-post :key="'material'.date('Y-m-d').$loop->index" :share_url="route('main.palestine_post.show_post',['id'=>$material?->sortable?->id,'slug'=>$material?->sortable?->slug])" :title="$material?->sortable?->title" :description="$material?->sortable?->slug" :image="file_url($material?->sortable?->author?->files?->file?->path)"/>--}}
                                    </div>
                                @endif
                            @elseif($material->sortable_type == Material::class)
                                @if($material?->sortable?->type == MaterialTypeEnum::PODCAST->value)
                                    <!-- podcast start بودكاست -->
                                    <div class="podcasts-cards ">
                                        <div class="card">
                                            <div class="card-img-top position-relative">
                                                <img
                                                     src="{{$material?->sortable?->files[0]->file?->path ? file_url($material?->sortable?->files?->first()?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.podcast_image') . '/dddddd/000000/'}}"
                                                     loading="lazy" alt="podcast-image">

                                                <div class="phone-img">
                                                    <img class=""
                                                         src="{{asset('assets/main/palestine_post/imgs/microphone.png')}}"
                                                         alt="Microphone icon"/>
                                                </div>

                                                <div class="podcast-contant">
                                                    <div
                                                        class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                        <div class="dot-title "></div>
                                                        <h4 class="card-title-with-dot white-text">بودكاست </h4>
                                                    </div>


                                                    {{--                                                    <livewire:main.palestine-post.share-post :key="'materialSecondOne'.date('Y-m-d').$loop->index" :title="$material?->sortable?->title" :share_url="route('main.palestine_post.podcast',['podcast_album_id'=>$material?->sortable?->id,'album_name'=>$material?->sortable?->title])" :image="file_url($material?->sortable?->files()?->where('model_column','image')?->first()?->file?->path)"/>--}}
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <a class="card-title">
                                                    {{$material?->sortable?->title}}
                                                </a>
                                                <div class="audio-player">
                                                    <div class="buttons-group d-flex gap-2">

                                                        <!-- Forward Button -->
                                                        <button class="forward audio-button" aria-label="تقديم">
                                                            <i class="fas fa-forward" aria-hidden="true"></i>
                                                        </button>

                                                        <!-- Play/Pause Button -->
                                                        <button class="play-pause audio-button" aria-label="تشغيل">
                                                            <i class="fas fa-play" aria-hidden="true"></i>
                                                        </button>
                                                        <!-- Rewind Button -->
                                                        <button class="rewind audio-button" aria-label="ترجيع">
                                                            <i class="fas fa-backward" aria-hidden="true"></i>
                                                        </button>

                                                    </div>
                                                    <div class="audio-group  d-flex gap-2 w-100">
                                                        <!-- Waveform Container with audio source -->
                                                        <div class="waveform-ph"
                                                             data-audio="{{file_url($material?->sortable?->files?->where('model_column','file')?->first()?->file?->path)}}"></div>

                                                        <!-- Time Display -->
                                                        <span class="time-display audio-time">0:00</span>
                                                    </div>

                                                    <!-- Volume Control Container (RTL order: slider then icon) -->
                                                    <div class="volume-control"
                                                         style="display: inline-flex; align-items: center; gap: 5px;">

                                                        <!-- Volume Slider -->
                                                        <input type="range" class="volume-slider" min="0" max="1"
                                                               step="0.01"
                                                               value="1" aria-label="مستوى الصوت"/>
                                                        <!-- Volume Icon with the specified color -->
                                                        <span class="volume-icon" style="color: #00a2b9;">
                                                        <i class="fas fa-volume-up"></i>
                                                      </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- podcast end بودكاست -->
                                @elseif($material?->sortable?->type == MaterialTypeEnum::VIDEO->value)
                                    <div class="latest-news-first-video-container">
                                        <div class="card">
                                            <div class="card-img-top position-relative">
                                                <!-- صورة الفيديو المصغرة + أيقونة التشغيل -->
                                                <div class="video-thumbnail-wrapper" onclick="playVideo(this)"
                                                     style="cursor: pointer;">
                                                    <img src="{{ file_url($material?->sortable?->files?->where('model_column','image')?->first()?->file?->path) }}"
                                                         loading="lazy" alt="Video Thumbnail"/>

                                                    <div
                                                        class="play-icon position-absolute top-50 start-50 translate-middle">
                                                        <img
                                                            src="{{ asset('assets/main/palestine_post/imgs/video-icon.png') }}"
                                                            alt="Play Icon"/>
                                                    </div>
                                                </div>
                                                @if($material?->sortable?->video_type == VideoTypeEnum::YOUTUBE->value)
                                                    <iframe class="video-embed d-none" width="100%" height="500"
                                                            src="{{ $material?->sortable?->link }}"
                                                            title="YouTube video player" frameborder="0"
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                            referrerpolicy="strict-origin-when-cross-origin"
                                                            allowfullscreen></iframe>
                                                @else
                                                    <video class="video-embed d-none" width="100%" height="100%"
                                                           controls>
                                                        <source
                                                            src="{{ file_url($material?->sortable?->files?->where('model_column','file')?->first()?->file?->path) }}"
                                                            type="video/mp4">
                                                    </video>
                                                @endif
                                            </div>

                                            <div class="podcast-contant">
                                                <div
                                                    class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                    <div class="dot-title"></div>
                                                    <h4 class="card-title-with-dot white-text">فيديو</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="share-date">
                                                <div class="date-article d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    {{ Carbon::parse($material?->sortable?->created_at ?? now())->isoFormat('hh:mm A DD MMMM YYYY') }}
                                                </div>
                                            </div>
                                            <a class="card-title">{{ $material?->sortable?->title }}</a>
                                        </div>
                                    </div>
                                @endif
                            @elseif($material->sortable_type == Quote::class)
                                <div class="quote-section">
                                    <div class="logo-unicef">
                                        <img
                                            src="{{$material?->sortable?->author?->files?->file?->path ? file_url($material?->sortable?->author?->files?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.podcast_image') . '/dddddd/000000/'}}"
                                            loading="lazy" alt="author_image"/>
                                    </div>
                                    <div class="quote-header">
                                        <span class="author-quote">
                                          <i class="fa-solid fa-quote-right"></i>
                                        </span>
                                        {{--                                        <livewire:main.palestine-post.share-post :key="'materialFourthOne'.date('Y-m-d').$loop->index" :title="$material?->sortable?->quote_text" :image="file_url($material?->sortable?->author?->files?->file?->path)"/>--}}
                                    </div>
                                    <div
                                        class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                        <div class="dot-title "></div>
                                        <h4 class="card-title-with-dot">اقتباس </h4>
                                    </div>
                                    <p class="quote-text">
                                        {{$material?->sortable?->quote_text}}
                                    </p>
                                    <div class="author">
                                        <div class="author-text">
                                            <div class="author-img">
                                                <img
                                                    src="{{$material?->sortable?->author?->files?->file?->path ? file_url($material?->sortable?->author?->files?->file?->path) : 'https://dummyimage.com/' . config('features.image_sizes.material.avater_author') . '/dddddd/000000/'}}"
                                                    loading="lazy" alt="author_image"/>
                                            </div>
                                            <div class="author-details">
                                                @php
                                                    $author = $material?->sortable?->author;
                                                @endphp

                                                @if($author && $author?->id && $author?->name)
                                                    <a href="{{ route('main.palestine_post.writer', ['author_id' => $author?->id, 'author_name' => Str::slug($author?->name)]) }}">
                                                        <p class="name">{{ $author?->name }}</p>
                                                    </a>
                                                @else
                                                    <p class="name">كاتب</p>
                                                @endif
                                                <p class="job-title">{{$author?->work}}</p>
                                            </div>

                                        </div>
                                        <div class="author-quote">
                                            <i class="fa-solid fa-quote-left"></i>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if($number_of_materials < $total_of_materials)
                        <div class="view-more" id="view-more-container">
                            <a href="/?count={{ $number_of_materials + 5 }}" id="load-more-btn"
                               class="btn btn-outline-primary read-more"
                               data-offset="{{ $number_of_materials }}" data-limit="5">
                                عرض المزيد
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                    @endif
                </section>
            </div>
            <div class="col-md-3">

                <div class="latest-news-section2 ">
                    <div
                        class="title-with-circle title-with-circle-small d-flex align-items-center justify-content-center gap-2 border-bottom-blue">
                        <div class="dot-title "></div>
                        <h3 class="section-title"> آخر الأخبار</h3>
                    </div>
                    @if($this->last_posts->isNotEmpty())
                        <div class="news-list">
                            @foreach($this->last_posts as $key => $post)
                                <div class="news-item">
                                    <span class="news-number">0{{$key + 1}}</span>
                                    <a href="{{route('main.palestine_post.show_post',['id'=>$post?->id,'slug'=>$post?->slug])}}">{{Str::limit($post?->title) ?? 'عنوان'}}</a>
                                    <div class="date-article d-flex align-items-center gap-2 justify-content-end">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        {{ Carbon::parse($post?->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @if($this->opportunitiesAndGrants->isNotEmpty())
                    <div class="opportunities-section my-5">
                        <h4 class="title-side">الفرص والمنح</h4>


                        <div class="opportunities-list mt-3">
                            @forelse($this->opportunitiesAndGrants as $item)
                                <div class="opportunity-item">
                                    <img src="{{file_url($item?->files?->file?->path)}}" alt="صورة منحة"
                                         loading="lazy" class="opportunity-image">

                                    <div class="opportunity-info">
                                        <h5 class="opportunity-title">{{$item?->name}}</h5>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-primary register-btn opportunity-btn"
                                            data-name="{{ $item->name }}"
                                            data-description="{{ e($item->description ?? $item->opportunity ?? '') }}"
                                            data-image="{{ file_url($item->files?->file?->path) }}">
                                        سجل الآن
                                    </button>
                                </div>
                            @empty
                            @endforelse

                        </div>
                    </div>
                @endif

                <div class="modal fade" id="opportunityModal1" tabindex="-1" aria-labelledby="opportunityModalLabel1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="opportunityModalLabel1"></h5>
                                <button type="button" class="btn-close m-0 modal-close-btn" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img src="" class="img-fluid rounded mb-3" alt="">
                                <h6><strong>تفاصيل الفرصة:</strong></h6>
                                <p></p>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-secondary modal-close-btn">إغلاق</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="side-social-media my-5">
                    <h4 class="title-side">
                        تـابعنـا علـى
                    </h4>
                    @php $social_media = SocialMedia::with('icon')->where('position',\App\Enums\LinkPosition::AllPlaces->value)->orderByDesc('order')->get(); @endphp
                    <div class="list-social-media">
                        @foreach($social_media as $social)
                            <div class="item twitter d-flex align-items-center justify-content-between">

                                <div class="icon">
                                    <img width="25" src="{{file_url($social?->icon?->icon_path)}}"
                                         alt="{{$social?->title}}"/>
                                </div>
                                <p>{{$social?->title}}</p>
                                <a href="{{$social?->url}}" target="_blank">متابعة</a>

                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
    @if($this->local_news?->children)
        <div class="container mt-5">
            <div class="local-news-header-controls">
                <div class="controls-header-title">
                    <p>أخبـار محليـة</p>
                </div>
                <ul class="right-choises">
                    @foreach($this->local_news?->children as $i => $local)
                        <li
                            class="{{ $i === 0 ? 'active' : '' }}"
                            data-index="{{ $i }}"
                        >
                            <a href="javascript:void(0)">{{ $local?->category_title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="local-news-tabs">
                @foreach($this->local_news->children as $i => $local)
                    <div
                        class="local-news-tab {{ $i === 0 ? 'active' : '' }}"
                        data-index="{{ $i }}"
                    >
                        <div class="row gx-4 gy-4">
                            @foreach(
                              $local
                                ->post_relation
                                ->sortByDesc('post.publish_date')
                                ->whereNotNull('post.slug')
                                ->take(3)
                            as $post_relation)
                                <div class="col-md-4">
                                    <div class="slider-content">
                                        <div class="slider-text">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div
                                                    class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                    <div class="dot-title red-dot"></div>
                                                    <h5 class="left-span">{{ $loop->iteration }}</h5>
                                                </div>
                                                <div class="date-article d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    {{ \Carbon\Carbon::parse($post_relation->post->publish_date)
                                                        ->isoFormat('hh:mm A DD MMMM YYYY') }}
                                                </div>
                                            </div>
                                            <h3 class="slider-contant-header">
                                                @if($post_relation->post->type?->type_name)
                                                    <span class="text-danger">
                          {{ $post_relation->post->type->type_name }}
                        </span>
                                                @endif
                                                {{ $post_relation->post->title }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if(!empty($this->last_videos) && $this->last_videos->isNotEmpty())
        <div class="container videos-section2 mt-5">
            <div class="video-section">
                <div class="video-list">
                    <header class="toplist-videos">
                        <div class="text-toplist">
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="state-video">now playing</p>
                                <span class="video-counter">1/{{ $this->last_videos->count() }}</span>
                            </div>
                            <p class="title-video-play"></p>
                        </div>
                        <button class="toggle-video">
                            <i class="fas fa-play"></i>
                        </button>
                    </header>
                    @foreach($this->last_videos as $i => $video)
                        @php
                            $isLocal = $video->type === MaterialTypeEnum::VIDEO->value
                                && $video->video_type === VideoTypeEnum::LOCAL->value;
                            $url = $isLocal
                                ? file_url($video?->files?->where('model_column','file')?->first()?->file?->path)
                                : $video->link;
                        @endphp
                        <div class="video-item {{ $i === 0 ? 'active' : '' }}"
                            data-video="{{ $url }}"
                            data-video-type="{{ $isLocal ? 'local' : 'youtube' }}"
                            data-title="{{ Str::limit($video?->title, 40, '...') }}"
                            data-index="{{ $i + 1 }}">
                            <img
                                src="{{ file_url($video?->files?->where('model_column','image')->first()?->file?->path) }}"
                                loading="lazy" alt="{{ $video?->title }}">
                            <p>{{ $video?->title }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="video-player" style="position: relative;">
                    @php
                        $firstVideo = $this->last_videos->first();
                        $thumb = file_url($firstVideo->files->where('model_column','image')->first()->file->path);
                    @endphp
                    <div id="video-thumbnail-overlay"
                         style="cursor:pointer; position: absolute; inset: 0; z-index: 10;">
                        <img src="{{ $thumb }}"
                             alt="thumbnail"
                             style="width:100%; height: 100%; object-fit: cover;">
                        <div
                            style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:60px; color:white;">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                    <div id="main-video" style="width: 100%"></div>
                </div>
            </div>
        </div>
    @endif
    @if(!empty($this->last_podcasts) && $this->last_podcasts->isNotEmpty())
        <div class="container mt-5">
            <div class="podcast-slider-container">
                <div class="podcast-slider-header">
                    <div class="main-section-title">
                        <h4>بودكاست</h4>
                    </div>
                    <a href="{{route('main.palestine_post.all_podcast_albums')}}" class="btn btn-outline-primary">
                        الـمزيــد
                        <i class="fa-solid fa-angles-left"></i>
                    </a>
                </div>
                <!-- استبدل السليدر بشبكة كاردز عادية -->
                <div class="row" id="cards-container">
                    @foreach($this->last_podcasts as $podcast)
                        <div class="col-md-6 ">
                            <div class="card">
                                <a href="{{route('main.palestine_post.podcast' , ['podcast_album_id' => $podcast?->material_album?->id , 'album_name' => $podcast?->material_album?->name])}}">
                                    <div class="card-img-top position-relative image-left-podcust">
                                        <img src="{{ file_url($podcast?->files?->where('model_column','image')?->first()?->file?->path) }}"
                                             loading="lazy" alt="صورة البودكاست">
                                        <div class="phone-img">
                                            <img
                                                src="{{ asset('assets/main/palestine_post/imgs/microphone.png') }}"
                                                alt="Microphone icon">
                                        </div>
                                        <div class="podcast-contant">
                                            <div
                                                class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                                <div class="dot-title"></div>
                                                <h4 class="card-title-with-dot">بودكاست</h4>
                                            </div>
                                            {{--                                            <livewire:main.palestine-post.share-post :key="'podcasts'.date('Y-m-d').$loop->index" :title="$podcast?->title" :image="file_url($podcast?->files()?->where('model_column','image')?->first()?->file?->path)"/>--}}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <a class="card-title">
                                            {{$podcast?->title}}
                                        </a>

                                        <div class="audio-player">
                                            <div class="buttons-group d-flex gap-2">
                                                <!-- Forward Button -->
                                                <button class="forward audio-button" aria-label="تقديم">
                                                    <i class="fas fa-forward" aria-hidden="true"></i>
                                                </button>
                                                <!-- Play/Pause Button -->
                                                <button class="play-pause audio-button" aria-label="تشغيل">
                                                    <i class="fas fa-play" aria-hidden="true"></i>
                                                </button>
                                                <!-- Rewind Button -->
                                                <button class="rewind audio-button" aria-label="ترجيع">
                                                    <i class="fas fa-backward" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div class="audio-group d-flex gap-2 w-100">
                                                <!-- Waveform Container with audio source -->
                                                <div class="waveform-ph"
                                                     data-audio="{{file_url($podcast?->files?->where('model_column','file')?->first()?->file?->path)}}"></div>
                                                <!-- Time Display -->
                                                <span class="time-display audio-time">0:00</span>
                                            </div>
                                            <!-- Volume Control Container -->
                                            <div class="volume-control"
                                                 style="display: inline-flex; align-items: center; gap: 5px;">
                                                <!-- Volume Slider -->
                                                <input type="range" class="volume-slider" min="0" max="1" step="0.01"
                                                       value="1" aria-label="مستوى الصوت">
                                                <!-- Volume Icon -->
                                                <span class="volume-icon" style="color: #00a2b9;">
                                    <i class="fas fa-volume-up"></i>
                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if(!empty($this->special_files) && $this->special_files->isNotEmpty())
        <div class="files-section2 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="fb-accordion">
                            @foreach($this->special_files as $special_file)
                                <!-- Opened section -->
                                <div class="fb-accordion-item @if($loop->first) fb-active @else fb-closed @endif">
                                    <div class="fb-accordion-title">
                                        <i class="fa-solid fa-square"></i>
                                        {{$special_file->file_name}}
                                    </div>
                                    <div class="fb-accordion-content">
                                        <div class="image">
                                            <img
                                                src="{{ file_url($special_file?->files?->file?->path)}}"
                                                loading="lazy" alt="صورة الملف الخاص">
                                        </div>
                                        <div class="text">
                                            <h3>{{$special_file->file_name}}</h3>
                                            <p>
                                                {{$special_file->file_description}}
                                            </p>
                                            <ul>
                                                @php
                                                    $all_post_relations = $special_file?->post_relation->whereNotNull('post.id')->sortByDesc('post.publish_date');

                                                    $posts_to_show_in_list = $all_post_relations->take(3);
                                                @endphp

                                                @foreach($posts_to_show_in_list as $relation)
                                                    @if(isset($relation?->post?->id))
                                                        <li>
                                                            <a href="{{ route('main.palestine_post.show_post', ['id' => $relation->post->id, 'slug' => $relation->post->slug]) }}">
                                                                {{ Str::limit($relation->post->title, 60) }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach

                                                @if($all_post_relations->count() > 3)
                                                    <li class="more-results-link">
                                                        <a href="#">
                                                            عرض كل الأخبار المتعلقة بهذا الملف <i class="fa-solid fa-arrow-left ms-2"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(!empty($this->articles) && $this->articles->isNotEmpty() && !empty($this->quotes) && $this->quotes->isNotEmpty())
        <section class="quotes-articles-section my-5">
            <div class="container">
                <div class="row">
                    <!-- عمود المقالات -->
                    <div class="col-md-6">
                        <div class="article-section h-100 border-0 p-0">
                            <!-- حاوية المقالات مع تمرير عمودي  <div class="article-header d-flex justify-content-between align-items-center mb-3">
                                 <h4>مقالات</h4>
                             </div>  -->
                            <div class="podcast-slider-header">

                                <div class="main-section-title">
                                    <h4>مقالات</h4>
                                </div>

                            </div>
                            <!-- حاوية المقالات مع تمرير عمودي -->
                            <div class="article-items">
                                @foreach($this->articles->take(3) as $article)
                                    <div class="article-item mb-4">
                                        <div class="article-card">
                                            <div class="d-flex align-items-center justify-content-between">
                                                @if($article?->author?->relationable)
                                                    <div class="author-text d-flex align-items-center mb-2">
                                                        <div class="author-img">
                                                            @if(!empty($article?->author?->relationable?->files?->file?->path))
                                                                <img
                                                                    src="{{file_url($article?->author?->relationable?->files?->file?->path)}}"
                                                                    loading="lazy" alt="صورة الكاتب">
                                                            @else
                                                                <img
                                                                    src="https://ui-avatars.com/api/?name={{$article?->author?->relationable?->name}}"
                                                                    loading="lazy" alt="صورة الكاتب">
                                                            @endif
                                                        </div>
                                                        <div class="author-details ms-2">
                                                            <a href="{{route('main.palestine_post.writer',['author_id'=>$article?->author?->relationable?->id,'author_name'=>$article?->author?->relationable?->name])}}">
                                                                <p class="name">{{$article?->author?->relationable?->name}}</p>
                                                            </a>
                                                            <p class="job-title">{{Str::limit($article?->author?->relationable?->work,50)}}</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="date-container">
                                                    <div class="date-article d-flex align-items-center gap-2">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                        {{Carbon::parse($article?->publish_date ?? now())->isoFormat('hh:mm A DD MMMM YYYY')}}
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{route('main.palestine_post.show_post',['id'=>$article?->id,'slug'=>$article?->slug])}}"
                                               class="card-title article-title article-slider-head-text">
                                                <h3>
                                                    {{$article?->title}}
                                                </h3>
                                            </a>

                                            <p class="article-slider-descrption">

                                                {{Str::words($article?->description,20,'...')}}

                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- عمود الاقتباسات -->
                    <div class="col-md-6">
                        <div class="quote-section h-100 border-0 p-0">
                            <div class="podcast-slider-header">

                                <div class="main-section-title">
                                    <h4>اقتباسات</h4>
                                </div>

                            </div>
                            <!-- <div class="quote-header d-flex justify-content-between align-items-center mb-3">
                                 <h4>اقتباسات</h4>
                             </div>-->
                            <!-- حاوية الاقتباسات مع تمرير عمودي -->
                            <div class="quote-items">
                                @foreach($this->quotes->take(3) as $quote)
                                    <div class="quote-item mb-4">
                                        <div class="logo-unicef">
                                            <img
                                                src="{{file_url($quote?->author?->files?->file?->path)}}"
                                                loading="lazy" alt="صورة الكاتب">
                                        </div>
                                        <div class="Quotation-slider-content">
                                            <div
                                                class="share-container d-flex justify-content-between align-items-center">
                                                <i class="fa-solid fa-quote-right"></i>
                                                {{--                                            <livewire:main.palestine-post.share-post :key="'quotes'.date('Y-m-d').$loop->index" :title="Str::limit($quote?->quote_text,120)" :image="file_url($quote?->author?->files()?->first()?->file?->path)"/>--}}
                                            </div>
                                            <h4 class="Quotation-title">اقتباس</h4>
                                            <a class="Quotation-text">
                                                {{Str::limit($quote?->quote_text,120)}}
                                            </a>
                                            <div class="author-text d-flex align-items-center mt-2">
                                                <div class="author-img">
                                                    <img
                                                        src="{{file_url($quote?->author?->files?->file?->path)}}"
                                                        loading="lazy" alt="صورة الكاتب">
                                                </div>
                                                <div class="author-details ms-2">
                                                    <a href="{{route('main.palestine_post.writer',['author_id'=>$quote?->author?->id,'author_name'=>$quote?->author?->name])}}">
                                                        <p class="name">{{$quote?->author?->name}}</p>
                                                    </a>
                                                    <p class="job-title">{{Str::limit($quote?->author?->work,50)}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
@section('script')
    <script>
        function playVideo(wrapper) {
            const parent = wrapper.parentElement;
            wrapper.classList.add('d-none');

            const videoElement = parent.querySelector('.video-embed');
            videoElement.classList.remove('d-none');

            // إذا كان iframe (YouTube)
            if (videoElement.tagName.toLowerCase() === 'iframe') {
                let src = videoElement.getAttribute('src');
                if (!src.includes('autoplay=1')) {
                    src += (src.includes('?') ? '&' : '?') + 'autoplay=1';
                    videoElement.setAttribute('src', src);
                }
            }

            // إذا كان <video> محلي، شغّله يدويًا
            if (videoElement.tagName.toLowerCase() === 'video') {
                videoElement.play().catch(e => console.error('فشل التشغيل:', e));
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const items = document.querySelectorAll('.video-item');
            const overlay = document.getElementById('video-thumbnail-overlay');
            const mainVideo = document.getElementById('main-video');
            const counter = document.querySelector('.video-counter');
            const title = document.querySelector('.title-video-play');
            const toggleButton = document.querySelector('.toggle-video');
            const toggleIcon = toggleButton?.querySelector('i');

            let currentType = null; // لحفظ نوع الفيديو الحالي
            let youtubeIframe = null;

            function extractYouTubeID(url) {
                try {
                    const u = new URL(url);
                    if (u.searchParams.has('v')) return u.searchParams.get('v');
                    const m = url.match(/(?:youtu\.be\/|embed\/|v=)([\w-]+)/);
                    return m ? m[1] : null;
                } catch {
                    return null;
                }
            }

            function clearPlayer() {
                const vids = mainVideo.querySelectorAll('video');
                vids.forEach(v => {
                    v.pause();
                    v.src = "";
                    v.load();
                });
                mainVideo.innerHTML = '';
                youtubeIframe = null;
            }

            function playVideo(url, type, titleText, index) {
                clearPlayer();
                overlay.style.display = 'none';
                counter.textContent = `${index}/${items.length}`;
                title.textContent = titleText;
                items.forEach(i => i.classList.remove('active'));
                const activeItem = Array.from(items).find(i => i.dataset.index == index);
                if (activeItem) activeItem.classList.add('active');
                currentType = type;

                // Reset toggle icon to pause
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-play');
                    toggleIcon.classList.add('fa-pause');
                }

                if (type === 'local') {
                    setTimeout(() => {
                        mainVideo.innerHTML = `
                        <video src="${url}" controls autoplay muted width="100%" style="display:block;"></video>
                    `;
                        setTimeout(() => {
                            const videoElement = mainVideo.querySelector('video');
                            if (videoElement) {
                                videoElement.muted = false;
                                videoElement.play().catch(e => console.warn('Local video error:', e));
                            }
                        }, 300);
                    }, 100);
                } else {
                    const videoId = extractYouTubeID(url);
                    if (videoId) {
                        mainVideo.innerHTML = `
                        <iframe id="youtube-player" width="100%"
                            src="https://www.youtube.com/embed/${videoId}?enablejsapi=1&autoplay=1&mute=0&controls=1"
                            frameborder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    `;
                        // نحتاج لحفظ الـ iframe للتحكم فيه لاحقًا
                        youtubeIframe = document.getElementById('youtube-player');
                    }
                }
            }

            // الزر الرئيسي لتشغيل/إيقاف الفيديو
            toggleButton?.addEventListener('click', () => {
                const video = mainVideo.querySelector('video');
                const iframe = mainVideo.querySelector('iframe');

                if (video) {
                    // التعامل مع الفيديو المحلي
                    if (video.paused) {
                        video.play();
                        toggleIcon.classList.remove('fa-play');
                        toggleIcon.classList.add('fa-pause');
                    } else {
                        video.pause();
                        toggleIcon.classList.remove('fa-pause');
                        toggleIcon.classList.add('fa-play');
                    }
                } else if (iframe && currentType === 'youtube') {
                    // التعامل مع يوتيوب
                    iframe.contentWindow.postMessage(JSON.stringify({
                        event: "command",
                        func: toggleIcon.classList.contains('fa-pause') ? "pauseVideo" : "playVideo",
                        args: []
                    }), "*");

                    if (toggleIcon.classList.contains('fa-pause')) {
                        toggleIcon.classList.remove('fa-pause');
                        toggleIcon.classList.add('fa-play');
                    } else {
                        toggleIcon.classList.remove('fa-play');
                        toggleIcon.classList.add('fa-pause');
                    }
                } else {
                    // ⛔ لا يوجد فيديو شغال → نشغّل أول فيديو
                    const first = document.querySelector('.video-item');
                    if (first) {
                        playVideo(
                            first.dataset.video,
                            first.dataset.videoType,
                            first.dataset.title,
                            first.dataset.index
                        );
                    }
                }
            });


            // عند الضغط على أول صورة في الـ overlay
            overlay.addEventListener('click', () => {
                const first = document.querySelector('.video-item');
                if (!first) return;
                playVideo(
                    first.dataset.video,
                    first.dataset.videoType,
                    first.dataset.title,
                    first.dataset.index
                );
            });

            // عند اختيار فيديو من القائمة
            items.forEach(item => {
                item.addEventListener('click', () => {
                    playVideo(
                        item.dataset.video,
                        item.dataset.videoType,
                        item.dataset.title,
                        item.dataset.index
                    );
                });
            });

            // عند الرجوع للصفحة من زر back في المتصفح
            window.addEventListener('pageshow', function (event) {
                const isBack = event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward";
                if (isBack) {
                    clearPlayer();
                    if (overlay) overlay.style.display = 'block';
                }
            });

            // عرض عنوان الفيديو الأول مباشرة
            const firstItem = document.querySelector('.video-item');
            if (firstItem) {
                title.textContent = firstItem.dataset.title;
            }
        });
    </script>

@endsection
