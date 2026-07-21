<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Tugas;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->get('q');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = collect();

        $role = $request->user()->role;

        if ($role === 'admin' || $role === 'kepala_sekolah') {
            $results = $results->concat(
                Siswa::where('nama', 'like', "%{$q}%")->limit(5)->get()
                    ->map(fn($s) => ['type' => 'Siswa', 'label' => $s->nama, 'url' => '#'])
            );
            $results = $results->concat(
                Guru::whereHas('user', fn($q2) => $q2->where('name', 'like', "%{$q}%"))
                    ->limit(5)->get()
                    ->map(fn($g) => ['type' => 'Guru', 'label' => $g->user->name, 'url' => '#'])
            );
            $results = $results->concat(
                Tugas::where('judul', 'like', "%{$q}%")->limit(5)->get()
                    ->map(fn($t) => ['type' => 'Tugas', 'label' => $t->judul, 'url' => '#'])
            );
        } elseif ($role === 'guru') {
            $guru = $request->user()->guru;
            $kelasIds = $guru ? Jadwal::where('guru_id', $guru->id)->pluck('kelas_id')->unique() : collect();
            $results = $results->concat(
                Siswa::whereIn('kelas_id', $kelasIds)
                    ->where('nama', 'like', "%{$q}%")
                    ->limit(5)
                    ->get()
                    ->map(fn($s) => ['type' => 'Siswa', 'label' => $s->nama, 'url' => '#'])
            );
            $results = $results->concat(
                Tugas::where('guru_id', $guru?->id)
                    ->where('judul', 'like', "%{$q}%")
                    ->limit(5)
                    ->get()
                    ->map(fn($t) => ['type' => 'Tugas', 'label' => $t->judul, 'url' => '#'])
            );
        } elseif (in_array($role, ['siswa_sd', 'siswa_smp'], true)) {
            $siswa = $request->user()->siswa;
            $results = $results->concat(
                Tugas::where('kelas_id', $siswa?->kelas_id)
                    ->where('judul', 'like', "%{$q}%")
                    ->limit(5)
                    ->get()
                    ->map(fn($t) => ['type' => 'Tugas', 'label' => $t->judul, 'url' => '#'])
            );
        } elseif ($role === 'orang_tua') {
            $ortu = $request->user()->orangTua;
            $results = $results->concat(
                ($ortu ? $ortu->siswa()->where('nama', 'like', "%{$q}%")->limit(5)->get() : collect())
                    ->map(fn($s) => ['type' => 'Siswa', 'label' => $s->nama, 'url' => '#'])
            );
        }

        return response()->json($results->take(10)->values());
    }
}
