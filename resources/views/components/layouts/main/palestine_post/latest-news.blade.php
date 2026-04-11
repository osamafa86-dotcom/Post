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
        <div class="ppost-latest__grid">

            {{-- الشريط الجانبي (يظهر على اليمين في RTL) --}}
            <aside class="ppost-latest__sidebar">
                <h2 id="ppost-latest-title" class="ppost-latest__sidebar-heading">
                    <span class="ppost-latest__sidebar-dot"></span>
                    آخر الأخبار
                </h2>
                <ol class="ppost-latest__sidebar-list">
                    @foreach($ppostRest as $index => $item)
                        <li class="ppost-latest__sidebar-item">
                            <span class="ppost-latest__sidebar-num">{{ sprintf('%02d', $index + 1) }}</span>
                            <div class="ppost-latest__sidebar-content">
                                <a href="{{ $item['url'] }}" class="ppost-latest__sidebar-link">{{ $item['title'] }}</a>
                                <span class="ppost-latest__sidebar-date">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    {{ $item['date'] }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </aside>

            {{-- الخبر المميّز (يظهر على اليسار في RTL) --}}
            @if($ppostFeatured)
            <article class="ppost-latest__featured">
                <a href="{{ $ppostFeatured['url'] }}" class="ppost-latest__featured-link">
                    <div class="ppost-latest__featured-body">
                        <div class="ppost-latest__featured-meta">
                            <span class="ppost-latest__featured-category">{{ $ppostFeatured['category'] }}</span>
                            <span class="ppost-latest__featured-date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                {{ $ppostFeatured['date'] }}
                            </span>
                        </div>
                        <h3 class="ppost-latest__featured-title">{{ $ppostFeatured['title'] }}</h3>
                        <p class="ppost-latest__featured-excerpt">{{ $ppostFeatured['excerpt'] }}</p>
                    </div>
                    <div class="ppost-latest__featured-image-wrap">
                        <img src="{{ $ppostFeatured['image'] }}"
                             alt="{{ $ppostFeatured['title'] }}"
                             class="ppost-latest__featured-image"
                             loading="lazy">
                    </div>
                </a>
            </article>
            @endif

        </div>
    </div>
</section>
@endif

<style>
    .ppost-latest {
        background-color: #f5f6f8;
        padding: 40px 0;
        direction: rtl;
        font-family: inherit;
    }

    .ppost-latest__container {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .ppost-latest__grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
        gap: 24px;
        align-items: stretch;
    }

    /* =====================================================
       الشريط الجانبي (right column in RTL)
       ===================================================== */
    .ppost-latest__sidebar {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
    }

    .ppost-latest__sidebar-heading {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1a1f36;
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #0ea5b7;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        align-self: flex-start;
    }

    .ppost-latest__sidebar-dot {
        display: inline-block;
        width: 9px;
        height: 9px;
        background-color: #0ea5b7;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .ppost-latest__sidebar-list {
        list-style: none;
        padding: 0;
        margin: 0;
        flex: 1;
    }

    .ppost-latest__sidebar-item {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 14px;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px dashed #e5e7eb;
    }

    .ppost-latest__sidebar-item:first-child {
        padding-top: 2px;
    }

    .ppost-latest__sidebar-item:last-child {
        border-bottom: none;
        padding-bottom: 2px;
    }

    .ppost-latest__sidebar-num {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0ea5b7;
        line-height: 1;
        min-width: 32px;
        font-variant-numeric: tabular-nums;
    }

    .ppost-latest__sidebar-content {
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 0;
    }

    .ppost-latest__sidebar-link {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a1f36;
        line-height: 1.55;
        text-decoration: none;
        transition: color 0.2s ease;
        display: block;
    }

    .ppost-latest__sidebar-link:hover,
    .ppost-latest__sidebar-link:focus {
        color: #0ea5b7;
    }

    .ppost-latest__sidebar-date {
        font-size: 0.75rem;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* =====================================================
       الخبر المميّز (left column in RTL)
       ===================================================== */
    .ppost-latest__featured {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }

    .ppost-latest__featured:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .ppost-latest__featured-link {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        color: inherit;
        text-decoration: none;
        height: 100%;
    }

    .ppost-latest__featured-body {
        padding: 28px 32px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 14px;
    }

    .ppost-latest__featured-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .ppost-latest__featured-category {
        color: #0ea5b7;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .ppost-latest__featured-category::before {
        content: "";
        width: 8px;
        height: 8px;
        background-color: #0ea5b7;
        border-radius: 50%;
        display: inline-block;
    }

    .ppost-latest__featured-date {
        font-size: 0.75rem;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ppost-latest__featured-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #e94560;
        margin: 0;
        line-height: 1.55;
        transition: color 0.25s ease;
    }

    .ppost-latest__featured:hover .ppost-latest__featured-title {
        color: #c81d3f;
    }

    .ppost-latest__featured-excerpt {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.85;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .ppost-latest__featured-image-wrap {
        position: relative;
        width: 100%;
        min-height: 320px;
        background-color: #e5e7eb;
        overflow: hidden;
    }

    .ppost-latest__featured-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .ppost-latest__featured:hover .ppost-latest__featured-image {
        transform: scale(1.04);
    }

    /* =====================================================
       التجاوب
       ===================================================== */
    @media (max-width: 992px) {
        .ppost-latest__grid {
            grid-template-columns: 1fr;
        }
        .ppost-latest__featured-link {
            grid-template-columns: 1fr;
        }
        .ppost-latest__featured-image-wrap {
            min-height: 0;
            aspect-ratio: 16 / 9;
        }
        .ppost-latest__featured-body {
            padding: 24px 24px 26px;
        }
    }

    @media (max-width: 640px) {
        .ppost-latest {
            padding: 28px 0;
        }
        .ppost-latest__sidebar {
            padding: 18px 18px;
        }
        .ppost-latest__featured-body {
            padding: 20px 20px 22px;
            gap: 12px;
        }
        .ppost-latest__featured-title {
            font-size: 1.1rem;
        }
        .ppost-latest__featured-excerpt {
            font-size: 0.85rem;
            -webkit-line-clamp: 3;
        }
        .ppost-latest__sidebar-num {
            font-size: 1.2rem;
            min-width: 28px;
        }
        .ppost-latest__sidebar-link {
            font-size: 0.9rem;
        }
        .ppost-latest__sidebar-heading {
            font-size: 1.05rem;
        }
    }
</style>
