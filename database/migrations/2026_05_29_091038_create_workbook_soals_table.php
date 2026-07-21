<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workbook_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workbook_id')->constrained('workbooks')->cascadeOnDelete();
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
        Schema::dropIfExists('workbook_soals');
    }
};
