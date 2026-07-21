<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\NilaiKti;
use App\Models\KtiBimbingan;
use Illuminate\Http\Request;

class SiswaKtiController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'siswa_smp') {
            return redirect()->back()->with('error', 'Hanya siswa SMP kelas 9 yang dapat mengakses portal KTI.');
        }

        $data = $request->validate([
            'bab' => 'required|in:Bab 1,Bab 2,Bab 3,Bab 4,Bab 5,Draft Akhir',
            'file_draft' => 'required',
            'catatan_siswa' => 'nullable|string|max:1000',
        ]);

        $siswa = Siswa::where('user_id', $request->user()->id)->firstOrFail();

        // Cari atau buat record NilaiKti
        $kti = NilaiKti::firstOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'judul_kti' => 'Belum Ditentukan',
                'current_bab' => 'Bab 1',
                'nilai_proses' => 0,
                'nilai_tulisan' => 0,
                'nilai_sidang' => 0,
                'nilai_akhir' => 0,
            ]
        );

        // Pastikan mengunggah sesuai bab aktif saat ini
        if ($kti->current_bab !== $data['bab']) {
            return redirect()->back()->with('error', 'Anda hanya bisa mengunggah draf untuk ' . $kti->current_bab . ' saat ini.');
        }

        // Cek jika ada pengajuan pending untuk bab yang sama
        $pending = KtiBimbingan::where('siswa_id', $siswa->id)
            ->where('bab', $data['bab'])
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->back()->with('error', 'Draf sebelumnya untuk ' . $data['bab'] . ' masih dalam proses peninjauan guru.');
        }

        // Proses upload file atau link
        $filePath = '';
        if ($request->hasFile('file_draft')) {
            $file = $request->file('file_draft');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            
            // Buat direktori jika belum ada
            if (!file_exists(public_path('uploads/drafts'))) {
                mkdir(public_path('uploads/drafts'), 0777, true);
            }
            
            $file->move(public_path('uploads/drafts'), $fileName);
            $filePath = '/uploads/drafts/' . $fileName;
        } else {
            // Jika berupa tautan text/url (misal Google Docs)
            $filePath = $request->input('file_draft');
        }

        KtiBimbingan::create([
            'siswa_id' => $siswa->id,
            'bab' => $data['bab'],
            'file_draft' => $filePath,
            'catatan_siswa' => $data['catatan_siswa'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Draf bimbingan ' . $data['bab'] . ' berhasil diunggah.');
    }
}
