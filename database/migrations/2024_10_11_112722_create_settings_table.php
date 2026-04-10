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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            //public
            $table->json('site_name')->nullable();
            $table->json('light_site_name')->nullable();
            $table->json('site_logo')->nullable();
            $table->string('site_email')->nullable();
            $table->json('footer_description')->nullable();
            $table->json('copyright_text')->nullable();
            $table->boolean('website_active')->nullable();
            //contact

            //open graph
            $table->string('open_graph_image')->nullable();
            $table->longText('tags')->nullable();
            $table->string('open_graph_description')->nullable();
            $table->string('open_graph_local')->nullable();
            $table->string('open_graph_author')->nullable();
            $table->string('open_graph_twitter_creator')->nullable();
            $table->string('open_graph_twitter_site')->nullable();
            //extra code
            $table->longText('header_code')->nullable();
            $table->longText('footer_code')->nullable();
            //watermark
            $table->string('water_mark_image')->nullable();
            $table->string('water_mark_place')->nullable();
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
        Schema::dropIfExists('settings');
    }
};
