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
        if (!Schema::hasColumn('social_media', 'position_int')) {
            if (Schema::hasColumn('social_media', 'position')) {
                Schema::table('social_media', function (Blueprint $table) {
                    $table->unsignedBigInteger('position_int')->nullable()->after('position');
                });
            } else {
                Schema::table('social_media', function (Blueprint $table) {
                    $table->unsignedBigInteger('position_int')->nullable();
                });
            }
        }

        // 2) حوّل القيم النصية إلى أرقام (لو العمود النصي موجود)
        if (Schema::hasColumn('social_media', 'position')) {
            DB::statement("
                UPDATE `social_media` SET `position_int` =
                    CASE `position`
                        WHEN 'header' THEN 1
                        WHEN 'footer' THEN 2
                        ELSE `position_int`
                    END
            ");
        }

        // 3) احذف العمود القديم النصّي لو موجود
        if (Schema::hasColumn('social_media', 'position')) {
            Schema::table('social_media', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }

        // 4) أعد تسمية المؤقت للاسم الأصلي
        if (Schema::hasColumn('social_media', 'position_int')) {
            Schema::table('social_media', function (Blueprint $table) {
                $table->renameColumn('position_int', 'position');
            });
        }

        // 5) ثبّت النوع والافتراضي (اختياري لكن مستحسن)
        if (Schema::hasColumn('social_media', 'position')) {
            Schema::table('social_media', function (Blueprint $table) {
                $table->unsignedBigInteger('position')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('social_media', 'position_text')) {
            Schema::table('social_media', function (Blueprint $table) {
                $table->string('position_text')->nullable()->after('position');
            });
        }

        // 2) رجّع الأرقام إلى نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('social_media', 'position')) {
            DB::statement("
                UPDATE `social_media` SET `position_text` =
                    CASE `position`
                        WHEN 1 THEN 'header'
                        WHEN 2 THEN 'footer'
                        ELSE `position_text`
                    END
            ");
        }

        // 3) احذف العمود الرقمي وأعد التسمية للنصي
        if (Schema::hasColumn('social_media', 'position')) {
            Schema::table('social_media', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }

        if (Schema::hasColumn('social_media', 'position_text')) {
            Schema::table('social_media', function (Blueprint $table) {
                $table->renameColumn('position_text', 'position');
            });
        }
    }
};
