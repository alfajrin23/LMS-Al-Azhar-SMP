<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\TahfidzProgress;
use Illuminate\Http\Request;

class KepalaTahfidzProgressController extends Controller
{
    public function store(Request $request)
    {
        abort_unless($request->user()?->role === 'kepala_sekolah', 403);

        $data = $request->validate([
            'target_mode' => 'required|in:siswa,kelas,kelas_quran,semua',
            'siswa_id' => 'required_if:target_mode,siswa|nullable|exists:siswa,id',
            'kelas_id' => 'required_if:target_mode,kelas|nullable|exists:kelas,id',
            'kelas_quran_id' => 'required_if:target_mode,kelas_quran|nullable|exists:kelas_quran,id',
            'surah' => 'nullable|string|max:255',
            'ayat_mulai' => 'nullable|integer|min:1',
            'ayat_selesai' => 'nullable|integer|min:1|gte:ayat_mulai',
            'juz_dihafal' => 'nullable|integer|min:0|max:30',
            'total_ayat' => 'nullable|integer|min:0|max:6236',
            'progress_percent' => 'required|numeric|min:0|max:100',
            'target_deskripsi' => 'nullable|string|max:255',
            'status' => 'required|in:belum_mulai,berproses,lancar,perlu_murojaah',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $query = Siswa::query()->with('user')->whereHas('user', function ($q) {
            $q->whereIn('role', ['siswa_sd', 'siswa_smp']);
        });

        if ($data['target_mode'] === 'siswa') {
            $query->where('id', $data['siswa_id']);
        } elseif ($data['target_mode'] === 'kelas') {
            $query->where('kelas_id', $data['kelas_id']);
        } elseif ($data['target_mode'] === 'kelas_quran') {
            $query->where('kelas_quran_id', $data['kelas_quran_id']);
        }

        $siswaList = $query->get();
        if ($siswaList->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang cocok dengan target progress Tahfidz.');
        }

        $progressPercent = round((float) $data['progress_percent'], 2);
        $juzDihafal = isset($data['juz_dihafal'])
            ? (int) $data['juz_dihafal']
            : min(30, (int) floor(($progressPercent / 100) * 30));
        $totalAyat = isset($data['total_ayat'])
            ? (int) $data['total_ayat']
            : min(6236, (int) round(($progressPercent / 100) * 6236));

        foreach ($siswaList as $siswa) {
            TahfidzProgress::updateOrCreate(
                ['siswa_id' => $siswa->id],
                [
                    'kelas_id' => $siswa->kelas_id,
                    'kelas_quran_id' => $siswa->kelas_quran_id,
                    'surah' => $data['surah'] ?? null,
                    'ayat_mulai' => $data['ayat_mulai'] ?? null,
                    'ayat_selesai' => $data['ayat_selesai'] ?? null,
                    'juz_dihafal' => $juzDihafal,
                    'total_ayat' => $totalAyat,
                    'progress_percent' => $progressPercent,
                    'target_deskripsi' => $data['target_deskripsi'] ?? null,
                    'status' => $data['status'],
                    'catatan' => $data['catatan'] ?? null,
                    'updated_by' => $request->user()->id,
                ]
            );
        }

        return redirect()->to(route('dashboard', ['tab' => 'karya_tahfidz']))
            ->with('success', 'Progress Tahfidz berhasil diperbarui untuk ' . $siswaList->count() . ' siswa.');
    }
}
