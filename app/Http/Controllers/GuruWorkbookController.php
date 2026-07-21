<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Workbook;
use App\Models\WorkbookSoal;
use Illuminate\Http\Request;

class GuruWorkbookController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'tipe' => 'required|in:tugas_pengganti,remedial,penugasan_di_rumah',
        ]);

        $guru = $this->currentGuru($request);
        $canUseMapel = $data['kelas_id'] ?? null
            ? $this->guruTeaches($guru, $data['kelas_id'], $data['mapel_id'])
            : $this->guruTeaches($guru, null, $data['mapel_id']);
        if (!$canUseMapel) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        Workbook::create([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'mapel_id' => $data['mapel_id'],
            'kelas_id' => $data['kelas_id'] ?? null,
            'guru_id' => $guru->id,
            'tipe' => $data['tipe'],
        ]);

        return redirect('/dashboard')->with('active_tab', 'workbook')->with('success', 'Workbook berhasil dibuat!');
    }

    public function storeSoal(Request $request, Workbook $workbook)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $guru = $this->currentGuru($request);
        if ($workbook->guru_id !== $guru->id) {
            return redirect()->back()->with('error', 'Workbook bukan milik Anda.');
        }

        $data = $request->validate([
            'soal' => 'required|string',
            'tipe' => 'required|in:pg,essay',
            'pilihan_a' => 'nullable|string',
            'pilihan_b' => 'nullable|string',
            'pilihan_c' => 'nullable|string',
            'pilihan_d' => 'nullable|string',
            'jawaban_benar' => 'nullable|string|max:10',
            'bobot' => 'nullable|integer|min:1|max:100',
        ]);

        $nomorTerakhir = $workbook->soals()->max('nomor') ?? 0;

        WorkbookSoal::create([
            'workbook_id' => $workbook->id,
            'nomor' => $nomorTerakhir + 1,
            'soal' => $data['soal'],
            'tipe' => $data['tipe'],
            'pilihan_a' => $data['pilihan_a'] ?? null,
            'pilihan_b' => $data['pilihan_b'] ?? null,
            'pilihan_c' => $data['pilihan_c'] ?? null,
            'pilihan_d' => $data['pilihan_d'] ?? null,
            'jawaban_benar' => $data['jawaban_benar'] ?? null,
            'bobot' => $data['bobot'] ?? 1,
        ]);

        return redirect('/dashboard')->with('active_tab', 'workbook')->with('success', 'Soal berhasil ditambahkan!');
    }
}
