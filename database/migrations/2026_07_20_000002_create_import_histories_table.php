<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_histories', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('total_rows_source')->default(0);
            $table->integer('total_rows_converted')->default(0);
            $table->string('status')->default('completed'); // pending, processing, completed, failed
            $table->foreignId('mapping_template_id')->nullable()->constrained('mapping_templates')->nullOnDelete();
            $table->integer('mismatch_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_histories');
    }
};
