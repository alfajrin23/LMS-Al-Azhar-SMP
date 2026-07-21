<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahfidz_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('kelas_quran_id')->nullable()->constrained('kelas_quran')->nullOnDelete();
            $table->string('surah')->nullable();
            $table->unsignedInteger('ayat_mulai')->nullable();
            $table->unsignedInteger('ayat_selesai')->nullable();
            $table->unsignedTinyInteger('juz_dihafal')->default(0);
            $table->unsignedInteger('total_ayat')->nullable();
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->string('target_deskripsi')->nullable();
            $table->enum('status', ['belum_mulai', 'berproses', 'lancar', 'perlu_murojaah'])->default('berproses');
            $table->text('catatan')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kelas_id', 'progress_percent']);
            $table->index(['kelas_quran_id', 'progress_percent']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahfidz_progress');
    }
};
