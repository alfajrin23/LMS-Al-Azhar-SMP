<?php
namespace App\Http\Controllers;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\TahfidzSetoran;
use App\Models\TahfidzAyatNilai;
use Illuminate\Http\Request;
class GuruTahfidzController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input setoran tahfidz');
        }
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'surah' => 'required|string|max:255',
            'ayat_mulai' => 'required|integer|min:1',
            'ayat_selesai' => 'required|integer|min:1|gte:ayat_mulai',
            'jumlah_ayat' => 'required|integer|min:1',
            'status' => 'required|in:baru,murojaah',
            'nilai' => 'nullable|integer|min:0|max:100',
            'tanggal' => 'required|date',
            'tanggal_berikutnya' => 'nullable|date',
            'catatan_guru' => 'nullable|string|max:500',
            'ayat_nilai' => 'nullable|array',
            'ayat_nilai.*.nomor_ayat' => 'required_with:ayat_nilai|integer|min:1',
            'ayat_nilai.*.makhroj' => 'required_with:ayat_nilai|integer|min:1|max:4',
            'ayat_nilai.*.tajwid' => 'required_with:ayat_nilai|integer|min:1|max:4',
            'ayat_nilai.*.kelancaran' => 'required_with:ayat_nilai|integer|min:1|max:4',
        ]);
        $guru = $this->currentGuru($request);
        $siswa = Siswa::findOrFail($data['siswa_id']);
        if ($siswa->kelas_id && !$this->guruCanAccessSiswa($guru, $siswa)) {
            return redirect()->back()->with('error', 'Siswa tidak berada pada kelas yang Anda ampu.');
        }
        $setoran = TahfidzSetoran::create([
            'siswa_id' => $data['siswa_id'],
            'guru_id' => $guru->id,
            'surah' => $data['surah'],
            'ayat_mulai' => $data['ayat_mulai'],
            'ayat_selesai' => $data['ayat_selesai'],
            'jumlah_ayat' => $data['jumlah_ayat'],
            'status' => $data['status'],
            'nilai' => $data['nilai'] ?? null,
            'tanggal' => $data['tanggal'],
            'tanggal_berikutnya' => $data['tanggal_berikutnya'] ?? null,
            'catatan_guru' => $data['catatan_guru'] ?? null,
        ]);
        if (!empty($data['ayat_nilai'])) {
            foreach ($data['ayat_nilai'] as $an) {
                TahfidzAyatNilai::create([
                    'tahfidz_setoran_id' => $setoran->id,
                    'guru_id' => $guru->id,
                    'nomor_ayat' => $an['nomor_ayat'],
                    'makhroj' => $an['makhroj'],
                    'tajwid' => $an['tajwid'],
                    'kelancaran' => $an['kelancaran'],
                ]);
            }
            $this->calculateSetoranNilai($setoran);
        }
        return redirect()->back()->with('success', 'Setoran tahfidz berhasil disimpan!');
    }
    public function storePembanding(Request $request, TahfidzSetoran $tahfidzSetoran)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input nilai pembanding');
        }
        $guru = $this->currentGuru($request);
        $siswa = $tahfidzSetoran->siswa;
        if ($siswa?->kelas_id && !$this->guruCanAccessSiswa($guru, $siswa)) {
            return redirect()->back()->with('error', 'Setoran tidak terkait dengan kelas yang Anda ampu.');
        }
        if ($tahfidzSetoran->guru_id === $guru->id) {
            return redirect()->back()->with('error', 'Guru yang sama tidak bisa mengisi nilai pembanding');
        }
        $data = $request->validate([
            'ayat_nilai' => 'required|array',
            'ayat_nilai.*.nomor_ayat' => 'required|integer|min:1',
            'ayat_nilai.*.makhroj' => 'required|integer|min:1|max:4',
            'ayat_nilai.*.tajwid' => 'required|integer|min:1|max:4',
            'ayat_nilai.*.kelancaran' => 'required|integer|min:1|max:4',
        ]);
        foreach ($data['ayat_nilai'] as $an) {
            TahfidzAyatNilai::updateOrCreate(
                [
                    'tahfidz_setoran_id' => $tahfidzSetoran->id,
                    'guru_id' => $guru->id,
                    'nomor_ayat' => $an['nomor_ayat'],
                ],
                [
                    'makhroj' => $an['makhroj'],
                    'tajwid' => $an['tajwid'],
                    'kelancaran' => $an['kelancaran'],
                ]
            );
        }
        $this->calculateSetoranNilai($tahfidzSetoran);
        return redirect()->back()->with('success', 'Nilai pembanding tahfidz berhasil disimpan!');
    }
    private function calculateSetoranNilai(TahfidzSetoran $setoran)
    {
        $ratings = TahfidzAyatNilai::query()->where('tahfidz_setoran_id', $setoran->id)->get();
        if ($ratings->isEmpty()) {
            return;
        }
        $grouped = $ratings->groupBy('nomor_ayat');
        $totalAyatScores = 0;
        $countAyat = 0;
        for ($i = $setoran->ayat_mulai; $i <= $setoran->ayat_selesai; $i++) {
            $ayatRatings = $grouped->get($i);
            if ($ayatRatings && $ayatRatings->count() > 0) {
                $makhrojAvg = $ayatRatings->avg('makhroj');
                $tajwidAvg = $ayatRatings->avg('tajwid');
                $kelancaranAvg = $ayatRatings->avg('kelancaran');
                $ayatScore = (($makhrojAvg + $tajwidAvg + $kelancaranAvg) / 12) * 100;
                $totalAyatScores += $ayatScore;
                $countAyat++;
            }
        }
        if ($countAyat > 0) {
            $finalNilai = round($totalAyatScores / $countAyat);
            $setoran->update(['nilai' => $finalNilai]);
        }
    }
}
