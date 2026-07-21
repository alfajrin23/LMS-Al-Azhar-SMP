<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\KondisiKelas;
use Illuminate\Http\Request;

class GuruKondisiKelasController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang dapat mengisi kondisi kelas.');
        }

        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'hubungan_guru_siswa' => 'required|integer|min:1|max:5',
            'siswa_nyaman' => 'required|integer|min:1|max:5',
            'siswa_minta_bantuan' => 'required|integer|min:1|max:5',
        ]);

        $guru = Guru::query()->where('user_id', $request->user()->id)->firstOrFail();
        $tanggal = now()->format('Y-m-d');

        KondisiKelas::updateOrCreate(
            [
                'guru_id' => $guru->id,
                'kelas_id' => $data['kelas_id'],
                'tanggal' => $tanggal,
            ],
            [
                'hubungan_guru_siswa' => $data['hubungan_guru_siswa'],
                'siswa_nyaman' => $data['siswa_nyaman'],
                'siswa_minta_bantuan' => $data['siswa_minta_bantuan'],
            ]
        );

        return redirect()->back()->with('success', 'Kondisi kelas berhasil disimpan!');
    }
}
