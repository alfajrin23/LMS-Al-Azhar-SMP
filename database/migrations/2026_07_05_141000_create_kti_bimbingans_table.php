<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kti_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('bab', ['Bab 1', 'Bab 2', 'Bab 3', 'Bab 4', 'Bab 5', 'Draft Akhir']);
            $table->string('file_draft');
            $table->text('catatan_siswa')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'revisi'])->default('pending');
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kti_bimbingans');
    }
};
