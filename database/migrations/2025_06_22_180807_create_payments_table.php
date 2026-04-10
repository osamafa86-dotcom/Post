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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('entity');
            $table->string('account_id');
            $table->string('event');
            $table->string('payment_id');
            $table->json('payment_object');
            $table->integer('amount');
            $table->string('currency');
            $table->string('status');
            $table->string('order_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->boolean('international')->default(false);
            $table->string('method')->nullable();
            $table->integer('amount_refunded')->default(0);
            $table->string('refund_status')->nullable();
            $table->text('description')->nullable();
            $table->string('card_id')->nullable();
            $table->string('bank')->nullable();
            $table->string('wallet')->nullable();
            $table->string('vpa')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->json('notes')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->string('error_code')->nullable();
            $table->string('error_description')->nullable();
            $table->string('error_source')->nullable();
            $table->string('error_step')->nullable();
            $table->string('error_reason')->nullable();
            $table->json('acquirer_data')->nullable();
            $table->timestamp('razorpay_created_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
