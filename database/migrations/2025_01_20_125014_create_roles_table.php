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
        Schema::create('notification_roles', function (Blueprint $table) {
            $table->id(); // model - action - notification - target
            $table->string('model')->index()->nullable();
//            $table->unsignedBigInteger('record_id')->index()->nullable();
            $table->string('action')->index()->nullable();
            $table->unsignedBigInteger('permission_target_id')->index()->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_roles');
    }
};
