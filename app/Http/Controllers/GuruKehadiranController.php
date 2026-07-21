<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\KehadiranGuru;
use Illuminate\Http\Request;

class GuruKehadiranController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $guru = Guru::where('user_id', $request->user()->id)->firstOrFail();
        $tanggal = now()->format('Y-m-d');
        
        $kehadiran = KehadiranGuru::firstOrCreate(
            ['guru_id' => $guru->id, 'tanggal' => $tanggal],
            ['status' => 'belum_absen']
        );

        $action = $request->input('action');

        if ($action === 'masuk') {
            if ($kehadiran->waktu_masuk) {
                return redirect()->back()->with('error', 'Anda sudah absen masuk hari ini.');
            }
            $kehadiran->update([
                'waktu_masuk' => now()->format('H:i:s'),
                'status' => 'hadir'
            ]);
            return redirect()->back()->with('success', 'Berhasil absen masuk!');
        } elseif ($action === 'pulang') {
            if (!$kehadiran->waktu_masuk) {
                return redirect()->back()->with('error', 'Anda belum absen masuk.');
            }
            if ($kehadiran->waktu_pulang) {
                return redirect()->back()->with('error', 'Anda sudah absen pulang hari ini.');
            }
            $kehadiran->update([
                'waktu_pulang' => now()->format('H:i:s')
            ]);
            return redirect()->back()->with('success', 'Berhasil absen pulang!');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }
}
