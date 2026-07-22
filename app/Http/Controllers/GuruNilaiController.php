<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Remedial;
use Illuminate\Http\Request;

class GuruNilaiController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input nilai');
        }

        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mapel_id' => 'required|exists:mapel,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'jenis_nilai' => 'required|in:biasa,unggulan',
            'nilai_bahasa' => 'nullable|numeric|min:0|max:100',
            'jenis_rapor' => 'nullable|in:akademik,english',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
            'lingkup_materi' => 'nullable|string|max:255',
            'tujuan_pembelajaran' => 'nullable|string|max:2000',
            'tp_scores' => 'nullable|array',
            'tugas_scores' => 'nullable|array',
            'sumatif_scores' => 'nullable|array',
            'nilai_sumatif' => 'nullable|numeric|min:0|max:100',
            'capaian_kompetensi' => 'nullable|string|max:2000',
            'kompetensi_dikuasai' => 'nullable|string|max:2000',
            'kompetensi_perlu_ditingkatkan' => 'nullable|string|max:2000',
            'catatan' => 'nullable|string|max:2000',
        ]);

        $siswa = Siswa::with('kelas')->findOrFail($data['siswa_id']);
        $guru = $this->currentGuru($request);
        if (!$this->guruCanAccessSiswa($guru, $siswa, $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Siswa atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        $kkm = $this->kkmForSiswa($siswa);

        $pendingRemedial = Remedial::where('siswa_id', $siswa->id)
            ->where('mapel_id', $data['mapel_id'])
            ->where('status', 'pending')
            ->first();

        $inputNilai = $data['nilai'];
        $finalNilai = $inputNilai;

        if ($pendingRemedial) {
            $finalNilai = min($inputNilai, $kkm);
        }

        $nilaiRecord = Nilai::updateOrCreate(
            [
                'siswa_id' => $data['siswa_id'], 
                'mapel_id' => $data['mapel_id'],
                'jenis_nilai' => $data['jenis_nilai'],
                'jenis_rapor' => $data['jenis_rapor'] ?? 'akademik',
                'tahun_ajaran' => $data['tahun_ajaran'] ?? '2025/2026',
                'semester' => $data['semester'] ?? 'Genap',
                'lingkup_materi' => $data['lingkup_materi'] ?? null,
            ],
            [
                'nilai' => $finalNilai,
                'nilai_bahasa' => $data['nilai_bahasa'] ?? null,
                'tujuan_pembelajaran' => $data['tujuan_pembelajaran'] ?? null,
                'tp_scores' => $data['tp_scores'] ?? null,
                'tugas_scores' => $data['tugas_scores'] ?? null,
                'sumatif_scores' => $data['sumatif_scores'] ?? null,
                'nilai_sumatif' => $data['nilai_sumatif'] ?? null,
                'capaian_kompetensi' => $data['capaian_kompetensi'] ?? null,
                'kompetensi_dikuasai' => $data['kompetensi_dikuasai'] ?? null,
                'kompetensi_perlu_ditingkatkan' => $data['kompetensi_perlu_ditingkatkan'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        if ($pendingRemedial) {
            if ($finalNilai >= $kkm) {
                $pendingRemedial->update(['status' => 'selesai']);
            }
        } else {
            if ($finalNilai < $kkm) {
                Remedial::create([
                    'siswa_id' => $siswa->id,
                    'mapel_id' => $data['mapel_id'],
                    'nilai_id' => $nilaiRecord->id,
                    'nilai_asal' => $finalNilai,
                    'deadline' => now()->addDays(3)->format('Y-m-d'),
                    'status' => 'pending'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan!');
    }

    private function kkmForSiswa(Siswa $siswa): int
    {
        $kelas = $siswa->kelas;
        $jenjang = strtoupper((string) ($kelas->jenjang ?? ''));
        $namaKelas = (string) ($kelas->nama_kelas ?? '');
        $kodeKelas = (string) ($kelas->kode_kelas ?? '');

        $isSd = $jenjang === 'SD'
            || str_contains(strtoupper($namaKelas), 'SD')
            || preg_match('/^(KELAS\s*)?[1-6]\b/i', $namaKelas)
            || preg_match('/^[1-6]\b/i', $kodeKelas);

        return (int) setting($isSd ? 'kkm_sd' : 'kkm_smp', $isSd ? 70 : 75);
    }
}
