<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) أعمدة رقمية مؤقتة إن لم تكن موجودة
        if (!Schema::hasColumn('categories', 'category_style_int')) {
            if (Schema::hasColumn('categories', 'category_style')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->unsignedBigInteger('category_style_int')->nullable()->after('category_style');
                });
            } else {
                Schema::table('categories', function (Blueprint $table) {
                    $table->unsignedBigInteger('category_style_int')->nullable();
                });
            }
        }

        if (!Schema::hasColumn('categories', 'category_type_int')) {
            if (Schema::hasColumn('categories', 'category_type')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->unsignedBigInteger('category_type_int')->nullable()->after('category_type');
                });
            } else {
                Schema::table('categories', function (Blueprint $table) {
                    $table->unsignedBigInteger('category_type_int')->nullable();
                });
            }
        }

        // 2) تحويل النصوص → أرقام (لو الأعمدة النصية موجودة)
        if (Schema::hasColumn('categories', 'category_style')) {
            DB::statement("
                UPDATE `categories` SET `category_style_int` =
                    CASE TRIM(`category_style`)
                        WHEN 'نمط واحد كبير وأربعة صغير' THEN 1
                        WHEN 'نمط أربعة كبيرة بقية صغير' THEN 2
                        WHEN 'نمط واحد كبير اثنين متوسط' THEN 3
                        WHEN 'نمط ثلاثة متوسطين مع قائمة فرعية' THEN 4
                        WHEN 'نمط ثلاث بودكاست' THEN 5
                        WHEN 'نمط فيديو واحد كبير وثلاث وسط' THEN 6
                        WHEN 'نمط اثنان كبيرين و أربع متوسطين' THEN 7
                        WHEN 'نمط أربع متوسطين' THEN 8
                        WHEN 'نمط ست متوسطين عرضيين' THEN 9
                        WHEN 'نمط ثلاث متوسطين طوليين' THEN 10
                        WHEN 'نمط واحد طويل وست فرعيين' THEN 11
                        WHEN 'نمط ست متحركين' THEN 12
                        WHEN 'نمط واحد طويل وأربع فرعيين' THEN 13
                        WHEN 'نمط ثلاثة متوسطين مع ثلاث صغيرين فرعيين' THEN 14
                        WHEN 'نمط واحد كبير مع ثلاث متوسطيين' THEN 15
                        WHEN 'نمط واحد كبير مع أربع فيديوهات متوسطيين' THEN 16
                        WHEN 'نمط واحد متوسط مع ثلاث فرعيين' THEN 17
                        WHEN 'نمط ثلاث متوسطين مع شريط متحرك' THEN 18
                        WHEN 'نمط أثنان كبيرين مع ثلاث متوسطين' THEN 19
                        WHEN 'نمط واحد كبير مع أربع متوسطيين' THEN 20
                        WHEN 'نمط واحد كبير مع واحد عريض مع أثنين متوسطيين' THEN 21
                        WHEN 'نمط واحد متوسط مع شريط عامودي' THEN 22
                        WHEN 'نمط أربع متوسطين مع قائمة فرعية' THEN 23
                        WHEN 'نمط واحد كبير مع أربع فرعيين' THEN 24
                        WHEN 'نمط فيديو كبير مع ثلاث فرعيين' THEN 25
                        WHEN 'نمط شريط متحرك يحوي ثلاث متوسطين' THEN 26
                        WHEN 'نمط شريط متحرك يحوي ثلاث متوسطين بخلفية سوداء' THEN 27
                        WHEN 'نمط تصنيفان' THEN 28
                        WHEN 'نمط ثلاثة متوسطين مع شريط متحرك وفرعيين' THEN 30
                        WHEN 'نمط ثمانية متوسطين مع ثمانية صغار' THEN 31
                        WHEN 'نمط شريط متحرك متوسط مع ثلاث فرعيين' THEN 32
                        WHEN 'نمط متوسط مع ثلاث فرعيين' THEN 33
                        ELSE `category_style_int`
                    END
            ");
        }

        if (Schema::hasColumn('categories', 'category_type')) {
            DB::statement("
                UPDATE `categories` SET `category_type_int` =
                    CASE TRIM(`category_type`)
                        WHEN 'أخبار' THEN 1
                        WHEN 'قائمة' THEN 2
                        WHEN 'صفحات' THEN 3
                        WHEN 'بودكاست' THEN 4
                        WHEN 'فيديو' THEN 5
                        WHEN 'صورة' THEN 6
                        WHEN 'الاحداث' THEN 7
                        WHEN 'المستخدمين' THEN 8
                        WHEN 'تدريب' THEN 9
                        WHEN 'تطوع' THEN 10
                        WHEN 'مؤتمرات' THEN 11
                        ELSE `category_type_int`
                    END
            ");
        }

        // 3) حذف الأعمدة النصية القديمة لو موجودة
        if (Schema::hasColumn('categories', 'category_style')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('category_style');
            });
        }
        if (Schema::hasColumn('categories', 'category_type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('category_type');
            });
        }

        // 4) إعادة تسمية المؤقتة إلى الأسماء الأصلية
        if (Schema::hasColumn('categories', 'category_style_int')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->renameColumn('category_style_int', 'category_style');
            });
        }
        if (Schema::hasColumn('categories', 'category_type_int')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->renameColumn('category_type_int', 'category_type');
            });
        }

        // 5) (اختياري) جعلها NOT NULL مع افتراضيات مناسبة
        // إن رغبت بذلك، الغِ التعليق التالي:
         if (Schema::hasColumn('categories', 'category_style')) {
             Schema::table('categories', function (Blueprint $table) {
                 $table->unsignedBigInteger('category_style')->nullable()->change();
             });
         }
         if (Schema::hasColumn('categories', 'category_type')) {
             Schema::table('categories', function (Blueprint $table) {
                 $table->unsignedBigInteger('category_type')->nullable()->change();
             });
         }
    }

    public function down(): void
    {
        // 1) أعمدة نصيّة مؤقتة إن لم تكن موجودة
        if (!Schema::hasColumn('categories', 'category_style_text')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('category_style_text')->nullable()->after('category_style');
            });
        }
        if (!Schema::hasColumn('categories', 'category_type_text')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('category_type_text')->nullable()->after('category_type');
            });
        }

        // 2) تحويل الأرقام → نصوص (لو الأعمدة الرقمية موجودة)
        if (Schema::hasColumn('categories', 'category_style')) {
            DB::statement("
                UPDATE `categories` SET `category_style_text` =
                    CASE `category_style`
                        WHEN 1 THEN 'نمط واحد كبير وأربعة صغير'
                        WHEN 2 THEN 'نمط أربعة كبيرة بقية صغير'
                        WHEN 3 THEN 'نمط واحد كبير اثنين متوسط'
                        WHEN 4 THEN 'نمط ثلاثة متوسطين مع قائمة فرعية'
                        WHEN 5 THEN 'نمط ثلاث بودكاست'
                        WHEN 6 THEN 'نمط فيديو واحد كبير وثلاث وسط'
                        WHEN 7 THEN 'نمط اثنان كبيرين و أربع متوسطين'
                        WHEN 8 THEN 'نمط أربع متوسطين'
                        WHEN 9 THEN 'نمط ست متوسطين عرضيين'
                        WHEN 10 THEN 'نمط ثلاث متوسطين طوليين'
                        WHEN 11 THEN 'نمط واحد طويل وست فرعيين'
                        WHEN 12 THEN 'نمط ست متحركين'
                        WHEN 13 THEN 'نمط واحد طويل وأربع فرعيين'
                        WHEN 14 THEN 'نمط ثلاثة متوسطين مع ثلاث صغيرين فرعيين'
                        WHEN 15 THEN 'نمط واحد كبير مع ثلاث متوسطيين'
                        WHEN 16 THEN 'نمط واحد كبير مع أربع فيديوهات متوسطيين'
                        WHEN 17 THEN 'نمط واحد متوسط مع ثلاث فرعيين'
                        WHEN 18 THEN 'نمط ثلاث متوسطين مع شريط متحرك'
                        WHEN 19 THEN 'نمط أثنان كبيرين مع ثلاث متوسطين'
                        WHEN 20 THEN 'نمط واحد كبير مع أربع متوسطيين'
                        WHEN 21 THEN 'نمط واحد كبير مع واحد عريض مع أثنين متوسطيين'
                        WHEN 22 THEN 'نمط واحد متوسط مع شريط عامودي'
                        WHEN 23 THEN 'نمط أربع متوسطين مع قائمة فرعية'
                        WHEN 24 THEN 'نمط واحد كبير مع أربع فرعيين'
                        WHEN 25 THEN 'نمط فيديو كبير مع ثلاث فرعيين'
                        WHEN 26 THEN 'نمط شريط متحرك يحوي ثلاث متوسطين'
                        WHEN 27 THEN 'نمط شريط متحرك يحوي ثلاث متوسطين بخلفية سوداء'
                        WHEN 28 THEN 'نمط تصنيفان'
                        WHEN 30 THEN 'نمط ثلاثة متوسطين مع شريط متحرك وفرعيين'
                        WHEN 31 THEN 'نمط ثمانية متوسطين مع ثمانية صغار'
                        WHEN 32 THEN 'نمط شريط متحرك متوسط مع ثلاث فرعيين'
                        WHEN 33 THEN 'نمط متوسط مع ثلاث فرعيين'
                        ELSE `category_style_text`
                    END
            ");
        }

        if (Schema::hasColumn('categories', 'category_type')) {
            DB::statement("
                UPDATE `categories` SET `category_type_text` =
                    CASE `category_type`
                        WHEN 1 THEN 'أخبار'
                        WHEN 2 THEN 'قائمة'
                        WHEN 3 THEN 'صفحات'
                        WHEN 4 THEN 'بودكاست'
                        WHEN 5 THEN 'فيديو'
                        WHEN 6 THEN 'صورة'
                        WHEN 7 THEN 'الاحداث'
                        WHEN 8 THEN 'المستخدمين'
                        WHEN 9 THEN 'تدريب'
                        WHEN 10 THEN 'تطوع'
                        WHEN 11 THEN 'مؤتمرات'
                        ELSE `category_type_text`
                    END
            ");
        }

        // 3) حذف الأعمدة الرقمية وإرجاع النصيّة
        if (Schema::hasColumn('categories', 'category_style')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('category_style');
            });
        }
        if (Schema::hasColumn('categories', 'category_type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('category_type');
            });
        }

        if (Schema::hasColumn('categories', 'category_style_text')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->renameColumn('category_style_text', 'category_style');
            });
        }
        if (Schema::hasColumn('categories', 'category_type_text')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->renameColumn('category_type_text', 'category_type');
            });
        }
    }
};
