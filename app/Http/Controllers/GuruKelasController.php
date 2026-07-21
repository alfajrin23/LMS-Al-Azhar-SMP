<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruKelasController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat' => 'required|string|max:20',
        ]);

        $guru = Guru::query()->where('user_id', $request->user()->id)->firstOrFail();

        Kelas::create([
            'nama_kelas' => $data['nama_kelas'],
            'tingkat' => $data['tingkat'],
            'guru_id' => $guru->id,
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function getSiswaDetail($id)
    {
        $selectedSiswa = Siswa::findOrFail($id);
        $guruUser = Auth::user();
        $guru = $guruUser->guru;
        if (!$guru || !$this->guruCanAccessSiswa($guru, $selectedSiswa)) {
            abort(403);
        }

        return view('dashboard.guru-sections.siswa-detail', ['selectedSiswa' => $selectedSiswa, 'guru' => $guruUser]);
    }
}
