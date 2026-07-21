<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kondisi_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->integer('hubungan_guru_siswa');
            $table->integer('siswa_nyaman');
            $table->integer('siswa_minta_bantuan');
            $table->date('tanggal');
            $table->timestamps();
            $table->unique(['siswa_id', 'tanggal'], 'siswa_tanggal_unique');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kondisi_kelas');
    }
};
