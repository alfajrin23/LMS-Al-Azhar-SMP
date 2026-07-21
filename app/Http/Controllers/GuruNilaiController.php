<?php
namespace App\Http\Controllers;
use App\Models\Guru;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Remedial;
use Illuminate\Http\Request;
class GuruNilaiController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input nilai');
        }
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mapel_id' => 'required|exists:mapel,id',
            'nilai' => 'required|numeric|min:0|max:100',
            'jenis_nilai' => 'required|in:biasa,unggulan',
            'nilai_bahasa' => 'nullable|numeric|min:0|max:100',
        ]);
        $siswa = Siswa::with('kelas')->findOrFail($data['siswa_id']);
        $guru = $this->currentGuru($request);
        if (!$this->guruCanAccessSiswa($guru, $siswa, $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Siswa atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }
        $kkm = str_contains($siswa->kelas->nama_kelas ?? '', 'SD') ? setting('kkm_sd', 70) : setting('kkm_smp', 75);
        $pendingRemedial = Remedial::where('siswa_id', $siswa->id)
            ->where('mapel_id', $data['mapel_id'])
            ->where('status', 'pending')
            ->first();
        $inputNilai = $data['nilai'];
        $finalNilai = $inputNilai;
        if ($pendingRemedial) {
            $finalNilai = min($inputNilai, $kkm);
        }
        $nilaiRecord = Nilai::updateOrCreate(
            [
                'siswa_id' => $data['siswa_id'],
                'mapel_id' => $data['mapel_id'],
                'jenis_nilai' => $data['jenis_nilai']
            ],
            [
                'nilai' => $finalNilai,
                'nilai_bahasa' => $data['nilai_bahasa'] ?? null,
            ]
        );
        if ($pendingRemedial) {
            if ($finalNilai >= $kkm) {
                $pendingRemedial->update(['status' => 'selesai']);
            }
        } else {
            if ($finalNilai < $kkm) {
                Remedial::create([
                    'siswa_id' => $siswa->id,
                    'mapel_id' => $data['mapel_id'],
                    'nilai_id' => $nilaiRecord->id,
                    'nilai_asal' => $finalNilai,
                    'deadline' => now()->addDays(3)->format('Y-m-d'),
                    'status' => 'pending'
                ]);
            }
        }
        return redirect()->back()->with('success', 'Nilai berhasil disimpan!');
    }
}
