<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->decimal('jumlah', 12, 2);
            $table->date('tenggat')->nullable();
            $table->enum('status', ['lunas', 'belum'])->default('belum');
            $table->timestamps();

            $table->unique(['siswa_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spps');
    }
};
