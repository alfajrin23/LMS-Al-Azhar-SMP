<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workbook_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workbook_soal_id')->constrained('workbook_soals')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->text('jawaban')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['workbook_soal_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workbook_jawabans');
    }
};
