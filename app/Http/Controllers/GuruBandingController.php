<?php
namespace App\Http\Controllers;
use App\Models\Guru;
use App\Models\BandingNilai;
use App\Models\Nilai;
use Illuminate\Http\Request;
class GuruBandingController extends Controller
{
    public function proses(Request $request, BandingNilai $banding)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa memproses banding nilai');
        }
        $data = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_guru' => 'nullable|string|max:1000',
            'nilai' => 'nullable|numeric|min:0|max:100',
            'nilai_bahasa' => 'nullable|numeric|min:0|max:100',
        ]);
        $guru = $this->currentGuru($request);
        $nilai = $banding->nilai()->with('siswa')->firstOrFail();
        if (!$nilai->siswa || !$this->guruCanAccessSiswa($guru, $nilai->siswa, $nilai->mapel_id)) {
            return redirect()->back()->with('error', 'Anda hanya bisa memproses banding untuk kelas dan mapel Anda sendiri.');
        }
        $banding->status = $data['status'];
        $banding->catatan_guru = $data['catatan_guru'] ?? null;
        $banding->save();
        if ($data['status'] === 'disetujui') {
            $updateData = [];
            if (isset($data['nilai'])) {
                $updateData['nilai'] = $data['nilai'];
            }
            if (isset($data['nilai_bahasa'])) {
                $updateData['nilai_bahasa'] = $data['nilai_bahasa'];
            }
            if (!empty($updateData)) {
                $nilai->update($updateData);
            }
        }
        return redirect()->back()->with('success', 'Banding nilai berhasil diproses.');
    }
}
