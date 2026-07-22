<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruAbsensiController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input absensi');
        }

        return $request->has('absensi')
            ? $this->storeBulk($request)
            : $this->storeSingle($request);
    }

    private function storeBulk(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'tanggal' => 'required|date',
            'pertemuan' => 'nullable|string|max:50',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
            'absensi' => 'required|array|min:1',
            'absensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
            'absensi.*.keterangan' => 'nullable|string|max:500',
        ]);

        $guru = $this->currentGuru($request);
        if (!$this->guruTeaches($guru, (int) $data['kelas_id'], (int) $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Anda tidak mengajar kelas atau mapel yang dipilih.');
        }

        $allowedStudentIds = Siswa::query()
            ->where('kelas_id', $data['kelas_id'])
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        return DB::transaction(function () use ($data, $guru, $allowedStudentIds) {
            foreach ($data['absensi'] as $siswaId => $row) {
                if (!in_array((string) $siswaId, $allowedStudentIds, true)) {
                    continue;
                }

                Kehadiran::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'tanggal' => $data['tanggal'],
                        'mapel_id' => $data['mapel_id'],
                        'pertemuan' => $data['pertemuan'] ?? '1',
                    ],
                    [
                        'kelas_id' => $data['kelas_id'],
                        'guru_id' => $guru->id,
                        'status' => $row['status'],
                        'keterangan' => $row['keterangan'] ?? null,
                        'tahun_ajaran' => $data['tahun_ajaran'] ?? '2026/2027',
                        'semester' => $data['semester'] ?? 'Ganjil',
                    ]
                );
            }

            return redirect()->back()->with('success', 'Absensi kelas berhasil disimpan.');
        });
    }

    private function storeSingle(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'keterangan' => 'nullable|string|max:500',
            'mapel_id' => 'nullable|exists:mapel,id',
            'pertemuan' => 'nullable|string|max:50',
        ]);

        $guru = $this->currentGuru($request);
        $siswa = Siswa::findOrFail($data['siswa_id']);
        if (!$this->guruCanAccessSiswa($guru, $siswa, $data['mapel_id'] ?? null)) {
            return redirect()->back()->with('error', 'Siswa tidak berada pada kelas yang Anda ampu.');
        }

        Kehadiran::updateOrCreate(
            [
                'siswa_id' => $data['siswa_id'],
                'tanggal' => $data['tanggal'],
                'mapel_id' => $data['mapel_id'] ?? null,
                'pertemuan' => $data['pertemuan'] ?? '1',
            ],
            [
                'kelas_id' => $siswa->kelas_id,
                'guru_id' => $guru->id,
                'status' => $data['status'],
                'keterangan' => $data['keterangan'] ?? null,
                'tahun_ajaran' => '2026/2027',
                'semester' => 'Ganjil',
            ]
        );

        return redirect()->back()->with('success', 'Absensi berhasil disimpan.');
    }
}
