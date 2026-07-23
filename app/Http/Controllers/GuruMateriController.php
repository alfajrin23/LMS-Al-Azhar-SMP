<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Materi;
use App\Models\MateriApprovalHistory;
use App\Support\SmpLearningDocumentInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruMateriController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $data = $this->validatedMateri($request, true);
        $guru = $this->currentGuru($request);

        if (!$this->guruTeaches($guru, $data['kelas_id'] ?? null, $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        return DB::transaction(function () use ($request, $data, $guru) {
            $filePath = $request->hasFile('file')
                ? $request->file('file')->store('materi/'.$guru->id, 'public')
                : '';

            $materi = Materi::create([
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'file_path' => $filePath,
                'tipe' => 'bahan_ajar',
                'kategori' => $data['kategori'],
                'kode' => $data['kode'] ?? null,
                'isi' => $data['isi'] ?? null,
                'mapel_id' => $data['mapel_id'],
                'kelas_id' => $data['kelas_id'] ?? null,
                'guru_id' => $guru->id,
                'tahun_ajaran' => $data['tahun_ajaran'] ?? '2026/2027',
                'semester' => $data['semester'] ?? 'Ganjil',
                'versi' => 1,
                'status' => 'draft',
            ]);

            $this->recordHistory($materi, $request, 'created', null, 'draft');

            if (($data['submit_action'] ?? 'draft') === 'request_approval') {
                $result = $this->submitForApproval($materi, $request, false);
                if ($result !== true) {
                    return $result;
                }
            }

            return redirect()->back()->with('success', 'Bahan Ajar berhasil disimpan.');
        });
    }

    public function update(Request $request, Materi $materi)
    {
        $guru = $this->currentGuru($request);
        if ($request->user()->role !== 'guru' || $materi->guru_id !== $guru->id) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        if (!$materi->isEditableByGuru()) {
            return redirect()->back()->with('error', 'Bahan Ajar yang sedang menunggu review atau sudah disetujui tidak dapat diedit.');
        }

        $data = $this->validatedMateri($request, false);

        if (!$this->guruTeaches($guru, $data['kelas_id'] ?? null, $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        return DB::transaction(function () use ($request, $data, $guru, $materi) {
            $updateData = [
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'tipe' => 'bahan_ajar',
                'kategori' => $data['kategori'],
                'kode' => $data['kode'] ?? null,
                'isi' => $data['isi'] ?? null,
                'mapel_id' => $data['mapel_id'],
                'kelas_id' => $data['kelas_id'] ?? null,
                'tahun_ajaran' => $data['tahun_ajaran'] ?? $materi->tahun_ajaran ?? '2026/2027',
                'semester' => $data['semester'] ?? $materi->semester ?? 'Ganjil',
            ];

            if ($request->hasFile('file')) {
                $updateData['file_path'] = $request->file('file')->store('materi/'.$guru->id, 'public');
                $updateData['versi'] = ((int) ($materi->versi ?? 1)) + 1;
            }

            $materi->update($updateData);
            $this->recordHistory($materi, $request, 'updated', $materi->status, $materi->status);

            if (($data['submit_action'] ?? 'draft') === 'request_approval') {
                $result = $this->submitForApproval($materi->refresh(), $request, true);
                if ($result !== true) {
                    return $result;
                }
            }

            return redirect()->back()->with('success', 'Bahan Ajar berhasil diperbarui.');
        });
    }

    public function requestApproval(Request $request, Materi $materi)
    {
        $guru = $this->currentGuru($request);
        if ($request->user()->role !== 'guru' || $materi->guru_id !== $guru->id) {
            abort(403);
        }

        return DB::transaction(function () use ($request, $materi) {
            $result = $this->submitForApproval($materi, $request, false);
            if ($result !== true) {
                return $result;
            }

            return redirect()->back()->with('success', 'Bahan Ajar dikirim untuk approval Kepala Sekolah.');
        });
    }

    public function cancelApproval(Request $request, Materi $materi)
    {
        $guru = $this->currentGuru($request);
        if ($request->user()->role !== 'guru' || $materi->guru_id !== $guru->id) {
            abort(403);
        }

        if ($materi->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan pending yang dapat dibatalkan.');
        }

        $from = $materi->status;
        $materi->update([
            'status' => 'draft',
            'submitted_at' => null,
        ]);
        $this->recordHistory($materi, $request, 'cancelled', $from, 'draft');

        return redirect()->back()->with('success', 'Pengajuan Bahan Ajar dibatalkan.');
    }

    public function destroy(Request $request, Materi $materi)
    {
        $guru = $this->currentGuru($request);
        if ($request->user()->role !== 'guru' || $materi->guru_id !== $guru->id) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        if ($materi->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya draft yang dapat dihapus.');
        }

        $materi->delete();

        return redirect()->back()->with('success', 'Bahan Ajar draft berhasil dihapus.');
    }

    public function approve(Request $request, Materi $materi)
    {
        return $this->review($request, $materi, 'approved');
    }

    public function reject(Request $request, Materi $materi)
    {
        return $this->review($request, $materi, 'rejected');
    }

    public function requestRevision(Request $request, Materi $materi)
    {
        return $this->review($request, $materi, 'revision_requested');
    }

    public function download(Request $request, Materi $materi)
    {
        if (!$this->canDownloadMateri($request, $materi)) {
            abort(403);
        }

        $path = storage_path('app/public/'.$materi->file_path);

        if (!$materi->file_path || !file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($path);
    }

    private function validatedMateri(Request $request, bool $create): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:'.implode(',', array_keys(SmpLearningDocumentInventory::categoryLabels())),
            'kode' => 'nullable|string|max:80',
            'deskripsi' => 'nullable|string|max:4000',
            'isi' => 'nullable|string',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'tahun_ajaran' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:20',
            'submit_action' => 'nullable|in:draft,request_approval',
            'file' => [$create ? 'nullable' : 'nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png,mp4,mp3', 'max:51200'],
        ]);
    }

    private function submitForApproval(Materi $materi, Request $request, bool $resubmit): mixed
    {
        if (!in_array($materi->status, ['draft', 'rejected', 'revision_requested'], true)) {
            return redirect()->back()->with('error', 'Status Bahan Ajar tidak dapat diajukan.');
        }

        if (!$materi->checklist()['is_ready']) {
            return redirect()->back()->with('error', 'Lengkapi kategori, judul, deskripsi, file, mapel, dan kelas sebelum meminta approval.');
        }

        $from = $materi->status;
        $materi->update([
            'status' => 'pending',
            'submitted_at' => now(),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'catatan_reviewer' => null,
            'versi' => $resubmit ? ((int) ($materi->versi ?? 1)) + 1 : (int) ($materi->versi ?? 1),
        ]);
        $this->recordHistory($materi, $request, $resubmit ? 'resubmitted' : 'submitted', $from, 'pending');

        return true;
    }

    private function review(Request $request, Materi $materi, string $status)
    {
        if (!in_array($request->user()->role, ['kepala_sekolah', 'admin'], true)) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        if ($materi->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan pending yang dapat direview.');
        }

        $data = $request->validate([
            'catatan_reviewer' => in_array($status, ['rejected', 'revision_requested'], true)
                ? 'required|string|max:2000'
                : 'nullable|string|max:2000',
            'skor_kauniyah' => 'nullable|integer|min:1|max:5',
            'skor_bilingual' => 'nullable|integer|min:1|max:5',
            'skor_ai' => 'nullable|integer|min:1|max:5',
        ]);

        $from = $materi->status;
        $materi->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
            'catatan_reviewer' => $data['catatan_reviewer'] ?? null,
            'skor_kauniyah' => $data['skor_kauniyah'] ?? $materi->skor_kauniyah,
            'skor_bilingual' => $data['skor_bilingual'] ?? $materi->skor_bilingual,
            'skor_ai' => $data['skor_ai'] ?? $materi->skor_ai,
        ]);

        $this->recordHistory($materi, $request, $status, $from, $status, $data['catatan_reviewer'] ?? null);

        $labels = [
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            'revision_requested' => 'diminta revisi',
        ];

        return redirect()->back()->with('success', 'Bahan Ajar berhasil '.$labels[$status].'.');
    }

    private function recordHistory(Materi $materi, Request $request, string $action, ?string $from, string $to, ?string $catatan = null): void
    {
        MateriApprovalHistory::create([
            'materi_id' => $materi->id,
            'actor_id' => $request->user()?->id,
            'action' => $action,
            'status_from' => $from,
            'status_to' => $to,
            'catatan' => $catatan,
        ]);
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
}
