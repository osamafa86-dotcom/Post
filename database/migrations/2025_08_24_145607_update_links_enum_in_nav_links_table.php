<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أضف أعمدة رقمية مؤقتة إن لم تكن موجودة
        if (!Schema::hasColumn('navbar_links', 'link_status_int')) {
            if (Schema::hasColumn('navbar_links', 'link_status')) {
                Schema::table('navbar_links', function (Blueprint $table) {
                    $table->unsignedBigInteger('link_status_int')->nullable()->after('link_status');
                });
            } else {
                Schema::table('navbar_links', function (Blueprint $table) {
                    $table->unsignedBigInteger('link_status_int')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('navbar_links', 'link_open_int')) {
            if (Schema::hasColumn('navbar_links', 'link_open')) {
                Schema::table('navbar_links', function (Blueprint $table) {
                    $table->unsignedBigInteger('link_open_int')->nullable()->after('link_open');
                });
            } else {
                Schema::table('navbar_links', function (Blueprint $table) {
                    $table->unsignedBigInteger('link_open_int')->nullable();
                });
            }
        }

        // 2) تحويل النصوص → أرقام (لو الأعمدة النصية موجودة)
        if (Schema::hasColumn('navbar_links', 'link_status')) {
            DB::statement("
                UPDATE `navbar_links` SET `link_status_int` =
                    CASE TRIM(`link_status`)
                        WHEN 'تصنيفات'   THEN 1
                        WHEN 'وسم'       THEN 2
                        WHEN 'بودكاست'   THEN 3
                        WHEN 'فيديو'     THEN 4
                        WHEN 'صفحة خاصة' THEN 5
                        WHEN 'رابط مخصص' THEN 6
                        ELSE `link_status_int`
                    END
            ");
        }

        if (Schema::hasColumn('navbar_links', 'link_open')) {
            DB::statement("
                UPDATE `navbar_links` SET `link_open_int` =
                    CASE TRIM(`link_open`)
                        WHEN 'نفس الصفحة'  THEN 1
                        WHEN 'نافذة جديدة' THEN 2
                        ELSE `link_open_int`
                    END
            ");
        }

        // 3) حذف الأعمدة النصية القديمة لو موجودة
        if (Schema::hasColumn('navbar_links', 'link_status')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->dropColumn('link_status');
            });
        }
        if (Schema::hasColumn('navbar_links', 'link_open')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->dropColumn('link_open');
            });
        }

        // 4) إعادة تسمية المؤقتة إلى الأسماء الأصلية
        if (Schema::hasColumn('navbar_links', 'link_status_int')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->renameColumn('link_status_int', 'link_status');
            });
        }
        if (Schema::hasColumn('navbar_links', 'link_open_int')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->renameColumn('link_open_int', 'link_open');
            });
        }

        // 5) (اختياري) اجعل الأعمدة NOT NULL وحدد افتراضيات
         if (Schema::hasColumn('navbar_links', 'link_status')) {
             Schema::table('navbar_links', function (Blueprint $table) {
                 $table->unsignedBigInteger('link_status')->nullable()->change();
             });
         }
         if (Schema::hasColumn('navbar_links', 'link_open')) {
             Schema::table('navbar_links', function (Blueprint $table) {
                 $table->unsignedBigInteger('link_open')->nullable()->change();
             });
         }
    }

    public function down(): void
    {
        // 1) أعمدة نصيّة مؤقتة إن لم تكن موجودة
        if (!Schema::hasColumn('navbar_links', 'link_status_text')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->string('link_status_text')->nullable()->after('link_status');
            });
        }
        if (!Schema::hasColumn('navbar_links', 'link_open_text')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->string('link_open_text')->nullable()->after('link_open');
            });
        }

        // 2) تحويل الأرقام → نصوص (لو الأعمدة الرقمية موجودة)
        if (Schema::hasColumn('navbar_links', 'link_status')) {
            DB::statement("
                UPDATE `navbar_links` SET `link_status_text` =
                    CASE `link_status`
                        WHEN 1 THEN 'تصنيفات'
                        WHEN 2 THEN 'وسم'
                        WHEN 3 THEN 'بودكاست'
                        WHEN 4 THEN 'فيديو'
                        WHEN 5 THEN 'صفحة خاصة'
                        WHEN 6 THEN 'رابط مخصص'
                        ELSE `link_status_text`
                    END
            ");
        }
        if (Schema::hasColumn('navbar_links', 'link_open')) {
            DB::statement("
                UPDATE `navbar_links` SET `link_open_text` =
                    CASE `link_open`
                        WHEN 1 THEN 'نفس الصفحة'
                        WHEN 2 THEN 'نافذة جديدة'
                        ELSE `link_open_text`
                    END
            ");
        }

        // 3) حذف الأعمدة الرقمية
        if (Schema::hasColumn('navbar_links', 'link_status')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->dropColumn('link_status');
            });
        }
        if (Schema::hasColumn('navbar_links', 'link_open')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->dropColumn('link_open');
            });
        }

        // 4) إعادة تسمية النصية إلى الأصل
        if (Schema::hasColumn('navbar_links', 'link_status_text')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->renameColumn('link_status_text', 'link_status');
            });
        }
        if (Schema::hasColumn('navbar_links', 'link_open_text')) {
            Schema::table('navbar_links', function (Blueprint $table) {
                $table->renameColumn('link_open_text', 'link_open');
            });
        }
    }
};
