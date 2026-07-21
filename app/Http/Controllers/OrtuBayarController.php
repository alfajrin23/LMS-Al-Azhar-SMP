<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class OrtuBayarController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'orang_tua') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'spp_id' => 'required|exists:spps,id',
            'metode' => 'required|string|max:50',
        ]);

        $ortu = $this->currentOrangTua($request);
        $spp = \App\Models\Spp::findOrFail($data['spp_id']);
        if (!$this->orangTuaHasSiswa($ortu, $spp->siswa_id)) {
            return redirect()->back()->with('error', 'Tagihan tidak terhubung dengan anak Anda.');
        }

        Pembayaran::create([
            'spp_id' => $spp->id,
            'orang_tua_id' => $ortu->id,
            'tanggal_bayar' => now(),
            'jumlah' => $spp->jumlah,
            'metode' => $data['metode'],
            'status' => 'pending',
        ]);

        $spp->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'Pembayaran berhasil diajukan!');
    }
}
