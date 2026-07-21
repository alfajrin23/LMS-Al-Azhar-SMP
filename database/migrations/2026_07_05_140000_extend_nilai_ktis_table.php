<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai_ktis', function (Blueprint $table) {
            $table->enum('current_bab', ['Bab 1', 'Bab 2', 'Bab 3', 'Bab 4', 'Bab 5', 'Draft Akhir', 'Siap Sidang', 'Selesai'])->default('Bab 1')->after('judul_kti');
            $table->dateTime('jadwal_sidang')->nullable()->after('nilai_sidang');
        });
    }
    public function down(): void
    {
        Schema::table('nilai_ktis', function (Blueprint $table) {
            $table->dropColumn(['current_bab', 'jadwal_sidang']);
        });
    }
};
