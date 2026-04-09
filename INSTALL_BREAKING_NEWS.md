# دليل تركيب شريط الأخبار العاجلة - ppost

## الملف المُضاف
`resources/views/components/layouts/main/palestine_post/breaking-news.blade.php`

## خطوات التفعيل

### 1. افتح الملف:
```
resources/views/components/layouts/main/palestine_post/header.blade.php
```

### 2. أضف هذا السطر في المكان المناسب داخل `header.blade.php`:

في آخر الملف، بعد إغلاق النافبار/القائمة الرئيسية مباشرة (قبل إغلاق `</header>` إن وجد):

```blade
@include('components.layouts.main.palestine_post.breaking-news')
```

### 3. مسح الكاش (إذا احتجت):
```bash
php artisan view:clear
php artisan cache:clear
```

## ربط الشريط بقاعدة البيانات (اختياري)

افتح ملف `breaking-news.blade.php` واستبدل مصفوفة `$breakingNews` بجلب من الـ Model:

```php
@php
    $breakingNews = \App\Models\Post::where('is_breaking', true)
        ->latest()
        ->take(10)
        ->pluck('title')
        ->toArray();
@endphp
```

أو مرّر الأخبار من Controller/Livewire Component.

## تخصيص الألوان

افتح `breaking-news.blade.php` وعدّل:
- `#e94560` → لون شريط "عاجل" والحدود
- `#ffffff` → لون الخلفية
- `#1a1a2e` → لون نص الأخبار
- `60s` في `animation` → سرعة التمرير (أقل = أسرع)

## مميزات الشريط
- تمرير أفقي تلقائي وسلس
- يتوقف التمرير عند hover
- نقطة نابضة بجانب كلمة "عاجل"
- دعم كامل RTL
- تصميم متجاوب للموبايل
- CSS محتوى داخل الملف نفسه (لا يحتاج ملفات خارجية)
