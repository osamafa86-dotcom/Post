@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }} - {{$page->page_title ?? 'صفحة خاصة'}}
@endsection
@section('style')
@endsection
<div>
    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">{{$page->page_title ?? 'صفحة خاصة'}}</h4>

                    <ul class="list-breadcrumb">
                        <li class="active">
                            <a href="{{route('main.palestine_post.index')}}">الرئيسية</a>
                        </li>
                        <li>
                            <span>|</span>
                        </li>
                        <li>
                            {{$page->page_title ?? 'صفحة خاصة'}}
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>
    <img src="{{file_url($page->files?->first()?->file?->path)}}" class="img-fluid" style="height: 350px!important;width: 100%!important" alt="page_image">

    <div class="container my-5">
        <div>
            {!! $page->page_content !!}

        </div>
    </div>

</div>
@section('script')
@endsection
