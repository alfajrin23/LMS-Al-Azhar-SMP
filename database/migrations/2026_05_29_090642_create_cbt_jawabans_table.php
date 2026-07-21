<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained('cbt_exams')->cascadeOnDelete();
            $table->foreignId('cbt_soal_id')->constrained('cbt_soals')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->text('jawaban')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->boolean('dinilai')->default(false);
            $table->timestamps();

            $table->unique(['cbt_soal_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_jawabans');
    }
};
