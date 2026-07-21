<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;

class NilaiTugasController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'pengumpulan_id' => 'required|exists:pengumpulan_tugas,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan_guru' => 'nullable|string|max:2000',
        ]);

        $pengumpulan = PengumpulanTugas::findOrFail($data['pengumpulan_id']);
        if ($pengumpulan->tugas?->guru_id !== $request->user()->guru?->id) {
            return redirect()->back()->with('error', 'Pengumpulan tugas bukan milik tugas Anda.');
        }

        $pengumpulan->update([
            'nilai' => $data['nilai'],
            'catatan_guru' => $data['catatan_guru'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Nilai berhasil diberikan!');
    }
}
