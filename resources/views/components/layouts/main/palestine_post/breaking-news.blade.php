{{--
    شريط الأخبار العاجلة - ppost
    طريقة الاستخدام في header.blade.php:
    @include('components.layouts.main.palestine_post.breaking-news')

    لتحديث الأخبار: عدّل المصفوفة $breakingNews أدناه
    أو اربطها بقاعدة البيانات/الـ Model الخاص بك.
--}}

@php
    // يمكنك استبدال هذه المصفوفة بجلب من قاعدة البيانات
    // مثال: $breakingNews = \App\Models\Post::where('is_breaking', true)->latest()->take(10)->pluck('title')->toArray();
    $breakingNews = $breakingNews ?? [
        'انطلاق قمة اقتصادية دولية لمناقشة الأزمات المالية العالمية',
        'ارتفاع ملحوظ في أسعار النفط بعد قرارات أوبك الأخيرة',
        'فوز المنتخب الوطني في المباراة النهائية للبطولة القارية',
        'إطلاق قمر صناعي جديد لأغراض الاتصالات والبحث العلمي',
        'توقيع اتفاقية تعاون استراتيجي بين عدة دول لمواجهة التغير المناخي',
    ];
@endphp

<div class="ppost-breaking-news" dir="rtl" aria-live="polite">
    <div class="ppost-breaking-news__container">
        <span class="ppost-breaking-news__label">
            <span class="ppost-breaking-news__dot"></span>
            عاجل
        </span>
        <div class="ppost-breaking-news__wrapper">
            <ul class="ppost-breaking-news__list">
                @foreach($breakingNews as $news)
                    <li class="ppost-breaking-news__item">{{ $news }}</li>
                @endforeach
                {{-- تكرار العناصر لتمرير سلس بدون فراغات --}}
                @foreach($breakingNews as $news)
                    <li class="ppost-breaking-news__item" aria-hidden="true">{{ $news }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

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
