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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('pet_name');
            $table->date('pet_birth');
            $table->enum('pet_gender', ['male', 'female']);
            $table->decimal('pet_weight', 5, 2);
            $table->text('pet_notes')->nullable();
            $table->string('pet_image')->nullable();
            
            $table->integer('customer_id');
            $table->integer('pet_breed_id');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
