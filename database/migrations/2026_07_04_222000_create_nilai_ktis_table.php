<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ktis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswa')->onDelete('cascade');
            $table->string('judul_kti');
            $table->decimal('nilai_proses', 5, 2);
            $table->decimal('nilai_tulisan', 5, 2);
            $table->decimal('nilai_sidang', 5, 2);
            $table->decimal('nilai_akhir', 5, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('nilai_ktis');
    }
};
