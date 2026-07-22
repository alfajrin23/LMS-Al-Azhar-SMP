<?php

namespace App\Http\Controllers;

use App\Models\AdministrasiGuruChecklist;
use App\Models\JurnalSikap;
use App\Models\LaporanMengajar;
use App\Models\ProgramPengayaan;
use App\Models\ProgramRemedial;
use App\Models\Siswa;
use Illuminate\Http\Request;

class GuruLaporanController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input laporan mengajar');
        }

        if ($request->input('tipe') === 'jurnal_harian') {
            return $this->storeHarian($request);
        }

        return $this->storeLegacyReport($request);
    }

    public function storeHarian(Request $request)
    {
        $guru = $this->currentGuru($request);
        $data = $request->validate([
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|string|max:30',
            'tanggal' => 'required|date',
            'jam_ke' => 'required|string|max:50',
            'bahasan_materi' => 'required|string|max:2000',
            'keterangan' => 'nullable|string|max:2000',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
        ]);

        if (!$this->guruTeaches($guru, (int) $data['kelas_id'], (int) $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        LaporanMengajar::updateOrCreate(
            [
                'guru_id' => $guru->id,
                'tipe' => 'jurnal_harian',
                'tanggal' => $data['tanggal'],
                'kelas_id' => $data['kelas_id'],
                'mapel_id' => $data['mapel_id'],
                'jam_ke' => $data['jam_ke'],
            ],
            [
                'hari' => $data['hari'],
                'bahasan_materi' => $data['bahasan_materi'],
                'keterangan' => $data['keterangan'] ?? null,
                'tahun_ajaran' => $data['tahun_ajaran'] ?? '2026/2027',
                'semester' => $data['semester'] ?? 'Ganjil',
                'isi' => [
                    'bahasan_materi' => $data['bahasan_materi'],
                    'keterangan' => $data['keterangan'] ?? null,
                ],
            ]
        );

        return redirect()->back()->with('success', 'Jurnal harian mengajar berhasil disimpan.');
    }

    public function storeSikap(Request $request)
    {
        $guru = $this->currentGuru($request);
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'kejadian' => 'required|string|max:2000',
            'tindakan' => 'nullable|string|max:2000',
            'paraf' => 'nullable|string|max:100',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
        ]);

        $siswa = Siswa::findOrFail($data['siswa_id']);
        if (!$this->guruCanAccessSiswa($guru, $siswa)) {
            return redirect()->back()->with('error', 'Siswa tidak berada pada kelas yang Anda ampu.');
        }

        JurnalSikap::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $siswa->kelas_id,
            'guru_id' => $guru->id,
            'tanggal' => $data['tanggal'],
            'kejadian' => $data['kejadian'],
            'tindakan' => $data['tindakan'] ?? null,
            'paraf' => $data['paraf'] ?? null,
            'tahun_ajaran' => $data['tahun_ajaran'] ?? '2026/2027',
            'semester' => $data['semester'] ?? 'Ganjil',
        ]);

        return redirect()->back()->with('success', 'Jurnal sikap siswa berhasil disimpan.');
    }

    public function storePengayaan(Request $request)
    {
        $guru = $this->currentGuru($request);
        $data = $request->validate([
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'kompetensi_dasar' => 'nullable|string|max:500',
            'materi' => 'required|string|max:2000',
            'bentuk_pengayaan' => 'required|string|max:2000',
            'keterangan' => 'nullable|string|max:2000',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
        ]);

        if (!$this->guruTeaches($guru, (int) $data['kelas_id'], (int) $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        ProgramPengayaan::create(array_merge($data, [
            'guru_id' => $guru->id,
            'tahun_ajaran' => $data['tahun_ajaran'] ?? '2026/2027',
            'semester' => $data['semester'] ?? 'Ganjil',
        ]));

        return redirect()->back()->with('success', 'Program pengayaan berhasil disimpan.');
    }

    public function storeRemedial(Request $request)
    {
        $guru = $this->currentGuru($request);
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mapel_id' => 'required|exists:mapel,id',
            'kompetensi_dasar' => 'nullable|string|max:500',
            'materi' => 'required|string|max:2000',
            'nilai_sebelum' => 'nullable|numeric|min:0|max:100',
            'nilai_sesudah' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:2000',
            'paraf' => 'nullable|string|max:100',
            'status' => 'nullable|in:pending,selesai,tidak_lulus',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
        ]);

        $siswa = Siswa::findOrFail($data['siswa_id']);
        if (!$this->guruCanAccessSiswa($guru, $siswa, (int) $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Siswa atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        ProgramRemedial::create(array_merge($data, [
            'guru_id' => $guru->id,
            'kelas_id' => $siswa->kelas_id,
            'status' => $data['status'] ?? 'pending',
            'tahun_ajaran' => $data['tahun_ajaran'] ?? '2026/2027',
            'semester' => $data['semester'] ?? 'Ganjil',
        ]));

        return redirect()->back()->with('success', 'Program remedial berhasil disimpan.');
    }

    public function storeAdministrasi(Request $request)
    {
        $guru = $this->currentGuru($request);
        $data = $request->validate([
            'dokumen' => 'required|string|max:255',
            'status' => 'required|in:belum_lengkap,lengkap,terverifikasi',
            'tanggal_dilengkapi' => 'nullable|date',
            'catatan_reviewer' => 'nullable|string|max:2000',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
        ]);

        AdministrasiGuruChecklist::updateOrCreate(
            [
                'guru_id' => $guru->id,
                'dokumen' => $data['dokumen'],
                'tahun_ajaran' => $data['tahun_ajaran'] ?? '2026/2027',
                'semester' => $data['semester'] ?? 'Ganjil',
            ],
            [
                'status' => $data['status'],
                'tanggal_dilengkapi' => $data['tanggal_dilengkapi'] ?? null,
                'catatan_reviewer' => $data['catatan_reviewer'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Checklist administrasi berhasil disimpan.');
    }

    private function storeLegacyReport(Request $request)
    {
        $guru = $this->currentGuru($request);

        $data = $request->validate([
            'tipe' => 'required|in:harian,mingguan,bulanan',
            'tanggal' => 'required|date',
            'isi' => 'required',
        ]);

        $isiData = $this->normalizeLegacyIsi($data['tipe'], $data['isi']);

        LaporanMengajar::updateOrCreate(
            [
                'guru_id' => $guru->id,
                'tipe' => $data['tipe'],
                'tanggal' => $data['tanggal'],
            ],
            [
                'tahun_ajaran' => '2026/2027',
                'semester' => 'Ganjil',
                'isi' => $isiData,
            ]
        );

        return redirect()->back()->with('success', 'Laporan mengajar berhasil dikirim.');
    }

    private function normalizeLegacyIsi(string $tipe, mixed $isi): array
    {
        if (is_array($isi)) {
            return $isi;
        }

        if (is_string($isi)) {
            $decoded = json_decode($isi, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        $text = (string) $isi;

        return match ($tipe) {
            'harian' => [
                'checklists' => [],
                'kendala' => [
                    ['bidang' => 'Akademik', 'deskripsi' => $text, 'dampak' => '-', 'solusi' => '-', 'tindak_lanjut' => '-'],
                ],
                'pemetaan_masalah_siswa' => [],
                'refleksi' => [
                    'materi_tersampaikan' => '-',
                    'target_tercapai' => '-',
                    'kendala_terbesar' => $text,
                    'strategi_perbaikan' => '-',
                    'rencana_pertemuan' => '-',
                ],
            ],
            'mingguan' => [
                'rekap_pembelajaran' => [
                    ['hari' => '-', 'materi' => $text, 'kehadiran' => '-', 'ketuntasan' => '-', 'hots' => '-', 'catatan' => '-'],
                ],
                'evaluasi_akademik' => [],
                'analisis_kendala' => [],
                'pemetaan_siswa' => [],
                'tindak_lanjut' => [],
            ],
            default => [
                'capaian_belajar_bulanan' => [
                    ['elemen_cp' => 'Umum', 'target' => '-', 'capaian' => '-', 'persentase' => '-', 'keterangan' => $text],
                ],
                'evaluasi_dan_kendala' => [],
                'analisis_siswa' => [],
                'pemetaan_masalah_jangka_pendek' => [],
                'pemetaan_masalah_jangka_menengah' => [],
                'monitoring_kinerja_guru' => [],
                'rekomendasi_supervisor' => [],
            ],
        };
    }
}
