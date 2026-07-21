<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->enum('tipe', ['ulangan', 'uts', 'uas'])->default('ulangan')->after('judul');
        });
    }
    public function down(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
