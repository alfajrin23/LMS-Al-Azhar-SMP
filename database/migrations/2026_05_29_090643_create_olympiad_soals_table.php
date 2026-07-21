<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('olympiad_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olympiad_exam_id')->constrained('olympiad_exams')->cascadeOnDelete();
            $table->integer('nomor');
            $table->text('soal');
            $table->enum('tipe', ['pg', 'essay'])->default('pg');
            $table->text('pilihan_a')->nullable();
            $table->text('pilihan_b')->nullable();
            $table->text('pilihan_c')->nullable();
            $table->text('pilihan_d')->nullable();
            $table->string('jawaban_benar', 10)->nullable();
            $table->integer('bobot')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('olympiad_soals');
    }
};
