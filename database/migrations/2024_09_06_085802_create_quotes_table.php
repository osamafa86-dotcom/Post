<?php

use App\Enums\PublishEnum;
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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
//            $table->string('quote_author')->nullable();
//            $table->string('quote_photo')->nullable();
            $table->text('quote_text')->nullable();
            $table->string('quote_from')->nullable();
            $table->unsignedBigInteger('author_id')->index()->nullable();//participant
            $table->unsignedBigInteger('team_id')->index()->nullable();
            $table->string('publish_status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
