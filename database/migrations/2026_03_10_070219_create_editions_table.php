<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->date('edition_date');
            $table->enum('status', ['draft', 'processing', 'published', 'failed'])->default('draft');
            $table->timestamps();
            $table->softDeletes(); // Protects against accidental deletion

            // Index for fast lookups on the frontend archive
            $table->index(['status', 'edition_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};