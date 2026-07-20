<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('converted_tariff_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_history_id')->constrained('import_histories')->cascadeOnDelete();
            $table->integer('row_index');
            $table->string('nama_tindakan');
            $table->string('kelas');
            $table->boolean('induk')->default(false);
            $table->decimal('total_tarif', 15, 2)->default(0);
            $table->decimal('tarif_komponen1', 15, 2)->default(0);
            $table->decimal('tarif_komponen2', 15, 2)->default(0);
            $table->decimal('tarif_komponen3', 15, 2)->default(0);
            $table->boolean('has_mismatch')->default(false);
            $table->timestamps();

            $table->index('import_history_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('converted_tariff_items');
    }
};
