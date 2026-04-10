<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'favicon')) {
                $table->dropColumn('favicon');
            }
            if (Schema::hasColumn('settings', 'footer_logo')) {
                $table->dropColumn('footer_logo');
            }
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('favicon')->nullable();
            $table->string('footer_logo')->nullable();
        });
    }
};
