<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;

class SiswaTugasController extends Controller
{
    public function kumpul(Request $request)
    {
        if (!in_array($request->user()->role, ['siswa_sd', 'siswa_smp'])) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'tugas_id' => 'required|exists:tugas,id',
            'catatan_siswa' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png|max:10240',
        ]);

        $siswa = $request->user()->siswa;
        $tugas = Tugas::findOrFail($data['tugas_id']);

        if (!$siswa || !$siswa->kelas_id || $tugas->kelas_id !== $siswa->kelas_id) {
            return redirect()->back()->with('error', 'Tugas tidak sesuai dengan kelas Anda.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('tugas-siswa/' . $siswa->id, 'public');
        }

        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $data['tugas_id'], 'siswa_id' => $siswa->id],
            [
                'file_path' => $filePath ?? PengumpulanTugas::where('tugas_id', $data['tugas_id'])->where('siswa_id', $siswa->id)->value('file_path'),
                'catatan_siswa' => $data['catatan_siswa'] ?? null,
                'dikumpulkan_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
