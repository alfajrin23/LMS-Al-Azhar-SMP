<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function currentGuru(Request $request): Guru
    {
        return Guru::where('user_id', $request->user()->id)->firstOrFail();
    }

    protected function currentOrangTua(Request $request): OrangTua
    {
        return OrangTua::where('user_id', $request->user()->id)->firstOrFail();
    }

    protected function guruTeaches(Guru $guru, ?int $kelasId, ?int $mapelId = null): bool
    {
        $hasMapel = !$mapelId
            || (int) $guru->mapel_id === (int) $mapelId
            || $guru->mapels()->whereKey($mapelId)->exists();

        if (!$hasMapel) {
            return false;
        }

        if (!$kelasId) {
            return (bool) $mapelId;
        }

        $query = Jadwal::where('guru_id', $guru->id)->where('kelas_id', $kelasId);

        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }

        return $query->exists();
    }

    protected function guruCanAccessSiswa(Guru $guru, Siswa $siswa, ?int $mapelId = null): bool
    {
        return $this->guruTeaches($guru, $siswa->kelas_id, $mapelId);
    }

    protected function orangTuaHasSiswa(OrangTua $orangTua, int $siswaId): bool
    {
        return $orangTua->siswa()->whereKey($siswaId)->exists();
    }
}
