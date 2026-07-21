<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workbooks', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->foreignId('mapel_id')->constrained('mapel');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas');
            $table->foreignId('guru_id')->constrained('guru');
            $table->enum('tipe', ['tugas_pengganti', 'remedial', 'penugasan_di_rumah'])->default('penugasan_di_rumah');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('workbooks');
    }
};
