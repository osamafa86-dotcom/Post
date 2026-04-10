<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('immigrant_data', function (Blueprint $table) {
            $table->longText('opportunity')->nullable()->after('file');
        });
    }

    public function down(): void
    {
        Schema::table('immigrant_data', function (Blueprint $table) {
            $table->dropColumn('opportunity');
        });
    }
};
