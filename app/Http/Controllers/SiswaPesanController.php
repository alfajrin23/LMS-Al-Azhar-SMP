<?php

namespace App\Http\Controllers;

use App\Models\Pesan;
use App\Models\Guru;
use Illuminate\Http\Request;

class SiswaPesanController extends Controller
{
    public function store(Request $request)
    {
        if (!in_array($request->user()->role, ['siswa_sd', 'siswa_smp'])) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'penerima_id' => 'required|exists:users,id',
            'isi' => 'required|string|max:2000',
        ]);

        $siswa = $request->user()->siswa;
        $guru = Guru::where('user_id', $data['penerima_id'])->first();
        $canMessage = $siswa && $guru && \App\Models\Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $siswa->kelas_id)
            ->exists();

        if (!$canMessage) {
            return redirect()->back()->with('error', 'Tujuan pesan tidak terkait dengan kelas Anda.');
        }

        Pesan::create([
            'pengirim_id' => $request->user()->id,
            'penerima_id' => $data['penerima_id'],
            'isi' => $data['isi'],
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim!');
    }
}
