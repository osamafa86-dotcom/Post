<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumns('settings', ['phone', 'address'])) {
                    $table->string('phone')->nullable()->after('site_email');
                    $table->string('address')->nullable()->after('site_email');
                }
                if (Schema::hasColumns('settings', ['site_name', 'light_site_name', 'site_logo', 'footer_description', 'copyright_text'])) {
                    $table->text('site_name')->nullable()->change();
                    $table->text('light_site_name')->nullable()->change();
                    $table->text('site_logo')->nullable()->change();
                    $table->text('footer_description')->nullable()->change();
                    $table->text('copyright_text')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
