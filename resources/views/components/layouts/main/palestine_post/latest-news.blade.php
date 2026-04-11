{{--
    قسم آخر الأخبار - ppost
    طريقة الاستخدام في أي Blade view (مثلاً home.blade.php):
    @include('components.layouts.main.palestine_post.latest-news')

    أو مع بيانات مخصّصة من Controller/Livewire:
    @include('components.layouts.main.palestine_post.latest-news', ['latestNews' => $posts])
--}}

@php
    /*
    |--------------------------------------------------------------------------
    | مصدر بيانات قسم آخر الأخبار
    |--------------------------------------------------------------------------
    | يدعم القسم ثلاث حالات:
    |   1. إذا مُرّر $latestNews من الـ Controller/Livewire Component يستخدمه.
    |   2. وإلا يحاول الجلب من جدول posts (إن وُجد الموديل).
    |   3. وإلا يعود لبيانات افتراضية كـ fallback.
    */

    if (! isset($latestNews)) {
        $latestNews = null;

        if (class_exists(\App\Models\Post::class)) {
            try {
                $query = \App\Models\Post::query();

                if (\Illuminate\Support\Facades\Schema::hasColumn('posts', 'status')) {
                    $query->where('status', 'published');
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('posts', 'published_at')) {
                    $query->whereNotNull('published_at')
                          ->where('published_at', '<=', now());
                }

                $latestNews = $query->latest(
                    \Illuminate\Support\Facades\Schema::hasColumn('posts', 'published_at') ? 'published_at' : 'created_at'
                )->take(10)->get();
            } catch (\Throwable $e) {
                $latestNews = null;
            }
        }

        if ($latestNews === null || (is_countable($latestNews) && count($latestNews) === 0)) {
            $latestNews = [
                [
                    'title'    => 'مجلس الأمن يعقد جلسة طارئة لبحث تطوّرات الأوضاع الإنسانية في غزة',
                    'excerpt'  => 'يعقد مجلس الأمن الدولي اليوم جلسة طارئة لمناقشة التطوّرات الأخيرة في قطاع غزة بعد تصاعد التحذيرات الأممية من تدهور الوضع الإنساني، وسط مطالبات دولية بفتح ممرّات آمنة لإدخال المساعدات.',
                    'image'    => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?w=800&h=500&fit=crop',
                    'category' => 'سياسة',
                    'date'     => 'قبل 30 دقيقة',
                    'author'   => 'هيئة التحرير',
                    'url'      => '#',
                ],
                [
                    'title'    => 'البنك الدولي يُقرّ منحة طارئة بقيمة 200 مليون دولار للأراضي الفلسطينية',
                    'excerpt'  => 'أعلن البنك الدولي عن تقديم منحة طارئة بقيمة 200 مليون دولار لدعم الاقتصاد الفلسطيني وإعادة إعمار البنى التحتية المتضرّرة في قطاع غزة.',
                    'image'    => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?w=800&h=500&fit=crop',
                    'category' => 'اقتصاد',
                    'date'     => 'قبل ساعتين',
                    'author'   => 'سامر يوسف',
                    'url'      => '#',
                ],
                [
                    'title'    => 'قافلة مساعدات إنسانية دولية جديدة تدخل غزة عبر معبر رفح',
                    'excerpt'  => 'دخلت قافلة مساعدات إنسانية تضمّ 120 شاحنة محمّلة بالغذاء والأدوية إلى قطاع غزة عبر معبر رفح، بعد مفاوضات ماراثونية أشرفت عليها الأمم المتحدة.',
                    'image'    => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=800&h=500&fit=crop',
                    'category' => 'إنسانية',
                    'date'     => 'قبل 3 ساعات',
                    'author'   => 'محمد أبو سيف',
                    'url'      => '#',
                ],
                [
                    'title'    => 'المنتخب الفلسطيني يتأهّل تاريخياً إلى دور المجموعات في كأس آسيا 2026',
                    'excerpt'  => 'حقّق المنتخب الفلسطيني لكرة القدم إنجازاً تاريخياً بتأهّله إلى دور المجموعات في بطولة كأس آسيا 2026، بعد فوزٍ مستحق في المباراة الفاصلة.',
                    'image'    => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=800&h=500&fit=crop',
                    'category' => 'رياضة',
                    'date'     => 'قبل 5 ساعات',
                    'author'   => 'طارق حمدان',
                    'url'      => '#',
                ],
                [
                    'title'    => 'جامعة بيرزيت تفتتح أوّل مختبر متخصّص في الذكاء الاصطناعي في فلسطين',
                    'excerpt'  => 'افتتحت جامعة بيرزيت أوّل مختبر متخصّص للذكاء الاصطناعي في فلسطين، ويهدف إلى تأهيل الكوادر الأكاديمية وإطلاق مشاريع بحثية مشتركة مع جامعات دولية.',
                    'image'    => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&h=500&fit=crop',
                    'category' => 'تعليم',
                    'date'     => 'قبل 7 ساعات',
                    'author'   => 'نور عبد الله',
                    'url'      => '#',
                ],
                [
                    'title'    => 'افتتاح مستشفى ميداني دولي في خان يونس بطاقة 500 سرير',
                    'excerpt'  => 'افتُتح في مدينة خان يونس جنوب قطاع غزة مستشفى ميداني دولي جديد بطاقة استيعاب 500 سرير، بالتعاون مع منظمة الصحة العالمية وعدد من الدول المانحة.',
                    'image'    => 'https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?w=800&h=500&fit=crop',
                    'category' => 'صحة',
                    'date'     => 'قبل 9 ساعات',
                    'author'   => 'د. هبة ناصر',
                    'url'      => '#',
                ],
                [
                    'title'    => 'انطلاق فعاليات معرض الكتاب الفلسطيني الدولي الثاني عشر في رام الله',
                    'excerpt'  => 'انطلقت في مدينة رام الله فعاليات الدورة الثانية عشرة من معرض الكتاب الفلسطيني الدولي بمشاركة أكثر من 300 دار نشر من 25 دولة.',
                    'image'    => 'https://images.unsplash.com/photo-1578321272176-b7bbc0679853?w=800&h=500&fit=crop',
                    'category' => 'ثقافة',
                    'date'     => 'قبل 11 ساعة',
                    'author'   => 'رنا إبراهيم',
                    'url'      => '#',
                ],
                [
                    'title'    => 'شركات ناشئة فلسطينية تتصدّر قائمة "فوربس" للابتكار في الشرق الأوسط',
                    'excerpt'  => 'أدرجت مجلة "فوربس" خمس شركات ناشئة فلسطينية ضمن قائمتها السنوية لأبرز شركات الابتكار في منطقة الشرق الأوسط لعام 2026.',
                    'image'    => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=500&fit=crop',
                    'category' => 'تكنولوجيا',
                    'date'     => 'قبل 13 ساعة',
                    'author'   => 'عمر الشريف',
                    'url'      => '#',
                ],
                [
                    'title'    => 'القدس: إطلاق مشروع لترميم المباني التاريخية في البلدة القديمة',
                    'excerpt'  => 'أطلقت مؤسسات فلسطينية بالشراكة مع منظمة اليونسكو مشروعاً لترميم عشرات المباني التاريخية في البلدة القديمة بالقدس، في إطار جهود الحفاظ على الهوية الثقافية.',
                    'image'    => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=800&h=500&fit=crop',
                    'category' => 'القدس',
                    'date'     => 'قبل 15 ساعة',
                    'author'   => 'ليلى خالد',
                    'url'      => '#',
                ],
                [
                    'title'    => 'توقيع اتفاقية تعاون ثقافي شاملة بين فلسطين وعدد من الدول العربية',
                    'excerpt'  => 'وقّعت وزارة الثقافة الفلسطينية اتفاقية تعاون ثقافي مع ست دول عربية تتضمّن تبادل الخبرات وتنظيم مهرجانات مشتركة ودعم الفنانين الفلسطينيين.',
                    'image'    => 'https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?w=800&h=500&fit=crop',
                    'category' => 'ثقافة',
                    'date'     => 'قبل 18 ساعة',
                    'author'   => 'هيئة التحرير',
                    'url'      => '#',
                ],
            ];
        }
    }

    // تطبيع العناصر (يدعم array و object)
    $ppostLatestItems = collect($latestNews)->map(function ($item) {
        if (is_array($item)) {
            return [
                'title'    => $item['title']    ?? '',
                'excerpt'  => $item['excerpt']  ?? ($item['summary'] ?? ''),
                'image'    => $item['image']    ?? ($item['featured_image'] ?? asset('images/placeholder.jpg')),
                'category' => $item['category'] ?? 'أخبار',
                'date'     => $item['date']     ?? ($item['published_at'] ?? 'حديثاً'),
                'author'   => $item['author']   ?? 'هيئة التحرير',
                'url'      => $item['url']      ?? '#',
            ];
        }
        if (is_object($item)) {
            return [
                'title'    => $item->title ?? '',
                'excerpt'  => \Illuminate\Support\Str::limit(strip_tags($item->excerpt ?? $item->content ?? ''), 140),
                'image'    => $item->featured_image ?? ($item->image ?? asset('images/placeholder.jpg')),
                'category' => is_object($item->category ?? null) ? ($item->category->name ?? 'أخبار') : ($item->category ?? 'أخبار'),
                'date'     => isset($item->published_at) ? \Carbon\Carbon::parse($item->published_at)->diffForHumans() : 'حديثاً',
                'author'   => is_object($item->user ?? null) ? ($item->user->name ?? 'هيئة التحرير') : 'هيئة التحرير',
                'url'      => method_exists($item, 'getUrlAttribute') ? $item->url : (isset($item->slug) ? url('/post/' . $item->slug) : '#'),
            ];
        }
        return null;
    })->filter()->values();

    $ppostFeatured = $ppostLatestItems->first();
    $ppostRest     = $ppostLatestItems->slice(1)->take(9);
