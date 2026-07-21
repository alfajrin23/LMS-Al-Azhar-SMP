<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert old text data to valid JSON first
        DB::table('laporan_mengajars')->get()->each(function ($laporan) {
            if (!is_null($laporan->isi)) {
                json_decode($laporan->isi);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    DB::table('laporan_mengajars')
                        ->where('id', $laporan->id)
                        ->update([
                            'isi' => json_encode(['catatan_umum' => $laporan->isi])
                        ]);
                }
            }
        });

        Schema::table('laporan_mengajars', function (Blueprint $table) {
            $table->json('isi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_mengajars', function (Blueprint $table) {
            $table->text('isi')->change();
        });
    }
};
