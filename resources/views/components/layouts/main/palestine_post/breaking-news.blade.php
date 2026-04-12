{{--
    شريط الأخبار العاجلة - ppost
    طريقة الاستخدام في header.blade.php:
    @include('components.layouts.main.palestine_post.breaking-news')

    لتحديث الأخبار: عدّل المصفوفة $breakingNews أدناه
    أو اربطها بقاعدة البيانات/الـ Model الخاص بك.
--}}

@php
    /*
    |--------------------------------------------------------------------------
    | مصدر بيانات شريط الأخبار العاجلة
    |--------------------------------------------------------------------------
    | يدعم الشريط ثلاث حالات:
    |   1. إذا مُرّر $breakingNews من الـ Controller/Livewire Component يستخدمه.
    |   2. وإلا يحاول الجلب من جدول breaking_news (إن وُجد الموديل).
    |   3. وإلا يعود لمصفوفة افتراضية كـ fallback.
    |
    | كل عنصر يمكن أن يكون:
    |   - نص بسيط (string)
    |   - مصفوفة أو كائن يحتوي على: title, url (اختياري)
    */

    if (! isset($breakingNews)) {
        $breakingNews = null;

        if (class_exists(\App\Models\BreakingNews::class)) {
            try {
                $breakingNews = \App\Models\BreakingNews::query()
                    ->when(
                        \Illuminate\Support\Facades\Schema::hasColumn('breaking_news', 'is_active'),
                        fn ($q) => $q->where('is_active', true)
                    )
                    ->when(
                        \Illuminate\Support\Facades\Schema::hasColumn('breaking_news', 'publish_date'),
                        fn ($q) => $q->whereNotNull('publish_date')->where('publish_date', '<=', now())
                    )
                    ->when(
                        ! \Illuminate\Support\Facades\Schema::hasColumn('breaking_news', 'publish_date') && \Illuminate\Support\Facades\Schema::hasColumn('breaking_news', 'published_at'),
                        fn ($q) => $q->whereNotNull('published_at')->where('published_at', '<=', now())
                    )
                    ->latest()
                    ->take(15)
                    ->get();
            } catch (\Throwable $e) {
                $breakingNews = null;
            }
        }

        if ($breakingNews === null || (is_countable($breakingNews) && count($breakingNews) === 0)) {
            $breakingNews = [
                'مجلس الأمن يعقد جلسة طارئة لبحث تطوّرات الأوضاع الإنسانية في غزة',
                'البنك الدولي يُقرّ منحة طارئة بقيمة 200 مليون دولار للأراضي الفلسطينية',
                'قافلة مساعدات إنسانية جديدة تضمّ 120 شاحنة تدخل غزة عبر معبر رفح',
                'المنتخب الفلسطيني يتأهّل تاريخياً إلى دور المجموعات في كأس آسيا 2026',
                'جامعة بيرزيت تفتتح أوّل مختبر متخصّص للذكاء الاصطناعي في فلسطين',
                'افتتاح مستشفى ميداني دولي في خان يونس بطاقة 500 سرير',
                'انطلاق فعاليات معرض الكتاب الفلسطيني الدولي الثاني عشر في رام الله',
                'شركات ناشئة فلسطينية تتصدّر قائمة "فوربس" للابتكار في الشرق الأوسط',
                'إطلاق مشروع دولي لترميم المباني التاريخية في البلدة القديمة بالقدس',
                'توقيع اتفاقية تعاون ثقافي شاملة بين فلسطين وعدد من الدول العربية',
            ];
        }
    }

    // تطبيع العناصر إلى: ['title' => ..., 'url' => ...]
    $ppostBreakingItems = collect($breakingNews)->map(function ($item) {
        if (is_string($item)) {
            return ['title' => $item, 'url' => null];
        }
        if (is_array($item)) {
            return [
                'title' => $item['title'] ?? ($item['text'] ?? ''),
                'url'   => $item['url']   ?? ($item['link'] ?? null),
            ];
        }
        if (is_object($item)) {
            return [
                'title' => $item->title ?? ($item->text ?? ''),
                'url'   => $item->url   ?? (method_exists($item, 'getUrlAttribute') ? $item->url : null),
            ];
        }
        return ['title' => (string) $item, 'url' => null];
    })->filter(fn ($i) => ! empty($i['title']))->values();
