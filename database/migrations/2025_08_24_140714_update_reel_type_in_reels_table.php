<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود مؤقت رقمي إن لم يكن موجوداً
        if (!Schema::hasColumn('reels', 'reel_type_int')) {
            if (Schema::hasColumn('reels', 'reel_type')) {
                Schema::table('reels', function (Blueprint $table) {
                    $table->unsignedBigInteger('reel_type_int')->nullable()->after('reel_type');
                });
            } else {
                Schema::table('reels', function (Blueprint $table) {
                    $table->unsignedBigInteger('reel_type_int')->nullable();
                });
            }
        }

        // 2) حوّل القيم النصية إلى أرقام (لو العمود النصي موجود)
        if (Schema::hasColumn('reels', 'reel_type')) {
            DB::statement("
                UPDATE `reels` SET `reel_type_int` =
                    CASE TRIM(`reel_type`)
                        WHEN 'يوتيوب'   THEN 1
                        WHEN 'انستغرام' THEN 2
                        ELSE `reel_type_int`
                    END
            ");
        }

        // 3) احذف العمود القديم النصّي لو موجود
        if (Schema::hasColumn('reels', 'reel_type')) {
            Schema::table('reels', function (Blueprint $table) {
                $table->dropColumn('reel_type');
            });
        }

        // 4) أعد تسمية المؤقت للاسم الأصلي
        if (Schema::hasColumn('reels', 'reel_type_int')) {
            Schema::table('reels', function (Blueprint $table) {
                $table->renameColumn('reel_type_int', 'reel_type');
            });
        }

        // 5) ثبّت النوع والافتراضي (اختياري لكنه مُستحسن)
        if (Schema::hasColumn('reels', 'reel_type')) {
            Schema::table('reels', function (Blueprint $table) {
                $table->unsignedBigInteger('reel_type')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('reels', 'reel_type_text')) {
            Schema::table('reels', function (Blueprint $table) {
                $table->string('reel_type_text')->nullable()->after('reel_type');
            });
        }

        // 2) رجّع الأرقام إلى نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('reels', 'reel_type')) {
            DB::statement("
                UPDATE `reels` SET `reel_type_text` =
                    CASE `reel_type`
                        WHEN 1 THEN 'يوتيوب'
                        WHEN 2 THEN 'انستغرام'
                        ELSE `reel_type_text`
                    END
            ");
        }

        // 3) احذف العمود الرقمي وأعد التسمية للنصي
        if (Schema::hasColumn('reels', 'reel_type')) {
            Schema::table('reels', function (Blueprint $table) {
                $table->dropColumn('reel_type');
            });
        }

        if (Schema::hasColumn('reels', 'reel_type_text')) {
            Schema::table('reels', function (Blueprint $table) {
                $table->renameColumn('reel_type_text', 'reel_type');
            });
        }
    }
};