@endphp

@if($ppostLatestItems->isNotEmpty())
<section class="ppost-latest" dir="rtl" aria-labelledby="ppost-latest-title">
    <div class="ppost-latest__container">
        <div class="ppost-latest__header">
            <h2 id="ppost-latest-title" class="ppost-latest__title">
                <span class="ppost-latest__title-bar"></span>
                آخر الأخبار
            </h2>
            <a href="#" class="ppost-latest__more">عرض الكل
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
        </div>

        <div class="ppost-latest__grid">
            {{-- الخبر المميّز (الأكبر) --}}
            @if($ppostFeatured)
            <article class="ppost-latest__featured">
                <a href="{{ $ppostFeatured['url'] }}" class="ppost-latest__featured-link">
                    <div class="ppost-latest__featured-image-wrap">
                        <img src="{{ $ppostFeatured['image'] }}"
                             alt="{{ $ppostFeatured['title'] }}"
                             class="ppost-latest__featured-image"
                             loading="lazy">
                        <span class="ppost-latest__badge ppost-latest__badge--featured">{{ $ppostFeatured['category'] }}</span>
                    </div>
                    <div class="ppost-latest__featured-body">
                        <h3 class="ppost-latest__featured-title">{{ $ppostFeatured['title'] }}</h3>
                        <p class="ppost-latest__featured-excerpt">{{ $ppostFeatured['excerpt'] }}</p>
                        <div class="ppost-latest__meta">
                            <span class="ppost-latest__meta-author">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                {{ $ppostFeatured['author'] }}
                            </span>
                            <span class="ppost-latest__meta-date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                {{ $ppostFeatured['date'] }}
                            </span>
                        </div>
                    </div>
                </a>
            </article>
            @endif

            {{-- الأخبار الفرعية --}}
            <div class="ppost-latest__sub-grid">
                @foreach($ppostRest as $item)
                    <article class="ppost-latest__card">
                        <a href="{{ $item['url'] }}" class="ppost-latest__card-link">
                            <div class="ppost-latest__card-image-wrap">
                                <img src="{{ $item['image'] }}"
                                     alt="{{ $item['title'] }}"
                                     class="ppost-latest__card-image"
                                     loading="lazy">
                                <span class="ppost-latest__badge">{{ $item['category'] }}</span>
                            </div>
                            <div class="ppost-latest__card-body">
                                <h4 class="ppost-latest__card-title">{{ $item['title'] }}</h4>
                                <div class="ppost-latest__meta ppost-latest__meta--sm">
                                    <span class="ppost-latest__meta-date">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        {{ $item['date'] }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<style>
    .ppost-latest {
        background-color: #f7f8fa;
        padding: 48px 0;
        direction: rtl;
        font-family: inherit;
    }

    .ppost-latest__container {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .ppost-latest__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e5e7eb;
    }

    .ppost-latest__title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ppost-latest__title-bar {
        display: inline-block;
        width: 6px;
        height: 32px;
        background: linear-gradient(180deg, #e94560, #c81d3f);
        border-radius: 3px;
    }

    .ppost-latest__more {
        color: #e94560;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border: 1.5px solid #e94560;
        border-radius: 999px;
        transition: all 0.25s ease;
    }

    .ppost-latest__more:hover {
        background-color: #e94560;
        color: #ffffff;
        gap: 10px;
    }

    .ppost-latest__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 28px;
        align-items: start;
    }

    /* الخبر المميّز */
    .ppost-latest__featured {
        background-color: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .ppost-latest__featured:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
    }

    .ppost-latest__featured-link {
        display: grid;
        grid-template-columns: 1.25fr 1fr;
        color: inherit;
        text-decoration: none;
    }

    .ppost-latest__featured-image-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background-color: #e5e7eb;
    }

    .ppost-latest__featured-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .ppost-latest__featured:hover .ppost-latest__featured-image {
        transform: scale(1.05);
    }

    .ppost-latest__featured-body {
        padding: 28px 32px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .ppost-latest__featured-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: #1a1a2e;
        margin: 0 0 14px;
        line-height: 1.4;
        transition: color 0.25s ease;
    }

    .ppost-latest__featured:hover .ppost-latest__featured-title {
        color: #e94560;
    }

    .ppost-latest__featured-excerpt {
        font-size: 1rem;
        color: #4b5563;
        line-height: 1.75;
        margin: 0 0 18px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* شارة التصنيف */
    .ppost-latest__badge {
        position: absolute;
        top: 14px;
        right: 14px;
        background-color: rgba(233, 69, 96, 0.95);
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 4px;
        letter-spacing: 0.3px;
        backdrop-filter: blur(6px);
    }

    .ppost-latest__badge--featured {
        font-size: 0.8rem;
        padding: 7px 14px;
    }

    /* meta */
    .ppost-latest__meta {
        display: flex;
        gap: 18px;
        font-size: 0.85rem;
        color: #6b7280;
        align-items: center;
    }

    .ppost-latest__meta--sm {
        font-size: 0.75rem;
        gap: 12px;
    }

    .ppost-latest__meta-author,
    .ppost-latest__meta-date {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* شبكة الأخبار الفرعية */
    .ppost-latest__sub-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    .ppost-latest__card {
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .ppost-latest__card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .ppost-latest__card-link {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .ppost-latest__card-image-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background-color: #e5e7eb;
    }

    .ppost-latest__card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .ppost-latest__card:hover .ppost-latest__card-image {
        transform: scale(1.06);
    }

    .ppost-latest__card-body {
        padding: 14px 16px 16px;
    }

    .ppost-latest__card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0 0 10px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.25s ease;
    }

    .ppost-latest__card:hover .ppost-latest__card-title {
        color: #e94560;
    }

    /* التجاوب */
    @media (max-width: 992px) {
        .ppost-latest__sub-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .ppost-latest__featured-link {
            grid-template-columns: 1fr;
        }
        .ppost-latest__featured-title {
            font-size: 1.35rem;
        }
    }

    @media (max-width: 640px) {
        .ppost-latest {
            padding: 32px 0;
        }
        .ppost-latest__header {
            flex-wrap: wrap;
            gap: 12px;
        }
        .ppost-latest__title {
            font-size: 1.4rem;
        }
        .ppost-latest__title-bar {
            height: 26px;
        }
        .ppost-latest__sub-grid {
            grid-template-columns: 1fr;
        }
        .ppost-latest__featured-body {
            padding: 18px 20px 22px;
        }
        .ppost-latest__featured-title {
            font-size: 1.15rem;
        }
        .ppost-latest__featured-excerpt {
            font-size: 0.9rem;
        }
    }
</style>
