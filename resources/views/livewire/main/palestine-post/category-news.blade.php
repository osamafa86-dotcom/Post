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
                </div>
            </div>
        </div>
    </section>
    <div class="container all-articles">
        <div class="row">
            @foreach($this->posts as $item)
                <div class="col-md-4">
                    <div class="news-card">
                        <div class="news-card-content">
                            <a href="{{route('main.palestine_post.show_post' , ['id'=>$item?->id , 'slug'=>$item?->slug])}}">
                                <div class="news-card-img">
                                    <img src="{{file_url($item->files?->first()?->file?->path)}}"
                                         loading="lazy" class="img-fluid"
                                         alt="Image of Israeli military officer statement"/>
                                </div>
                            </a>
                            <div class="news-card-text">
                                <div class="date-container">
                                    <p class="date">
                                        <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                        {{Carbon\Carbon::parse($item?->publish_date)->isoFormat('D MMMM YYYY')}}
                                    </p>
                                    @if($item?->author?->relationable)
                                        <p class="author">
                                            <i class="fa-regular fa-user" alt="User icon"></i>
                                            <a style="color: #757575" href="{{route('main.palestine_post.writer' , ['author_id' => $item->author?->relationable?->id , 'author_name' => $item->author?->relationable?->name])}}">
                                                {{$item->author?->relationable?->name}}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                                <a class="card-title"
                                   href="{{route('main.palestine_post.show_post' , ['id'=>$item?->id , 'slug'=>$item?->slug])}}">
                                    {{Str::limit($item?->title , 45, '...')}}
                                </a>
                                <p>
                                    {{Str::limit($item?->description , 70, '...')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach

        </div>
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
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
