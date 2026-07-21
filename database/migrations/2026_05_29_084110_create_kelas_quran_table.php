<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_quran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->enum('jenjang', ['SD', 'SMP']);
            $table->enum('kategori', [
                'Ikhwan',
                'Akhwat',
            ]);
            $table->string('tingkat');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kelas_quran');
    }
};
