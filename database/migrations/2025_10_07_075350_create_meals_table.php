<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->date('date');
            $table->time('time')->nullable();
            $table->integer('total_calories')->default(0);
            $table->decimal('total_protein', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);
            $table->decimal('total_fat', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
