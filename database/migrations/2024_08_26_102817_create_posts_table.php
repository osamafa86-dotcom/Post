<?php

use App\Enums\PublishEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('publish_date')->nullable();
            $table->bigInteger('order_number')->nullable();
            $table->string('title')->index()->nullable();
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('image_alt')->nullable();

            //just for theme
            $table->string('news_type')->nullable();//view enum
            $table->string('image_size')->nullable();//view enum

            $table->text('description')->nullable();
            $table->longText('body')->nullable();
            $table->bigInteger('views')->nullable();
            $table->bigInteger('updates')->nullable();
            $table->string('publish_status', 20)->default(PublishEnum::DRAFT->value);
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->unsignedBigInteger('publisher_id')->index()->nullable();
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();


//            $table->unsignedBigInteger('author_id')->index()->nullable();
//            $table->unsignedBigInteger('type_id')->index()->nullable();
//            $table->unsignedBigInteger('status_id')->index()->nullable();
//            $table->unsignedBigInteger('category_id')->index()->nullable();
//            $table->unsignedBigInteger('special_file_id')->index()->nullable();
//            $table->boolean('main_news')->default(false)->nullable();
//            $table->boolean('sub_news')->default(false)->nullable();
//            $table->boolean('last_news')->default(false)->nullable();
//            $table->boolean('big_image')->default(false)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
