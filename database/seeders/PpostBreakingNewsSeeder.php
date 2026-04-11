<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

/**
 * PpostBreakingNewsSeeder
 * ----------------------------------------------------------------
 * يَنقل العناوين العاجلة من:
 *   resources/views/components/layouts/main/palestine_post/breaking-news.blade.php
 * إلى جدول `breaking_news` في قاعدة البيانات.
 *
 * الطريقة:
 *   php artisan db:seed --class=Database\\Seeders\\PpostBreakingNewsSeeder
 *
 * الـ Seeder آمن ويُعيد الاستخدام (idempotent):
 *   - يستخدم العنوان كمفتاح فريد للـ upsert
 *   - يكتشف الأعمدة الموجودة في جدول `breaking_news` ويكتب فقط فيها
 */
class PpostBreakingNewsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('breaking_news')) {
            $this->command?->warn('[PpostBreakingNewsSeeder] جدول "breaking_news" غير موجود — تم التخطّي.');
            return;
        }

        $cols = Schema::getColumnListing('breaking_news');
        $now  = Carbon::now();

        $items = $this->items();
        $inserted = 0;
        $updated  = 0;

        foreach ($items as $index => $title) {
            $publishedAt = $now->copy()->subMinutes(($index + 1) * 10);

            $data = [];

            $this->set($data, $cols, 'title',        $title);
            $this->set($data, $cols, 'text',         $title);
            $this->set($data, $cols, 'headline',     $title);
            $this->set($data, $cols, 'content',      $title);
            $this->set($data, $cols, 'body',         $title);

            $this->set($data, $cols, 'url',          null);
            $this->set($data, $cols, 'link',         null);

            $this->set($data, $cols, 'is_active',    1);
            $this->set($data, $cols, 'active',       1);
            $this->set($data, $cols, 'status',       'published');

            $this->set($data, $cols, 'order',        $index + 1);
            $this->set($data, $cols, 'sort_order',   $index + 1);
            $this->set($data, $cols, 'position',     $index + 1);

            $this->set($data, $cols, 'lang',         'ar');
            $this->set($data, $cols, 'language',     'ar');
            $this->set($data, $cols, 'locale',       'ar');

            $this->set($data, $cols, 'published_at', $publishedAt);
            $this->set($data, $cols, 'created_at',   $publishedAt);
            $this->set($data, $cols, 'updated_at',   $publishedAt);

            $titleColumn = $this->firstExistingColumn($cols, ['title', 'text', 'headline']);

            if ($titleColumn === null) {
                DB::table('breaking_news')->insert($data);
                $inserted++;
                continue;
            }

            $exists = DB::table('breaking_news')->where($titleColumn, $title)->exists();
            if ($exists) {
                DB::table('breaking_news')->where($titleColumn, $title)->update($data);
                $updated++;
            } else {
                DB::table('breaking_news')->insert($data);
                $inserted++;
            }
        }

        $this->command?->info("[PpostBreakingNewsSeeder] تمّ: {$inserted} إدراج، {$updated} تحديث (إجمالي ".count($items).").");
    }

    private function set(array &$data, array $cols, string $column, $value): void
    {
        if (in_array($column, $cols, true)) {
            $data[$column] = $value;
        }
    }

    private function firstExistingColumn(array $cols, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (in_array($c, $cols, true)) {
                return $c;
            }
        }
        return null;
    }

    private function items(): array
    {
        return [
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
