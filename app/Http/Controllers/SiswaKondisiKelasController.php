<?php

namespace App\Http\Controllers;

use App\Models\KondisiKelas;
use Illuminate\Http\Request;

class SiswaKondisiKelasController extends Controller
{
    public function store(Request $request)
    {
        $role = $request->user()->role;
        if ($role !== 'siswa_sd' && $role !== 'siswa_smp') {
            return redirect()->back()->with('error', 'Hanya siswa yang dapat mengisi kondisi kelas.');
        }

        $siswa = $request->user()->siswa;
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        if (!$siswa->kelas_id) {
            return redirect()->back()->with('error', 'Anda belum terdaftar di kelas, penilaian tidak dapat dikirim.');
        }

        $data = $request->validate([
            'hubungan_guru_siswa' => 'required|integer|min:1|max:5',
            'siswa_nyaman' => 'required|integer|min:1|max:5',
            'siswa_minta_bantuan' => 'required|integer|min:1|max:5',
        ]);

        $tanggal = now()->format('Y-m-d');

        KondisiKelas::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'tanggal' => $tanggal,
            ],
            [
                'kelas_id' => $siswa->kelas_id,
                'hubungan_guru_siswa' => $data['hubungan_guru_siswa'],
                'siswa_nyaman' => $data['siswa_nyaman'],
                'siswa_minta_bantuan' => $data['siswa_minta_bantuan'],
            ]
        );

        return redirect()->back()->with('success', 'Terima kasih, penilaian kondisi kelas Anda berhasil dikirim!');
    }
}
