<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

/**
 * PpostNewsSeeder
 * ----------------------------------------------------------------
 * يَنقل البيانات الافتراضية الموجودة في:
 *   resources/views/components/layouts/main/palestine_post/latest-news.blade.php
 * إلى جدول `posts` في قاعدة البيانات.
 *
 * الطريقة:
 *   php artisan db:seed --class=Database\\Seeders\\PpostNewsSeeder
 *
 * الـ Seeder آمن ويُعيد الاستخدام (idempotent):
 *   - يستخدم العنوان كمفتاح فريد للـ upsert
 *   - يكتشف الأعمدة الموجودة في جدول `posts` ويكتب فقط فيها
 *   - يدعم مسمّيات مختلفة للأعمدة (image/featured_image/cover, content/body/description ...)
 */
class PpostNewsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('posts')) {
            $this->command?->warn('[PpostNewsSeeder] جدول "posts" غير موجود — تم التخطّي.');
            return;
        }

        $cols = Schema::getColumnListing('posts');
        $now  = Carbon::now();

        $items = $this->items();
        $inserted = 0;
        $updated  = 0;

        foreach ($items as $item) {
            $publishedAt = $now->copy()->subMinutes((int) $item['minutes_ago']);
            $slug        = Str::slug($item['title'], '-', null);

            $data = [];

            // Title / slug
            $this->set($data, $cols, 'title',          $item['title']);
            $this->set($data, $cols, 'slug',           $slug);
            $this->set($data, $cols, 'name',           $item['title']);     // بعض الجداول تستخدم name
            $this->set($data, $cols, 'post_title',     $item['title']);

            // Short text
            $this->set($data, $cols, 'excerpt',        $item['excerpt']);
            $this->set($data, $cols, 'summary',        $item['excerpt']);
            $this->set($data, $cols, 'short_desc',     $item['excerpt']);
            $this->set($data, $cols, 'description',    $item['excerpt']);

            // Body (نضع الـ excerpt كمحتوى مبدئي إذا لم يتوفّر محتوى كامل)
            $this->set($data, $cols, 'content',        $item['excerpt']);
            $this->set($data, $cols, 'body',           $item['excerpt']);
            $this->set($data, $cols, 'post_content',   $item['excerpt']);
            $this->set($data, $cols, 'text',           $item['excerpt']);

            // Image
            $this->set($data, $cols, 'image',          $item['image']);
            $this->set($data, $cols, 'featured_image', $item['image']);
            $this->set($data, $cols, 'cover',          $item['image']);
            $this->set($data, $cols, 'cover_image',    $item['image']);
            $this->set($data, $cols, 'thumbnail',      $item['image']);
            $this->set($data, $cols, 'photo',          $item['image']);

            // Category as string (إذا كان العمود موجوداً)
            $this->set($data, $cols, 'category',       $item['category']);
            $this->set($data, $cols, 'category_name',  $item['category']);
            $this->set($data, $cols, 'section',        $item['category']);

            // Author as string
            $this->set($data, $cols, 'author',         $item['author']);
            $this->set($data, $cols, 'author_name',    $item['author']);
            $this->set($data, $cols, 'writer',         $item['author']);

            // Status / publish flags
            $this->set($data, $cols, 'publish_status', 1);
            $this->set($data, $cols, 'status',         'published');
            $this->set($data, $cols, 'state',          'published');
            $this->set($data, $cols, 'is_published',   1);
            $this->set($data, $cols, 'is_active',      1);
            $this->set($data, $cols, 'active',         1);
            $this->set($data, $cols, 'visible',        1);

            // Language
            $this->set($data, $cols, 'lang',           'ar');
            $this->set($data, $cols, 'language',       'ar');
            $this->set($data, $cols, 'locale',         'ar');

            // Timestamps
            $this->set($data, $cols, 'publish_date',   $publishedAt);
            $this->set($data, $cols, 'published_at',   $publishedAt);
            $this->set($data, $cols, 'created_at',     $publishedAt);
            $this->set($data, $cols, 'updated_at',     $publishedAt);

            // Upsert باستخدام العنوان كمُعرِّف
            $titleColumn = $this->firstExistingColumn($cols, ['title', 'name', 'post_title']);

            if ($titleColumn === null) {
                DB::table('posts')->insert($data);
                $inserted++;
                continue;
            }

            $exists = DB::table('posts')->where($titleColumn, $item['title'])->exists();
            if ($exists) {
                DB::table('posts')->where($titleColumn, $item['title'])->update($data);
                $updated++;
            } else {
                DB::table('posts')->insert($data);
                $inserted++;
            }
        }

        $this->command?->info("[PpostNewsSeeder] تمّ: {$inserted} إدراج، {$updated} تحديث (إجمالي ".count($items).").");
    }

    /**
     * يُسجّل القيمة في الـ payload فقط إذا كان العمود موجوداً في الجدول.
     */
    private function set(array &$data, array $cols, string $column, $value): void
    {
        if (in_array($column, $cols, true)) {
            $data[$column] = $value;
        }
    }

    /**
     * يُعيد أوّل عمود موجود من مجموعة الأسماء المُحتملة.
     */
    private function firstExistingColumn(array $cols, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (in_array($c, $cols, true)) {
                return $c;
            }
        }
        return null;
    }

    /**
     * قائمة الأخبار المأخوذة من latest-news.blade.php
     */
    private function items(): array
    {
        return [
            [
                'title'       => 'مجلس الأمن يعقد جلسة طارئة لبحث تطوّرات الأوضاع الإنسانية في غزة',
                'excerpt'     => 'يعقد مجلس الأمن الدولي اليوم جلسة طارئة لمناقشة التطوّرات الأخيرة في قطاع غزة بعد تصاعد التحذيرات الأممية من تدهور الوضع الإنساني، وسط مطالبات دولية بفتح ممرّات آمنة لإدخال المساعدات.',
                'image'       => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?w=800&h=500&fit=crop',
                'category'    => 'سياسة',
                'author'      => 'هيئة التحرير',
                'minutes_ago' => 30,
            ],
            [
                'title'       => 'البنك الدولي يُقرّ منحة طارئة بقيمة 200 مليون دولار للأراضي الفلسطينية',
                'excerpt'     => 'أعلن البنك الدولي عن تقديم منحة طارئة بقيمة 200 مليون دولار لدعم الاقتصاد الفلسطيني وإعادة إعمار البنى التحتية المتضرّرة في قطاع غزة.',
                'image'       => 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?w=800&h=500&fit=crop',
                'category'    => 'اقتصاد',
                'author'      => 'سامر يوسف',
                'minutes_ago' => 120,
            ],
            [
                'title'       => 'قافلة مساعدات إنسانية دولية جديدة تدخل غزة عبر معبر رفح',
                'excerpt'     => 'دخلت قافلة مساعدات إنسانية تضمّ 120 شاحنة محمّلة بالغذاء والأدوية إلى قطاع غزة عبر معبر رفح، بعد مفاوضات ماراثونية أشرفت عليها الأمم المتحدة.',
                'image'       => 'https://images.unsplash.com/photo-1593113598332-cd288d649433?w=800&h=500&fit=crop',
                'category'    => 'إنسانية',
                'author'      => 'محمد أبو سيف',
                'minutes_ago' => 180,
            ],
            [
                'title'       => 'المنتخب الفلسطيني يتأهّل تاريخياً إلى دور المجموعات في كأس آسيا 2026',
                'excerpt'     => 'حقّق المنتخب الفلسطيني لكرة القدم إنجازاً تاريخياً بتأهّله إلى دور المجموعات في بطولة كأس آسيا 2026، بعد فوزٍ مستحق في المباراة الفاصلة.',
                'image'       => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=800&h=500&fit=crop',
                'category'    => 'رياضة',
                'author'      => 'طارق حمدان',
                'minutes_ago' => 300,
            ],
            [
                'title'       => 'جامعة بيرزيت تفتتح أوّل مختبر متخصّص في الذكاء الاصطناعي في فلسطين',
                'excerpt'     => 'افتتحت جامعة بيرزيت أوّل مختبر متخصّص للذكاء الاصطناعي في فلسطين، ويهدف إلى تأهيل الكوادر الأكاديمية وإطلاق مشاريع بحثية مشتركة مع جامعات دولية.',
                'image'       => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&h=500&fit=crop',
                'category'    => 'تعليم',
                'author'      => 'نور عبد الله',
                'minutes_ago' => 420,
            ],
            [
                'title'       => 'افتتاح مستشفى ميداني دولي في خان يونس بطاقة 500 سرير',
                'excerpt'     => 'افتُتح في مدينة خان يونس جنوب قطاع غزة مستشفى ميداني دولي جديد بطاقة استيعاب 500 سرير، بالتعاون مع منظمة الصحة العالمية وعدد من الدول المانحة.',
                'image'       => 'https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?w=800&h=500&fit=crop',
                'category'    => 'صحة',
                'author'      => 'د. هبة ناصر',
                'minutes_ago' => 540,
            ],
            [
                'title'       => 'انطلاق فعاليات معرض الكتاب الفلسطيني الدولي الثاني عشر في رام الله',
                'excerpt'     => 'انطلقت في مدينة رام الله فعاليات الدورة الثانية عشرة من معرض الكتاب الفلسطيني الدولي بمشاركة أكثر من 300 دار نشر من 25 دولة.',
                'image'       => 'https://images.unsplash.com/photo-1578321272176-b7bbc0679853?w=800&h=500&fit=crop',
                'category'    => 'ثقافة',
                'author'      => 'رنا إبراهيم',
                'minutes_ago' => 660,
            ],
            [
                'title'       => 'شركات ناشئة فلسطينية تتصدّر قائمة "فوربس" للابتكار في الشرق الأوسط',
                'excerpt'     => 'أدرجت مجلة "فوربس" خمس شركات ناشئة فلسطينية ضمن قائمتها السنوية لأبرز شركات الابتكار في منطقة الشرق الأوسط لعام 2026.',
                'image'       => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&h=500&fit=crop',
                'category'    => 'تكنولوجيا',
                'author'      => 'عمر الشريف',
                'minutes_ago' => 780,
            ],
            [
                'title'       => 'القدس: إطلاق مشروع لترميم المباني التاريخية في البلدة القديمة',
                'excerpt'     => 'أطلقت مؤسسات فلسطينية بالشراكة مع منظمة اليونسكو مشروعاً لترميم عشرات المباني التاريخية في البلدة القديمة بالقدس، في إطار جهود الحفاظ على الهوية الثقافية.',
                'image'       => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=800&h=500&fit=crop',
                'category'    => 'القدس',
                'author'      => 'ليلى خالد',
                'minutes_ago' => 900,
            ],
            [
                'title'       => 'توقيع اتفاقية تعاون ثقافي شاملة بين فلسطين وعدد من الدول العربية',
                'excerpt'     => 'وقّعت وزارة الثقافة الفلسطينية اتفاقية تعاون ثقافي مع ست دول عربية تتضمّن تبادل الخبرات وتنظيم مهرجانات مشتركة ودعم الفنانين الفلسطينيين.',
                'image'       => 'https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?w=800&h=500&fit=crop',
                'category'    => 'ثقافة',
                'author'      => 'هيئة التحرير',
                'minutes_ago' => 1080,
            ],
        ];
    }
}
