@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |    الفيديو

@endsection
<div>

    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">فيـديو </h4>

                    <ul class="list-breadcrumb">
                        <li>
                            الرئيسية
                        </li>
                        <li>
                            <span>|</span>
                        </li>
                        <li class="active">
                            <a href="#">فيـديو </a>
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
                        <input type="text" class="filter-input" placeholder="بحث عن النتائج" />
                        <i class="fa fa-search"></i>
                    </div>

                    <!-- Select Category -->
                    <div class="filter-item">
                        <select class="filter-select">
                            <option value="">التصنيف</option>
                            <option value="category1">التصنيف 1</option>
                            <option value="category2">التصنيف 2</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div class="filter-item">
                        <input type="date" class="filter-date" placeholder="التاريخ من" />
                    </div>

                    <!-- Date To -->
                    <div class="filter-item">
                        <input type="date" class="filter-date" placeholder="التاريخ إلى" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="news-videos">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <!-- Main V'ideo -->
                    <div class="main-video-card">


                        <div class="video-container" onclick="playVideo(this)">
                            <img src="https://dummyimage.com/{{config('features.image_sizes.video_container')}}/dddddd/000000/" alt="Main Video">
                            <button class="play-button">
                                <i class="fas fa-play"></i>
                            </button>
                            <iframe src="https://www.youtube.com/embed/VIDEO_ID?autoplay=1" allow="autoplay"></iframe>
                        </div>
                        <div class="news-details mt-3">
                            <div class="card-title">
                                <h4 class="card-title-with-dot">أخبار محلية</h4>

                                <div class="share-article">
                                    <h4 class="btn-share">
                                        شارك
                                        <i class="fa-solid fa-share"></i>
                                    </h4>
                                    <div class="share-social-icons social-icons">
                                        <ul>
                                            <li>
                                                <a href="https://facebook.com" target="_blank">
                                                    <img src="./imgs/64px-2021_Facebook_icon.svg.png" alt="Facebook" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://twitter.com" target="_blank">
                                                    <img src="./imgs/twitter.png" alt="Twitter" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://whatsapp.com" target="_blank">
                                                    <img src="./imgs/whatsapp.png" alt="WhatsApp" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://Telegram.com" target="_blank">
                                                    <img src="./imgs/64px-Telegram_logo.svg.png" alt="Telegram" />
                                                </a>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <p class="date">
                                <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                12:06 م 09 ديسمبر 2024
                            </p>
                            <h4 class="title-video">توافد أهالي الداخل إلى جنين لدعم السوق المحلي بعد إعادة فتح معبر الجلمة
                                بعد إغلاق دام أكثر من عام</h4>
                        </div>
                    </div>
                </div>
                <!-- Side Videos -->
                <div class="col-md-5">
                    <div class="d-flex mb-3  small-video-side">
                        <div class="video-container " onclick="playVideo(this)">
                            <img src="https://dummyimage.com/{{config('features.image_sizes.video_container')}}/dddddd/000000/" alt="Side Video 1">
                            <button class="play-button">
                                <i class="fas fa-play"></i>
                            </button>
                            <iframe src="https://www.youtube.com/embed/VIDEO_ID?autoplay=1" allow="autoplay"></iframe>
                        </div>
                        <div class="news-details">
                            <div class="card-title">
                                <h4 class="card-title-with-dot">أخبار محلية</h4>

                                <div class="share-article">
                                    <h4 class="btn-share">
                                        شارك
                                        <i class="fa-solid fa-share"></i>
                                    </h4>
                                    <div class="share-social-icons social-icons">
                                        <ul>
                                            <li>
                                                <a href="https://facebook.com" target="_blank">
                                                    <img src="./imgs/64px-2021_Facebook_icon.svg.png" alt="Facebook" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://twitter.com" target="_blank">
                                                    <img src="./imgs/twitter.png" alt="Twitter" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://whatsapp.com" target="_blank">
                                                    <img src="./imgs/whatsapp.png" alt="WhatsApp" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://Telegram.com" target="_blank">
                                                    <img src="./imgs/64px-Telegram_logo.svg.png" alt="Telegram" />
                                                </a>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <p class="date">
                                <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                12:06 م 09 ديسمبر 2024
                            </p>
                            <h4 class="title-video-small">المتحدث باسم الدفاع المدني...</h4>

                        </div>
                    </div>
                    <div class="d-flex mb-3  small-video-side">
                        <div class="video-container " onclick="playVideo(this)">
                            <img src="https://dummyimage.com/{{config('features.image_sizes.video_container')}}/dddddd/000000/" alt="Side Video 1">
                            <button class="play-button">
                                <i class="fas fa-play"></i>
                            </button>
                            <iframe src="https://www.youtube.com/embed/VIDEO_ID?autoplay=1" allow="autoplay"></iframe>
                        </div>
                        <div class="news-details">
                            <div class="card-title">
                                <h4 class="card-title-with-dot">أخبار محلية</h4>

                                <div class="share-article">
                                    <h4 class="btn-share">
                                        شارك
                                        <i class="fa-solid fa-share"></i>
                                    </h4>
                                    <div class="share-social-icons social-icons">
                                        <ul>
                                            <li>
                                                <a href="https://facebook.com" target="_blank">
                                                    <img src="./imgs/64px-2021_Facebook_icon.svg.png" alt="Facebook" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://twitter.com" target="_blank">
                                                    <img src="./imgs/twitter.png" alt="Twitter" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://whatsapp.com" target="_blank">
                                                    <img src="./imgs/whatsapp.png" alt="WhatsApp" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://Telegram.com" target="_blank">
                                                    <img src="./imgs/64px-Telegram_logo.svg.png" alt="Telegram" />
                                                </a>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <p class="date">
                                <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                12:06 م 09 ديسمبر 2024
                            </p>
                            <h4 class="title-video-small">المتحدث باسم الدفاع المدني...</h4>

                        </div>
                    </div>
                    <div class="d-flex mb-3  small-video-side">
                        <div class="video-container " onclick="playVideo(this)">
                            <img src="https://dummyimage.com/{{config('features.image_sizes.video_container')}}/dddddd/000000/" alt="Side Video 1">
                            <button class="play-button">
                                <i class="fas fa-play"></i>
                            </button>
                            <iframe src="https://www.youtube.com/embed/VIDEO_ID?autoplay=1" allow="autoplay"></iframe>
                        </div>
                        <div class="news-details">
                            <div class="card-title">
                                <h4 class="card-title-with-dot">أخبار محلية</h4>

                                <div class="share-article">
                                    <h4 class="btn-share">
                                        شارك
                                        <i class="fa-solid fa-share"></i>
                                    </h4>
                                    <div class="share-social-icons social-icons">
                                        <ul>
                                            <li>
                                                <a href="https://facebook.com" target="_blank">
                                                    <img src="./imgs/64px-2021_Facebook_icon.svg.png" alt="Facebook" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://twitter.com" target="_blank">
                                                    <img src="./imgs/twitter.png" alt="Twitter" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://whatsapp.com" target="_blank">
                                                    <img src="./imgs/whatsapp.png" alt="WhatsApp" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://Telegram.com" target="_blank">
                                                    <img src="./imgs/64px-Telegram_logo.svg.png" alt="Telegram" />
                                                </a>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <p class="date">
                                <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                12:06 م 09 ديسمبر 2024
                            </p>
                            <h4 class="title-video-small">المتحدث باسم الدفاع المدني...</h4>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
    <div class="container all-articles">
        <div class="row">
            <div class="col-md-4">
                <div class="news-card">
                    <div class="news-card-content">
                        <div class="news-card-img">
                            <!-- Added alt text for SEO and accessibility -->
                            <img src="https://dummyimage.com/{{config('features.image_sizes.video_size')}}/dddddd/000000/" loading="lazy"
                                 class="img-fluid" alt="Image of Israeli military officer statement" />
                        </div>
                        <div class="news-card-text">
                            <div class="date-container">
                                <div class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                    <div class="dot-title "></div>
                                    <h4 class="card-title-with-dot">أخبار محلية</h4>
                                </div>
                                <p class="date">
                                    <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                    12:06 م 09 ديسمبر 2024
                                </p>

                            </div>

                            <a class="card-title" href="#">
                                ضابط في جيش الاحتلال: لن نصل إلى آخر صاروخ لدى حماس حتى لو
                                حاربناها 20 عاما
                            </a>

                            <p>
                                صرح ضابط كبير في جيش الاحتلال الاسرائيلي للقناه ١٢ العبريه
                                اليوم الاحد بان"اسرائيل" لن تنجح بالوصول الي اخر صاروخ
                                تمتلكه حركه "حماس" حتي لو ظلت تحاربها ٢٠ عاما في غزة
                            </p>

                            <div class="share-article">
                                <h4 class="btn-share">
                                    شارك
                                    <i class="fa-solid fa-share"></i>
                                </h4>
                                <div class="share-social-icons social-icons">
                                    <ul>
                                        <li>
                                            <a href="https://facebook.com" target="_blank">
                                                <img src="./imgs/64px-2021_Facebook_icon.svg.png"
                                                     alt="Facebook" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://twitter.com" target="_blank">
                                                <img src="./imgs/twitter.png" alt="Twitter" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://whatsapp.com" target="_blank">
                                                <img src="./imgs/whatsapp.png" alt="WhatsApp" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://Telegram.com" target="_blank">
                                                <img src="./imgs/64px-Telegram_logo.svg.png"
                                                     alt="Telegram" />
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="news-card">
                    <div class="news-card-content">
                        <div class="news-card-img">
                            <!-- Added alt text for SEO and accessibility -->
                            <img src="https://dummyimage.com/{{config('features.image_sizes.video_size')}}/dddddd/000000/" loading="lazy"
                                 class="img-fluid" alt="Image of Israeli military officer statement" />
                        </div>
                        <div class="news-card-text">
                            <div class="date-container">
                                <div class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                    <div class="dot-title "></div>
                                    <h4 class="card-title-with-dot">أخبار محلية</h4>
                                </div>
                                <p class="date">
                                    <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                    12:06 م 09 ديسمبر 2024
                                </p>

                            </div>

                            <a class="card-title" href="#">
                                ضابط في جيش الاحتلال: لن نصل إلى آخر صاروخ لدى حماس حتى لو
                                حاربناها 20 عاما
                            </a>

                            <p>
                                صرح ضابط كبير في جيش الاحتلال الاسرائيلي للقناه ١٢ العبريه
                                اليوم الاحد بان"اسرائيل" لن تنجح بالوصول الي اخر صاروخ
                                تمتلكه حركه "حماس" حتي لو ظلت تحاربها ٢٠ عاما في غزة
                            </p>

                            <div class="share-article">
                                <h4 class="btn-share">
                                    شارك
                                    <i class="fa-solid fa-share"></i>
                                </h4>
                                <div class="share-social-icons social-icons">
                                    <ul>
                                        <li>
                                            <a href="https://facebook.com" target="_blank">
                                                <img src="./imgs/64px-2021_Facebook_icon.svg.png"
                                                     alt="Facebook" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://twitter.com" target="_blank">
                                                <img src="./imgs/twitter.png" alt="Twitter" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://whatsapp.com" target="_blank">
                                                <img src="./imgs/whatsapp.png" alt="WhatsApp" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://Telegram.com" target="_blank">
                                                <img src="./imgs/64px-Telegram_logo.svg.png"
                                                     alt="Telegram" />
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="news-card">
                    <div class="news-card-content">
                        <div class="news-card-img">
                            <!-- Added alt text for SEO and accessibility -->
                            <img src="https://dummyimage.com/{{config('features.image_sizes.video_size')}}/dddddd/000000/" loading="lazy"
                                 class="img-fluid" alt="Image of Israeli military officer statement" />
                        </div>
                        <div class="news-card-text">
                            <div class="date-container">
                                <div class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
                                    <div class="dot-title "></div>
                                    <h4 class="card-title-with-dot">أخبار محلية</h4>
                                </div>
                                <p class="date">
                                    <i class="fa-regular fa-calendar" alt="Calendar icon"></i>
                                    12:06 م 09 ديسمبر 2024
                                </p>

                            </div>

                            <a class="card-title" href="#">
                                ضابط في جيش الاحتلال: لن نصل إلى آخر صاروخ لدى حماس حتى لو
                                حاربناها 20 عاما
                            </a>

                            <p>
                                صرح ضابط كبير في جيش الاحتلال الاسرائيلي للقناه ١٢ العبريه
                                اليوم الاحد بان"اسرائيل" لن تنجح بالوصول الي اخر صاروخ
                                تمتلكه حركه "حماس" حتي لو ظلت تحاربها ٢٠ عاما في غزة
                            </p>

                            <div class="share-article">
                                <h4 class="btn-share">
                                    شارك
                                    <i class="fa-solid fa-share"></i>
                                </h4>
                                <div class="share-social-icons social-icons">
                                    <ul>
                                        <li>
                                            <a href="https://facebook.com" target="_blank">
                                                <img src="./imgs/64px-2021_Facebook_icon.svg.png"
                                                     alt="Facebook" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://twitter.com" target="_blank">
                                                <img src="./imgs/twitter.png" alt="Twitter" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://whatsapp.com" target="_blank">
                                                <img src="./imgs/whatsapp.png" alt="WhatsApp" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://Telegram.com" target="_blank">
                                                <img src="./imgs/64px-Telegram_logo.svg.png"
                                                     alt="Telegram" />
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Link -->
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true"><i
                                    class="fa-solid fa-angles-right"></i></a>
                        </li>

                        <!-- Page 3 -->
                        <li class="page-item"><a class="page-link" href="#">3</a></li>

                        <!-- Page 5 (Active) -->
                        <li class="page-item active" aria-current="page">
                            <a class="page-link" href="#">5</a>
                        </li>

                        <!-- Page 7 -->
                        <li class="page-item"><a class="page-link" href="#">7</a></li>

                        <!-- Page 8 -->
                        <li class="page-item"><a class="page-link" href="#">8</a></li>

                        <!-- Page 10 -->
                        <li class="page-item"><a class="page-link" href="#">10</a></li>

                        <!-- Next Link -->
                        <li class="page-item">
                            <a class="page-link" href="#"><i class="fa-solid fa-angles-left"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>


</div>
@section('script')
@endsection
