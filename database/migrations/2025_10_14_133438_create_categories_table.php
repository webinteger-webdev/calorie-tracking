<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name der Kategorie
            $table->timestamps();
        });

        // Beziehung in foods-Tabelle
        Schema::table('foods', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('brand')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
};
