<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->index('name');
            $table->text('description')->nullable();
            $table->integer('servings')->default(1);
            $table->integer('calories_per_serving');
            $table->decimal('protein_per_serving', 5, 2);
            $table->decimal('carbs_per_serving', 5, 2);
            $table->decimal('fat_per_serving', 5, 2);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
