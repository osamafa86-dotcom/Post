<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أعمدة رقمية مؤقتة إن لم تكن موجودة (بدون default)
        if (!Schema::hasColumn('advertisements', 'type_int')) {
            if (Schema::hasColumn('advertisements', 'type')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable()->after('type');
                });
            } else {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_int')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('advertisements', 'place_int')) {
            if (Schema::hasColumn('advertisements', 'place')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->unsignedBigInteger('place_int')->nullable()->after('place');
                });
            } else {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->unsignedBigInteger('place_int')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('advertisements', 'url_target_int')) {
            if (Schema::hasColumn('advertisements', 'url_target')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->unsignedBigInteger('url_target_int')->nullable()->after('url_target');
                });
            } else {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->unsignedBigInteger('url_target_int')->nullable();
                });
            }
        }

        // 2) تحويل النصوص → أرقام (لو الأعمدة النصية موجودة)
        if (Schema::hasColumn('advertisements', 'type')) {
            DB::statement("
                UPDATE `advertisements` SET `type_int` =
                    CASE TRIM(`type`)
                        WHEN 'صورة' THEN 1
                        WHEN 'كود'  THEN 2
                        ELSE `type_int`
                    END
            ");
        }

        if (Schema::hasColumn('advertisements', 'place')) {
            DB::statement("
                UPDATE `advertisements` SET `place_int` =
                    CASE TRIM(`place`)
                        WHEN 'اعلان جانبي'    THEN 1
                        WHEN 'اعلان بالاسفل'  THEN 2
                        WHEN 'اعلان بالمنتصف' THEN 3
                        ELSE `place_int`
                    END
            ");
        }

        if (Schema::hasColumn('advertisements', 'url_target')) {
            DB::statement("
                UPDATE `advertisements` SET `url_target_int` =
                    CASE TRIM(`url_target`)
                        WHEN 'نفس الصفحة'   THEN 1
                        WHEN 'نافذة جديدة'  THEN 2
                        ELSE `url_target_int`
                    END
            ");
        }

        // 3) حذف الأعمدة النصية/القديمة لو موجودة
        if (Schema::hasColumn('advertisements', 'type')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn('advertisements', 'place')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->dropColumn('place');
            });
        }
        if (Schema::hasColumn('advertisements', 'url_target')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->dropColumn('url_target');
            });
        }

        // 4) إعادة تسمية الأعمدة المؤقتة إلى الأسماء الأصلية
        if (Schema::hasColumn('advertisements', 'type_int')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->renameColumn('type_int', 'type');
            });
        }
        if (Schema::hasColumn('advertisements', 'place_int')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->renameColumn('place_int', 'place');
            });
        }
        if (Schema::hasColumn('advertisements', 'url_target_int')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->renameColumn('url_target_int', 'url_target');
            });
        }

        // (اختياري) لو حاب تثبّت NOT NULL أو تضيف default، اعمل ->change() هنا.
        // يتطلب doctrine/dbal لعمليات change/rename:
        // composer require doctrine/dbal
    }

    public function down(): void
    {
        // 1) أعمدة نصية مؤقتة إن لم تكن موجودة
        if (!Schema::hasColumn('advertisements', 'type_text')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->string('type_text')->nullable()->after('type');
            });
        }
        if (!Schema::hasColumn('advertisements', 'place_text')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->string('place_text')->nullable()->after('place');
            });
        }
        if (!Schema::hasColumn('advertisements', 'url_target_text')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->string('url_target_text')->nullable()->after('url_target');
            });
        }

        // 2) تحويل الأرقام → نصوص (لو الأعمدة الرقمية موجودة)
        if (Schema::hasColumn('advertisements', 'type')) {
            DB::statement("
                UPDATE `advertisements` SET `type_text` =
                    CASE `type`
                        WHEN 1 THEN 'صورة'
                        WHEN 2 THEN 'كود'
                        ELSE `type_text`
                    END
            ");
        }
        if (Schema::hasColumn('advertisements', 'place')) {
            DB::statement("
                UPDATE `advertisements` SET `place_text` =
                    CASE `place`
                        WHEN 1 THEN 'اعلان جانبي'
                        WHEN 2 THEN 'اعلان بالاسفل'
                        WHEN 3 THEN 'اعلان بالمنتصف'
                        ELSE `place_text`
                    END
            ");
        }
        if (Schema::hasColumn('advertisements', 'url_target')) {
            DB::statement("
                UPDATE `advertisements` SET `url_target_text` =
                    CASE `url_target`
                        WHEN 1 THEN 'نفس الصفحة'
                        WHEN 2 THEN 'نافذة جديدة'
                        ELSE `url_target_text`
                    END
            ");
        }

        // 3) حذف الأعمدة الرقمية وإرجاع النصية
        if (Schema::hasColumn('advertisements', 'type')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn('advertisements', 'place')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->dropColumn('place');
            });
        }
        if (Schema::hasColumn('advertisements', 'url_target')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->dropColumn('url_target');
            });
        }

        if (Schema::hasColumn('advertisements', 'type_text')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->renameColumn('type_text', 'type');
            });
        }
        if (Schema::hasColumn('advertisements', 'place_text')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->renameColumn('place_text', 'place');
            });
        }
        if (Schema::hasColumn('advertisements', 'url_target_text')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->renameColumn('url_target_text', 'url_target');
            });
        }
    }
};
