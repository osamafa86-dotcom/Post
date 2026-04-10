<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف عمود مؤقت للنوع الرقمي إن لم يكن موجوداً
        if (!Schema::hasColumn('materials', 'publish_status_int')) {
            if (Schema::hasColumn('materials', 'publish_status')) {
                Schema::table('materials', function (Blueprint $table) {
                    $table->unsignedBigInteger('publish_status_int')->nullable()->after('publish_status');
                });
            } else {
                // لو ما في publish_status أصلاً (كان رقم من قبل/محذوف)
                Schema::table('materials', function (Blueprint $table) {
                    $table->unsignedBigInteger('publish_status_int')->nullable();
                });
            }
        }

        // 2) حوّل القيم النصية إلى أرقام (فقط إذا العمود النصي موجود)
        if (Schema::hasColumn('materials', 'publish_status')) {
            DB::statement("
                UPDATE `materials` SET `publish_status_int` =
                    CASE `publish_status`
                        WHEN 'تم النشر' THEN 1
                        WHEN 'مسودة'    THEN 2
                        WHEN 'معلقة'    THEN 3
                        ELSE `publish_status_int`
                    END
            ");
        }

        // 3) احذف العمود القديم النصي لو موجود
        if (Schema::hasColumn('materials', 'publish_status')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('publish_status');
            });
        }

        // 4) أعد تسمية العمود المؤقت إلى الاسم الأصلي
        if (Schema::hasColumn('materials', 'publish_status_int')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->renameColumn('publish_status_int', 'publish_status');
            });
        }

        // 5) (اختياري) امنع NULL وحدد افتراضي 1
        if (Schema::hasColumn('materials', 'publish_status')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->unsignedBigInteger('publish_status')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) أضف عمود نصي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('materials', 'publish_status_text')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->string('publish_status_text')->nullable()->after('publish_status');
            });
        }

        // 2) رجّع الأرقام إلى نصوص (فقط إذا العمود الرقمي موجود)
        if (Schema::hasColumn('materials', 'publish_status')) {
            DB::statement("
                UPDATE `materials` SET `publish_status_text` =
                    CASE `publish_status`
                        WHEN 1 THEN 'تم النشر'
                        WHEN 2 THEN 'مسودة'
                        WHEN 3 THEN 'معلقة'
                        ELSE `publish_status_text`
                    END
            ");
        }

        // 3) احذف العمود الرقمي وأعد تسمية النصي للاسم الأصلي
        if (Schema::hasColumn('materials', 'publish_status')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('publish_status');
            });
        }

        if (Schema::hasColumn('materials', 'publish_status_text')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->renameColumn('publish_status_text', 'publish_status');
            });
        }
    }
};
