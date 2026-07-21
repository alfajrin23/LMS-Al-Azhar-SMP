<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('deskripsi');
            $table->string('icon', 10)->default('⭐');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
