<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahfidz_setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('surah');
            $table->integer('ayat_mulai');
            $table->integer('ayat_selesai');
            $table->integer('jumlah_ayat');
            $table->enum('status', ['baru', 'murojaah'])->default('baru');
            $table->integer('nilai')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tahfidz_setoran');
    }
};
