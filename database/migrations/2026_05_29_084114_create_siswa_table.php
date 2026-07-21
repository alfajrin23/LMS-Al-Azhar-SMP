<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nis')->unique();
            $table->string('nama');

            $table->foreignId('kelas_id')
                ->nullable()
                ->constrained('kelas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('kelas_quran_id')
                ->nullable()
                ->constrained('kelas_quran')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->enum('jenis_kelamin', ['L', 'P']);

            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->text('alamat')->nullable();

            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
