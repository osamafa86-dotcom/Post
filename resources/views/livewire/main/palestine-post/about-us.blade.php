@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')  ? config('system.site_name') : 'فلسطين بوست' }}    |  من نحن

@endsection
<div>

    <section class="breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 d-flex align-items-center justify-content-between">
                    <h4 class="title-breadcrumb">من نحن</h4>
                    <ul class="list-breadcrumb">
                        <li>الرئيسية</li>
                        <li><span>|</span></li>
                        <li class="active">
                            <a href="">من نحن </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ============= بداية الأقسام الناقصة (بعد الـbreadcrumb وقبل الفوتر) ============= -->
    <!-- قسم: عن الموقع -->
    <section class="about-us-intro ">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-md-6 content">
                    <h2 class="mb-4">
                        "فلسطين بوست" منبراً إعلامياً جديداً، تعطي حيزاً كبيراً للمواطن في صناعة الحدث الإعلامي </h2>
                    <p class="mb-3">
                        مع تطور وازدهار شبكات التواصل الاجتماعي اتجه مجموعة من الصحفيين الفلسطينيين بتاريخ 5/03/2015
                        بجهود تطوعية ذاتية لتأسيس منبر إعلامي، هدفه تعزيز صحافة المواطن وضبطها ضمن أسس ومعايير الخبر
                        الصحفي؛ رغبة في تقديم مضمون إعلامي يتسم بالمصداقية والتميز. 
                        فكانت "فلسطين بوست" منبراً إعلامياً جديداً، تعطي حيزاً كبيراً للمواطن في صناعة الحدث الإعلامي،
                        وتقدم مضموناً يجمع أكبر قدر ممكن من أهداف الإعلام، في قالب بصري مشوق، ضمن حدود قيم الإعلام
                        وضوابطه.
                    </p>
                    <p>
                        بدأت الشبكة بالتواجد على جميع منصات التواصل الاجتماعي، ثم أطلقت موقعها الالكتروني الإخباري
                        "www.ppost.ps"، حيث تم استحداثه كمنصة آكثر ثباتًا لمساندة  المشروع عبر منصات التواصل الاجتماعي،
                        وتضمن برمجة الموقع مجموعة من الأقسام لتغطية القضايا الفلسطينية بطريقة معمقة مكثفة من جوانبها
                        المختلفة.

                    </p>
                </div>
                <!-- صورة توضيحية أو placeholder -->
                <div class="col-md-6 image">
                    <img src="https://dummyimage.com/{{ config('features.image_sizes.about_us') }}/dddddd/000000/" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- This section represents the goals or objectives of the platform -->
    <section class="goals-section ">
        <div class="container">
            <!-- Section title -->
            <h2 class="text-center mb-4">أهدافنا</h2>

            <div class="row gy-4">
                <!-- First goal card -->
                <div class="col-md-4">
                    <div class="goals-card text-center p-3">
                        <!-- Icon or image placeholder -->
                        <div class="goals-icon mb-3">
                            <!-- Replace 'goal-icon1.svg' with your actual icon/image path -->
                            <img src="{{asset('assets/main/palestine_post/images/icons/1.png')}}" alt="نشر الوعي" width="60" />
                        </div>
                        <!-- Goal title -->
                        <h4 class="mb-2">نشر الوعي بالقضية الفلسطينية</h4>
                        <!-- Goal description text -->
                        <p>
                            تهدف المنصة إلى تعزيز الوعي بالقضية الفلسطينية عبر نقل الأحداث
                            وطرح التحليلات الدقيقة، مما يساعد الجمهور على الإلمام بمجريات
                            الأمور والاتصال بالقضايا الوطنية بشكل أعمق.
                        </p>
                    </div>
                </div>

                <!-- Second goal card -->
                <div class="col-md-4">
                    <div class="goals-card text-center p-3">
                        <!-- Icon or image placeholder -->
                        <div class="goals-icon mb-3">
                            <!-- Replace 'goal-icon2.svg' with your actual icon/image path -->
                            <img src="{{asset('assets/main/palestine_post/images/icons/2.png')}}" alt="محتوى عالمي موثوق" width="60" />
                        </div>
                        <!-- Goal title -->
                        <h4 class="mb-2">تقديم محتوى عالمي موثوق</h4>
                        <!-- Goal description text -->
                        <p>
                            نقدّم تغطية إخبارية موثوقة وتحليلات عميقة، وذلك وفقًا لضوابط
                            مهنية تضمن تنوع الآراء واحترام مختلف الأطياف السياسية.
                        </p>
                    </div>
                </div>

                <!-- Third goal card -->
                <div class="col-md-4">
                    <div class="goals-card text-center p-3">
                        <!-- Icon or image placeholder -->
                        <div class="goals-icon mb-3">
                            <!-- Replace 'goal-icon3.svg' with your actual icon/image path -->
                            <img src="{{asset('assets/main/palestine_post/images/icons/3.png')}}" alt="تعزيز منصة المواطن" width="60" />
                        </div>
                        <!-- Goal title -->
                        <h4 class="mb-2">تعزيز منصة المواطن</h4>
                        <!-- Goal description text -->
                        <p>
                            نسعى لإشراك المجتمع ورأي المواطن في بناء المحتوى، عبر مساحة
                            للتعبير الحر، ودعم الرأي والرأي الآخر لتطوير الحوار وتعميق الفهم.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="principles-section ">
        <div class="container">
            <!-- Section title -->
            <h2 class="text-center mb-5">مبادئنا</h2>

            <!-- Principle 1: Text on the left, Image on the right -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <!-- Principle title -->
                    <h3 class="mb-3">المصداقية</h3>
                    <!-- Principle description -->
                    <p>
                        نلتزم بتقديم المعلومات بدقة وأمانة، والحرص على نشر الأخبار بعد التحقق منها.
                        نؤمن أن الشفافية في عرض الحقائق هي الأساس لبناء الثقة مع الجمهور.
                        <br><br>
                        وتعتمد مؤسستنا منهجية واضحة في التحقق من المصادر المختلفة، لتقديم صورة شاملة ومتكاملة للقارئ.
                        فالحفاظ على صدقية المحتوى يسهم في ترسيخ الثقة واستمرارية التواصل مع المتابعين، ويعزز من دورنا
                        كمنصة إعلامية موثوقة.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://dummyimage.com/{{ config('features.image_sizes.about_us') }}/dddddd/000000/" alt="المصداقية" class="img-fluid">
                </div>
            </div>

            <!-- Principle 2: Image on the left, Text on the right (reverse columns on medium+) -->
            <div class="row align-items-center mb-5 flex-md-row-reverse">
                <div class="col-md-6">
                    <h3 class="mb-3">الشفافية</h3>
                    <p>
                        نحرص على توضيح مصادر المعلومات والبيانات، وتجنب أي تضليل أو إخفاء للحقائق.
                        نتعامل بوضوح مع الجمهور، ونوفر سياقًا متكاملًا لكل قضية.
                        <br><br>
                        إن الشفافية تتيح للجمهور تقييم المعلومات بدقة، وفهم الصورة الكاملة للحدث أو الخبر.
                        كما نؤكد على أهمية الإشارة إلى الأخطاء في حال وقوعها وتصحيحها علنًا،
                        لنحافظ على علاقة مبنية على الوضوح والاحترام المتبادل.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://dummyimage.com/{{ config('features.image_sizes.about_us') }}/dddddd/000000/" alt="الشفافية" class="img-fluid">
                </div>
            </div>

            <!-- Principle 3: Text on the left, Image on the right -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <h3 class="mb-3">المسؤولية</h3>
                    <p>
                        نسعى للتعامل مع القضايا بحس من المسؤولية الاجتماعية،
                        ونضع في اعتبارنا أثر المحتوى المنشور على المجتمع والفرد.
                        نتحمل نتائج ما ننشره ونقوم بتصحيح أي أخطاء فور اكتشافها.
                        <br><br>
                        ففي كل خطوة نتخذها، نراعي المبادئ الأخلاقية والإعلامية لضمان تغطية تحترم قيم المجتمع
                        وتُعلي من مصلحة المواطن. نؤمن بأن المسؤولية تتطلب منا النظر إلى الأبعاد المختلفة
                        للأحداث قبل اتخاذ قرار النشر.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://dummyimage.com/{{ config('features.image_sizes.about_us') }}/dddddd/000000/" alt="المسؤولية" class="img-fluid">
                </div>
            </div>

            <!-- Principle 4: Image on the left, Text on the right -->
            <div class="row align-items-center mb-5 flex-md-row-reverse">
                <div class="col-md-6">
                    <h3 class="mb-3">الموضوعية</h3>
                    <p>
                        نلتزم بالحياد وعرض وجهات النظر المختلفة من دون تحيز،
                        مع دعم الحقائق بالأدلة وتحليل المعطيات بدقة.
                        نسعى لفهم أعمق للأحداث قبل نقلها للجمهور.
                        <br><br>
                        من خلال إجراء مقابلات متنوعة مع الخبراء وأصحاب الشأن، نضمن تمثيلًا عادلًا لمختلف الرؤى،
                        ما يساعد على توسيع مدارك المتابعين وتمكينهم من تكوين رأي واعٍ يعتمد على حقائق موضوعية
                        بدلًا من الانطباعات السطحية.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://dummyimage.com/{{ config('features.image_sizes.about_us') }}/dddddd/000000/" alt="الموضوعية" class="img-fluid">
                </div>
            </div>

            <!-- Principle 5: Text on the left, Image on the right -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <h3 class="mb-3">الإنسانية</h3>
                    <p>
                        نؤمن بأهمية التركيز على الجانب الإنساني في التغطيات الإخبارية،
                        وتسليط الضوء على معاناة الأفراد وقصصهم الملهمة.
                        نسعى لتعزيز قيم التكاتف والتضامن المجتمعي.
                        <br><br>
                        نسعى دائمًا لإظهار الجانب الإنساني وراء كل قصة أو حدث، مما يساهم في تعزيز التلاحم
                        الاجتماعي ودعم الفئات الأكثر ضعفًا في المجتمع. ونؤكد على أن الإعلام يمكن أن يكون
                        قوة دافعة للتغيير الإيجابي.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://dummyimage.com/{{ config('features.image_sizes.about_us') }}/dddddd/000000/" alt="الإنسانية" class="img-fluid">
                </div>
            </div>

            <!-- Principle 6: Image on the left, Text on the right -->
            <div class="row align-items-center flex-md-row-reverse">
                <div class="col-md-6">
                    <h3 class="mb-3">النزاهة</h3>
                    <p>
                        نرفض كل أشكال التأثير الخارجي أو الرشوة،
                        ونحرص على الشفافية في التعاون مع الجهات الأخرى.
                        نضمن أن يبقى المحتوى خاليًا من أي تلاعب أو تزوير.
                        <br><br>
                        إننا نتصدى لأي محاولات لتسييس أو توجيه الخط التحريري، ونحرص على حماية استقلاليتنا
                        لتقديم محتوى مهني ومتوازن. فالالتزام بالنزاهة هو ما يحفظ ثقة الجمهور
                        ويؤكد مصداقية رسالتنا الإعلامية.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://dummyimage.com/{{ config('features.image_sizes.about_us') }}/dddddd/000000/" alt="النزاهة" class="img-fluid">
                </div>
            </div>

        </div>
    </section>

</div>
@section('script')
@endsection
