<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_mengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->string('tipe');
            $table->date('tanggal');
            $table->text('isi');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('laporan_mengajars');
    }
};
