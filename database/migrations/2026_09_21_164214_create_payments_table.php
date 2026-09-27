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
            $table->id('payment_id');
            $table->datetime('payment_date')->useCurrent();
            $table->decimal('payment_amount', 8, 2);
            $table->enum('payment_method', ['qrCode','creditCard']);
            $table->string('transaction_ref')->nullable();
            $table->string('payment_image')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            
            $table->integer('booking_id');

            $table->timestamps();
            $table->softDeletes();
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
