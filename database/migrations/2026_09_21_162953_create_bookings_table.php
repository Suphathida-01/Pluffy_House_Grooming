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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->date('booking_date');
            $table->time('booking_start_time');
            $table->time('booking_end_time');
            $table->decimal('booking_total_price', 8, 2);
            $table->enum('booking_status', ['pending', 'confirmed', 'completed'])->default('pending');

            $table->integer('customer_id');
            $table->integer('admin_id');
            $table->integer('service_id');
            $table->integer('pet_id');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
