<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        DB::table('settings')->insert([
            ['key' => 'kkm_sd', 'value' => '70'],
            ['key' => 'kkm_smp', 'value' => '75'],
            ['key' => 'school_name', 'value' => 'Al Azhar Jaya Indonesia'],
            ['key' => 'semester_aktif', 'value' => 'Genap 2025/2026'],
            ['key' => 'alamat_sekolah', 'value' => 'Jl. Al Azhar No. 1, Bekasi'],
        ]);
    }
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