@endphp

@if($ppostBreakingItems->isNotEmpty())
<div class="ppost-breaking-news" dir="rtl" aria-live="polite" role="region" aria-label="شريط الأخبار العاجلة">
    <div class="ppost-breaking-news__container">
        <span class="ppost-breaking-news__label">
            <span class="ppost-breaking-news__dot"></span>
            عاجل
        </span>
        <div class="ppost-breaking-news__wrapper">
            <ul class="ppost-breaking-news__list">
                @foreach($ppostBreakingItems as $news)
                    <li class="ppost-breaking-news__item">
                        @if(!empty($news['url']))
                            <a href="{{ $news['url'] }}" class="ppost-breaking-news__link">{{ $news['title'] }}</a>
                        @else
                            {{ $news['title'] }}
                        @endif
                    </li>
                @endforeach
                {{-- تكرار العناصر لتمرير سلس بدون فراغات --}}
                @foreach($ppostBreakingItems as $news)
                    <li class="ppost-breaking-news__item" aria-hidden="true">
                        @if(!empty($news['url']))
                            <a href="{{ $news['url'] }}" class="ppost-breaking-news__link" tabindex="-1">{{ $news['title'] }}</a>
                        @else
                            {{ $news['title'] }}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<style>
    .ppost-breaking-news {
        background-color: #ffffff;
        border-top: 2px solid #e94560;
        border-bottom: 2px solid #e94560;
        overflow: hidden;
        width: 100%;
        direction: rtl;
        font-family: inherit;
        position: relative;
        z-index: 10;
    }

    .ppost-breaking-news__container {
        display: flex;
        align-items: center;
        max-width: 100%;
        margin: 0 auto;
    }

    .ppost-breaking-news__label {
        background-color: #e94560;
        color: #ffffff;
        font-weight: 700;
        font-size: 1rem;
        padding: 12px 22px;
        flex-shrink: 0;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 1px;
        position: relative;
    }

    .ppost-breaking-news__label::after {
        content: "";
        position: absolute;
        left: -12px;
        top: 50%;
        transform: translateY(-50%);
        border-top: 12px solid transparent;
        border-bottom: 12px solid transparent;
        border-right: 12px solid #e94560;
    }

    .ppost-breaking-news__dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: #ffffff;
        border-radius: 50%;
        animation: ppost-pulse 1.2s ease-in-out infinite;
    }

    @keyframes ppost-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.35); }
    }

    .ppost-breaking-news__wrapper {
        flex: 1;
        overflow: hidden;
        position: relative;
        height: 44px;
        margin-right: 15px;
    }

    .ppost-breaking-news__list {
        list-style: none;
        display: flex;
        gap: 60px;
        white-space: nowrap;
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        align-items: center;
        padding: 0;
        margin: 0;
        animation: ppost-ticker 60s linear infinite;
    }

    .ppost-breaking-news:hover .ppost-breaking-news__list {
        animation-play-state: paused;
    }

    .ppost-breaking-news__item {
        font-size: 1rem;
        color: #1a1a2e;
        font-weight: 500;
        position: relative;
        padding: 0;
    }

    .ppost-breaking-news__link {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .ppost-breaking-news__link:hover,
    .ppost-breaking-news__link:focus {
        color: #e94560;
        text-decoration: underline;
    }

    .ppost-breaking-news__item::after {
        content: "●";
        color: #e94560;
        margin-right: 30px;
        font-size: 0.65rem;
        vertical-align: middle;
    }

    @keyframes ppost-ticker {
        0% { transform: translateX(-50%); }
        100% { transform: translateX(50%); }
    }

    @media (max-width: 768px) {
        .ppost-breaking-news__label {
            font-size: 0.85rem;
            padding: 10px 14px;
        }
        .ppost-breaking-news__item {
            font-size: 0.9rem;
        }
        .ppost-breaking-news__wrapper {
            height: 40px;
        }
    }
</style>
