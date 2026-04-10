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
        if (!Schema::hasColumn('quotes', 'publish_status_int')) {
            if (Schema::hasColumn('quotes', 'publish_status')) {
                Schema::table('quotes', function (Blueprint $table) {
                    $table->unsignedBigInteger('publish_status_int')->nullable()->after('publish_status');
                });
            } else {
                Schema::table('quotes', function (Blueprint $table) {
                    $table->unsignedBigInteger('publish_status_int')->nullable();
                });
            }
        }

        // 2) حوّل النصوص إلى أرقام (لو العمود النصّي موجود)
        if (Schema::hasColumn('quotes', 'publish_status')) {
            DB::statement("
                UPDATE `quotes` SET `publish_status_int` =
                    CASE TRIM(`publish_status`)
                        WHEN 'تم النشر' THEN 1
                        WHEN 'مسودة'    THEN 2
                        WHEN 'معلقة'    THEN 3
                        ELSE `publish_status_int`
                    END
            ");
        }

        // 3) احذف العمود النصّي القديم لو موجود
        if (Schema::hasColumn('quotes', 'publish_status')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->dropColumn('publish_status');
            });
        }

        // 4) أعد تسمية العمود المؤقت للاسم الأصلي
        if (Schema::hasColumn('quotes', 'publish_status_int')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->renameColumn('publish_status_int', 'publish_status');
            });
        }

        // 5) ثبّت النوع والافتراضي (اختياري لكن مستحسن)
        if (Schema::hasColumn('quotes', 'publish_status')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->unsignedBigInteger('publish_status')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // 1) عمود نصّي مؤقت إن لم يكن موجوداً
        if (!Schema::hasColumn('quotes', 'publish_status_text')) {
            if (Schema::hasColumn('quotes', 'publish_status')) {
                Schema::table('quotes', function (Blueprint $table) {
                    $table->string('publish_status_text')->nullable()->after('publish_status');
                });
            } else {
                Schema::table('quotes', function (Blueprint $table) {
                    $table->string('publish_status_text')->nullable();
                });
            }
        }

        // 2) رجّع الأرقام إلى نصوص (لو العمود الرقمي موجود)
        if (Schema::hasColumn('quotes', 'publish_status')) {
            DB::statement("
                UPDATE `quotes` SET `publish_status_text` =
                    CASE `publish_status`
                        WHEN 1 THEN 'تم النشر'
                        WHEN 2 THEN 'مسودة'
                        WHEN 3 THEN 'معلقة'
                        ELSE `publish_status_text`
                    END
            ");
        }

        // 3) احذف العمود الرقمي وأعد التسمية للنصي
        if (Schema::hasColumn('quotes', 'publish_status')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->dropColumn('publish_status');
            });
        }

        if (Schema::hasColumn('quotes', 'publish_status_text')) {
            Schema::table('quotes', function (Blueprint $table) {
                $table->renameColumn('publish_status_text', 'publish_status');
            });
        }
    }
};
