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
    Schema::create('hotspots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('page_id')->constrained()->cascadeOnDelete();
        $table->foreignId('article_id')->constrained()->cascadeOnDelete();
        // Coordinates for the interactive clickable zone
        $table->decimal('x', 8, 2); 
        $table->decimal('y', 8, 2);
        $table->decimal('width', 8, 2);
        $table->decimal('height', 8, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
