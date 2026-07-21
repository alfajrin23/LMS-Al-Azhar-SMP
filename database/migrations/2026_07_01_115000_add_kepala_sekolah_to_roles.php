<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa_sd', 'siswa_smp', 'guru', 'orang_tua', 'admin', 'kepala_sekolah'))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('siswa_sd', 'siswa_smp', 'guru', 'orang_tua', 'admin', 'kepala_sekolah') DEFAULT 'siswa_sd'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa_sd', 'siswa_smp', 'guru', 'orang_tua', 'admin'))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('siswa_sd', 'siswa_smp', 'guru', 'orang_tua', 'admin') DEFAULT 'siswa_sd'");
        }
    }
};
