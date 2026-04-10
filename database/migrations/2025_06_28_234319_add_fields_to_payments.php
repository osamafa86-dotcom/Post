<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('subscription_plan_id')->nullable();
            $table->string('subscription_current_start')->nullable();
            $table->string('subscription_current_end')->nullable();
            $table->json('subscription_object')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('subscription_plan_id');
            $table->dropColumn('subscription_current_start');
            $table->dropColumn('subscription_current_end');
            $table->dropColumn('subscription_object');
        });
    }
};
