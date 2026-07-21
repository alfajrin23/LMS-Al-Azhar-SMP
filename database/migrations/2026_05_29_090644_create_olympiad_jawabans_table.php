<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('olympiad_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olympiad_exam_id')->constrained('olympiad_exams')->cascadeOnDelete();
            $table->foreignId('olympiad_soal_id')->constrained('olympiad_soals')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->text('jawaban')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->boolean('dinilai')->default(false);
            $table->timestamps();

            $table->unique(['olympiad_soal_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('olympiad_jawabans');
    }
};
