<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Materi;
use Illuminate\Http\Request;

class GuruMateriController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'tipe' => 'required|in:materi,referensi,tugas',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png,mp4,mp3|max:51200',
        ]);

        $guru = $this->currentGuru($request);
        $canUseMapel = $data['kelas_id'] ?? null
            ? $this->guruTeaches($guru, $data['kelas_id'], $data['mapel_id'])
            : $this->guruTeaches($guru, null, $data['mapel_id']);
        if (!$canUseMapel) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        $filePath = $request->file('file')->store('materi/' . $guru->id, 'public');

        $materi = Materi::create([
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $filePath,
            'tipe' => $data['tipe'],
            'mapel_id' => $data['mapel_id'],
            'kelas_id' => $data['kelas_id'] ?? null,
            'guru_id' => $guru->id,
            'status' => 'draft',
        ]);

        // Evaluate checklist to update status
        $checklist = $materi->checklist();
        if ($checklist['is_ready']) {
            $materi->update(['status' => 'pending']);
        }

        return redirect()->back()->with('success', 'Materi berhasil diupload! Status: ' . ($materi->status === 'pending' ? 'Menunggu Persetujuan' : 'Draft (Belum Lengkap)'));
    }

    public function update(Request $request, Materi $materi)
    {
        $guru = $this->currentGuru($request);
        if ($request->user()->role !== 'guru' || $materi->guru_id !== $guru->id) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'tipe' => 'required|in:materi,referensi,tugas',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png,mp4,mp3|max:51200',
        ]);

        $canUseMapel = $data['kelas_id'] ?? null
            ? $this->guruTeaches($guru, $data['kelas_id'], $data['mapel_id'])
            : $this->guruTeaches($guru, null, $data['mapel_id']);
        if (!$canUseMapel) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        $updateData = [
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tipe' => $data['tipe'],
            'mapel_id' => $data['mapel_id'],
            'kelas_id' => $data['kelas_id'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materi/' . $guru->id, 'public');
            $updateData['file_path'] = $filePath;
        }

        $materi->update($updateData);

        // Re-evaluate checklist
        $checklist = $materi->checklist();
        $newStatus = $checklist['is_ready'] ? 'pending' : 'draft';
        $materi->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Materi berhasil diperbarui! Status: ' . ($newStatus === 'pending' ? 'Menunggu Persetujuan' : 'Draft (Belum Lengkap)'));
    }

    public function download(Request $request, Materi $materi)
    {
        if (!$this->canDownloadMateri($request, $materi)) {
            abort(403);
        }

        $path = storage_path('app/public/' . $materi->file_path);

        if (!file_exists($path)) {
            return abort(404, 'File tidak ditemukan.');
        }

        return response()->download($path);
    }

    private function canDownloadMateri(Request $request, Materi $materi): bool
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'kepala_sekolah'], true)) {
            return true;
        }

        if ($user->role === 'guru') {
            return $materi->guru_id === $user->guru?->id;
        }

        if (in_array($user->role, ['siswa_sd', 'siswa_smp'], true)) {
            $siswa = $user->siswa;
            return $materi->status === 'approved'
                && ($materi->kelas_id === null || $materi->kelas_id === $siswa?->kelas_id);
        }

        if ($user->role === 'orang_tua') {
            $ortu = $user->orangTua;
            $kelasIds = $ortu ? $ortu->siswa()->whereNotNull('kelas_id')->pluck('siswa.kelas_id') : collect();
            return $materi->status === 'approved'
                && ($materi->kelas_id === null || $kelasIds->contains($materi->kelas_id));
        }

        return false;
    }


    public function destroy(Request $request, Materi $materi)
    {
        if ($request->user()->role !== 'guru' || $materi->guru_id !== $this->currentGuru($request)->id) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $materi->delete();

        return redirect()->back()->with('success', 'Materi berhasil dihapus!');
    }

    public function approve(Request $request, Materi $materi)
    {
        if ($request->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $request->validate([
            'skor_kauniyah' => 'required|integer|min:1|max:5',
            'skor_bilingual' => 'required|integer|min:1|max:5',
            'skor_ai' => 'required|integer|min:1|max:5',
        ]);

        $materi->update([
            'status' => 'approved',
            'skor_kauniyah' => $data['skor_kauniyah'],
            'skor_bilingual' => $data['skor_bilingual'],
            'skor_ai' => $data['skor_ai'],
        ]);

        return redirect()->back()->with('success', 'Materi ajar berhasil diaudit dan disetujui!');
    }


    public function reject(Request $request, Materi $materi)
    {
        if ($request->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $materi->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Materi ajar ditolak!');
    }
}
