<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\BandingNilai;
use Illuminate\Http\Request;

class SiswaBandingController extends Controller
{
    public function store(Request $request)
    {
        if (!in_array($request->user()->role, ['siswa_sd', 'siswa_smp'])) {
            return redirect()->back()->with('error', 'Hanya siswa yang bisa mengajukan banding nilai');
        }

        $data = $request->validate([
            'nilai_id' => 'required|exists:nilai,id',
            'alasan_siswa' => 'required|string|min:5|max:1000',
        ]);

        $siswa = Siswa::where('user_id', $request->user()->id)->firstOrFail();
        $nilai = Nilai::where('id', $data['nilai_id'])->where('siswa_id', $siswa->id)->firstOrFail();

        // Check if already appealed
        $existing = BandingNilai::where('nilai_id', $nilai->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan banding untuk nilai ini.');
        }

        BandingNilai::create([
            'nilai_id' => $nilai->id,
            'siswa_id' => $siswa->id,
            'alasan_siswa' => $data['alasan_siswa'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan banding nilai berhasil dikirim.');
    }
}
