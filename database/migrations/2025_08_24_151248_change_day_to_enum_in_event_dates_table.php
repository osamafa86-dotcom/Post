<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود رقمي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('event_dates', 'day_int')) {
            if (Schema::hasColumn('event_dates', 'day')) {
                Schema::table('event_dates', function (Blueprint $table) {
                    $table->unsignedBigInteger('day_int')->nullable()->after('day');
                });
            } else {
                Schema::table('event_dates', function (Blueprint $table) {
                    $table->unsignedBigInteger('day_int')->nullable();
                });
            }
        }

        // 2) حوّل النصوص → أرقام (لو العمود النصّي موجود)
        if (Schema::hasColumn('event_dates', 'day')) {
            DB::statement("
                UPDATE `event_dates` SET `day_int` =
                    CASE
                        WHEN TRIM(`day`) = 'السبت' THEN 1
                        WHEN TRIM(`day`) IN ('الاحد','الأحد') THEN 2
                        WHEN TRIM(`day`) IN ('الاثنين','الإثنين') THEN 3
                        WHEN TRIM(`day`) = 'الثلاثاء' THEN 4
                        WHEN TRIM(`day`) IN ('الاربعاء','الأربعاء') THEN 5
                        WHEN TRIM(`day`) = 'الخميس' THEN 6
                        WHEN TRIM(`day`) = 'الجمعة' THEN 7
                        ELSE `day_int`
                    END
            ");
        }

        // 3) احذف العمود النصّي القديم لو موجود
        if (Schema::hasColumn('event_dates', 'day')) {
            Schema::table('event_dates', function (Blueprint $table) {
                $table->dropColumn('day');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('event_dates', 'day_int')) {
            Schema::table('event_dates', function (Blueprint $table) {
                $table->renameColumn('day_int', 'day');
            });
        }

        // 5) (اختياري) ثبّت النوع NOT NULL
        if (Schema::hasColumn('event_dates', 'day')) {
            Schema::table('event_dates', function (Blueprint $table) {
                $table->unsignedBigInteger('day')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('event_dates', 'day_text')) {
            Schema::table('event_dates', function (Blueprint $table) {
                $table->string('day_text')->nullable()->after('day');
            });
        }

        // 2) الأرقام → نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('event_dates', 'day')) {
            DB::statement("
                UPDATE `event_dates` SET `day_text` =
                    CASE `day`
                        WHEN 1 THEN 'السبت'
                        WHEN 2 THEN 'الاحد'
                        WHEN 3 THEN 'الاثنين'
                        WHEN 4 THEN 'الثلاثاء'
                        WHEN 5 THEN 'الاربعاء'
                        WHEN 6 THEN 'الخميس'
                        WHEN 7 THEN 'الجمعة'
                        ELSE `day_text`
                    END
            ");
        }

        // 3) احذف الرقمي وأعد تسمية النصّي
        if (Schema::hasColumn('event_dates', 'day')) {
            Schema::table('event_dates', function (Blueprint $table) {
                $table->dropColumn('day');
            });
        }

        if (Schema::hasColumn('event_dates', 'day_text')) {
            Schema::table('event_dates', function (Blueprint $table) {
                $table->renameColumn('day_text', 'day');
            });
        }
    }
};
