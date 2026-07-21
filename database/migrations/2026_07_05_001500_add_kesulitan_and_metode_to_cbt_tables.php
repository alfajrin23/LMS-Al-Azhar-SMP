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
        Schema::table('cbt_soals', function (Blueprint $table) {
            $table->string('kesulitan', 50)->default('sedang'); // mudah, sedang, sulit
        });

        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->string('metode', 50)->default('online'); // online, cetak
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbt_soals', function (Blueprint $table) {
            $table->dropColumn('kesulitan');
        });

        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->dropColumn('metode');
        });
    }
};
