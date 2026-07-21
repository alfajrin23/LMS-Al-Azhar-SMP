<?php

namespace App\Http\Controllers;

use App\Models\NilaiKti;
use App\Models\Siswa;
use App\Models\KtiBimbingan;
use Illuminate\Http\Request;

class GuruKtiController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input nilai KTI');
        }

        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'judul_kti' => 'required|string|max:255',
            'nilai_proses' => 'required|numeric|min:0|max:100',
            'nilai_tulisan' => 'required|numeric|min:0|max:100',
            'nilai_sidang' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $guru = $this->currentGuru($request);
        $siswa = Siswa::findOrFail($data['siswa_id']);
        if (!$this->guruCanAccessSiswa($guru, $siswa)) {
            return redirect()->back()->with('error', 'Siswa tidak berada pada kelas yang Anda ampu.');
        }

        // Hitung nilai akhir dengan bobot
        $nilaiAkhir = ($data['nilai_proses'] * 0.40) + ($data['nilai_tulisan'] * 0.30) + ($data['nilai_sidang'] * 0.30);

        NilaiKti::updateOrCreate(
            ['siswa_id' => $data['siswa_id']],
            [
                'judul_kti' => $data['judul_kti'],
                'current_bab' => 'Selesai', // KTI telah selesai dinilai
                'nilai_proses' => $data['nilai_proses'],
                'nilai_tulisan' => $data['nilai_tulisan'],
                'nilai_sidang' => $data['nilai_sidang'],
                'nilai_akhir' => round($nilaiAkhir, 2),
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Nilai KTI berhasil disimpan!');
    }

    public function prosesBimbingan(Request $request, KtiBimbingan $bimbingan)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa memproses draf KTI.');
        }

        $data = $request->validate([
            'status' => 'required|in:disetujui,revisi',
            'catatan_guru' => 'nullable|string|max:1000',
        ]);

        $guru = $this->currentGuru($request);
        if (!$bimbingan->siswa || !$this->guruCanAccessSiswa($guru, $bimbingan->siswa)) {
            return redirect()->back()->with('error', 'Draf KTI tidak terkait dengan kelas yang Anda ampu.');
        }

        $bimbingan->status = $data['status'];
        $bimbingan->catatan_guru = $data['catatan_guru'] ?? null;
        $bimbingan->save();

        if ($data['status'] === 'disetujui') {
            $kti = NilaiKti::where('siswa_id', $bimbingan->siswa_id)->first();
            if ($kti) {
                $nextBabMap = [
                    'Bab 1' => 'Bab 2',
                    'Bab 2' => 'Bab 3',
                    'Bab 3' => 'Bab 4',
                    'Bab 4' => 'Bab 5',
                    'Bab 5' => 'Draft Akhir',
                    'Draft Akhir' => 'Siap Sidang',
                    'Siap Sidang' => 'Siap Sidang',
                    'Selesai' => 'Selesai',
                ];

                $current = $kti->current_bab;
                $next = $nextBabMap[$current] ?? $current;

                $kti->update(['current_bab' => $next]);
            }
        }

        $statusText = $data['status'] === 'disetujui' ? 'disetujui (ACC)' : 'direvisi';
        return redirect()->back()->with('success', 'Draf bimbingan berhasil diproses dengan status: ' . $statusText);
    }

    public function jadwalSidang(Request $request, NilaiKti $kti)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa menjadwalkan sidang KTI.');
        }

        $data = $request->validate([
            'jadwal_sidang' => 'required|date',
        ]);

        $guru = $this->currentGuru($request);
        if (!$kti->siswa || !$this->guruCanAccessSiswa($guru, $kti->siswa)) {
            return redirect()->back()->with('error', 'KTI tidak terkait dengan kelas yang Anda ampu.');
        }

        $kti->update([
            'jadwal_sidang' => $data['jadwal_sidang']
        ]);

        return redirect()->back()->with('success', 'Jadwal sidang KTI berhasil disimpan!');
    }
}
