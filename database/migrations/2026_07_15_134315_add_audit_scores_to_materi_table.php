<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->integer('skor_kauniyah')->nullable()->after('status');
            $table->integer('skor_bilingual')->nullable()->after('skor_kauniyah');
            $table->integer('skor_ai')->nullable()->after('skor_bilingual');
        });
    }
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumn(['skor_kauniyah', 'skor_bilingual', 'skor_ai']);
        });
    }
};
