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
        Schema::create('admin_schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['available', 'unavailable'])->default('available');

            $table->integer('admin_id');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_schedules');
    }
};
    