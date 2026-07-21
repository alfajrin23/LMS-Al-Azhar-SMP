<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahfidz_ayat_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahfidz_setoran_id')->constrained('tahfidz_setoran')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->integer('nomor_ayat');
            $table->tinyInteger('makhroj');
            $table->tinyInteger('tajwid');
            $table->tinyInteger('kelancaran');
            $table->timestamps();
            $table->unique(['tahfidz_setoran_id', 'guru_id', 'nomor_ayat'], 'tahfidz_ayat_nilai_unique');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tahfidz_ayat_nilai');
    }
};
