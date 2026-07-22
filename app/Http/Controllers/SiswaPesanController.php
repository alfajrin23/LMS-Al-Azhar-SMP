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
            'subjek' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:80',
            'isi' => 'required|string|max:4000',
            'parent_message_id' => 'nullable|exists:pesan,id',
        ]);

        $siswa = $request->user()->siswa;
        $guru = Guru::where('user_id', $data['penerima_id'])->first();
        $canMessage = $siswa && $guru && \App\Models\Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $siswa->kelas_id)
            ->exists();

        if (!$canMessage) {
            return redirect()->back()->with('error', 'Tujuan pesan tidak terkait dengan kelas Anda.');
        }

        $parent = isset($data['parent_message_id']) ? Pesan::find($data['parent_message_id']) : null;
        $threadId = $parent?->thread_id ?: 'pesan-'.uniqid();

        $pesan = Pesan::create([
            'pengirim_id' => $request->user()->id,
            'penerima_id' => $data['penerima_id'],
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

        return redirect()->back()->with('success', 'Pesan berhasil dikirim!');
    }
}
