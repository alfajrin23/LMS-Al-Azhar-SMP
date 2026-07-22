<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\OrangTua;
use App\Models\Pesan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;

class GuruPesanController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'penerima_id' => 'required|exists:users,id',
            'siswa_id' => 'required|exists:siswa,id',
            'subjek' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:80',
            'isi' => 'required|string|max:4000',
            'parent_message_id' => 'nullable|exists:pesan,id',
        ]);

        $guru = $this->currentGuru($request);
        $siswa = Siswa::findOrFail($data['siswa_id']);
        if (!$this->guruCanAccessSiswa($guru, $siswa)) {
            return redirect()->back()->with('error', 'Siswa tidak berada pada kelas yang Anda ampu.');
        }

        $penerima = User::findOrFail($data['penerima_id']);
        $canReceive = $penerima->id === $siswa->user_id;

        if ($penerima->role === 'orang_tua') {
            $ortu = OrangTua::where('user_id', $penerima->id)->first();
            $canReceive = $ortu?->siswa()->whereKey($siswa->id)->exists() ?? false;
        }

        if (!$canReceive) {
            return redirect()->back()->with('error', 'Penerima tidak terkait dengan siswa tersebut.');
        }

        $parent = isset($data['parent_message_id']) ? Pesan::find($data['parent_message_id']) : null;
        $threadId = $parent?->thread_id ?: 'pesan-'.uniqid();

        $pesan = Pesan::create([
            'pengirim_id' => $request->user()->id,
            'penerima_id' => $penerima->id,
            'siswa_id' => $siswa->id,
            'subjek' => $data['subjek'] ?? $parent?->subjek ?? 'Buku Penghubung',
            'kategori' => $data['kategori'] ?? $parent?->kategori ?? 'Lainnya',
            'isi' => $data['isi'],
            'tanggal' => now(),
            'dibaca' => false,
            'parent_message_id' => $parent?->id,
            'thread_id' => $threadId,
        ]);

        if (!$parent) {
            $pesan->update(['thread_id' => 'pesan-'.$pesan->id]);
        }

        return redirect()->back()->with('success', 'Pesan Buku Penghubung berhasil dikirim.');
    }
}
