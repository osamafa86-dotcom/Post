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

## ربط الشريط بقاعدة البيانات

الشريط يدعم **ثلاث حالات تلقائياً**:

1. **تمرير صريح من Controller / Livewire Component**:
   ```php
   @include('components.layouts.main.palestine_post.breaking-news', [
       'breakingNews' => \App\Models\Post::where('is_breaking', true)
           ->latest()->take(10)->get(['title', 'slug'])
           ->map(fn($p) => ['title' => $p->title, 'url' => route('post.show', $p->slug)]),
   ])
   ```

2. **الجلب التلقائي من جدول `breaking_news`** (إن وُجد الموديل `App\Models\BreakingNews`):
   - يحترم عمود `is_active` إن وُجد.
   - يحترم عمود `published_at` إن وُجد.
   - يجلب أحدث 15 خبر.

3. **المصفوفة الافتراضية** كـ fallback إذا لم يُمرَّر شيء ولم يُعثر على بيانات.

### الأشكال المدعومة لكل عنصر
- نص بسيط: `'عنوان الخبر'`
- مصفوفة: `['title' => '...', 'url' => '...']`
- كائن: `$model->title`, `$model->url`

إذا تم توفير `url` فإن العنصر سيظهر كرابط قابل للنقر.

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
