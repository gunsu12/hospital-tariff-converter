<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapping_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->jsonb('config'); // Stores JSON mapping of source columns to target categories
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapping_templates');
    }
};
