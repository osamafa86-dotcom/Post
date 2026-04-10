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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('participants', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('breaking_news', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('icons', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('special_files', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('special_pages', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('events', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('user_logs', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('navbar_links', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('social_media', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('reels', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('services', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
        Schema::table('post_relations', function (Blueprint $table) {
            $table->string('lang')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('breaking_news', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('icons', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('special_files', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('special_pages', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('user_logs', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('navbar_links', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('social_media', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('reels', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
        Schema::table('post_relations', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
    }
};
