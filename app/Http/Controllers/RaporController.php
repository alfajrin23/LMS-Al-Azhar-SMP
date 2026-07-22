<?php

namespace App\Http\Controllers;

use App\Models\CatatanWali;
use App\Models\Kehadiran;
use App\Models\Nilai;
use App\Models\Rapor;
use App\Models\RaporItem;
use App\Models\Siswa;
use App\Models\TahfidzProgress;
use App\Models\TahfidzSetoran;
use App\Models\TahsinSetoran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RaporController extends Controller
{
    public function show(Request $request, string $jenis)
    {
        $payload = $this->payload($request, $this->normalizeJenis($jenis));

        return view('rapor-show', $payload);
    }

    public function pdf(Request $request, ?string $jenis = null)
    {
        $jenis = $this->normalizeJenis($jenis ?? $request->query('jenis', $request->query('type', 'akademik')));
        $payload = $this->payload($request, $jenis);

        $pdf = Pdf::loadView('rapor-pdf', $payload)->setPaper('a4');

        return $pdf->download('rapor-'.$jenis.'-'.$payload['siswa']->nama.'.pdf');
    }

    private function payload(Request $request, string $jenis): array
    {
        $siswa = $this->resolveSiswa($request);
        $siswa->loadMissing(['kelas', 'kelasQuran']);

        $tahunAjaran = $request->query('tahun_ajaran', $jenis === 'akademik' ? '2025/2026' : '2026/2027');
        $semester = $request->query('semester', $jenis === 'akademik' ? 'Genap' : 'Ganjil');

        $rapor = Rapor::query()
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('jenis_rapor', $jenis)
            ->with(['items.mapel'])
            ->first();

        $items = $rapor?->items ?? $this->fallbackItems($siswa, $jenis, $tahunAjaran, $semester);
        $kehadiran = Kehadiran::query()
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->get();

        if ($kehadiran->isEmpty()) {
            $kehadiran = Kehadiran::query()->where('siswa_id', $siswa->id)->get();
        }

        $attendanceSummary = [
            'total_hari_efektif' => $kehadiran->count(),
            'hadir' => $kehadiran->where('status', 'hadir')->count(),
            'sakit' => $kehadiran->where('status', 'sakit')->count(),
            'izin' => $kehadiran->where('status', 'izin')->count(),
            'alpha' => $kehadiran->where('status', 'alpha')->count(),
            'tidak_hadir' => $kehadiran->whereIn('status', ['sakit', 'izin', 'alpha'])->count(),
        ];
        $attendanceSummary['persentase'] = $attendanceSummary['total_hari_efektif'] > 0
            ? round(($attendanceSummary['hadir'] / $attendanceSummary['total_hari_efektif']) * 100, 2)
            : 0;

        $catatanWali = CatatanWali::query()->where('siswa_id', $siswa->id)->latest()->with('guru')->first();
        $tahfidzProgress = TahfidzProgress::query()->where('siswa_id', $siswa->id)->first();
        $tahfidzSetoran = TahfidzSetoran::query()->where('siswa_id', $siswa->id)
            ->with('guru')
            ->latest('tanggal')
            ->get();
        $tahsinSetoran = TahsinSetoran::query()->where('siswa_id', $siswa->id)
            ->with('guru')
            ->latest('tanggal')
            ->get();

        return [
            'jenis' => $jenis,
            'rapor' => $rapor,
            'siswa' => $siswa,
            'kelas' => $siswa->kelas,
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester,
            'items' => $items,
            'academicItems' => $items->where('kategori', 'akademik')->values(),
            'englishItems' => $items->where('kategori', 'english')->values(),
            'quranReadingItems' => $items->where('kategori', 'quran_reading')->values(),
            'quranSurahItems' => $items->where('kategori', 'quran_surah')->values(),
            'extraItems' => $items->where('kategori', 'ekstrakurikuler')->values(),
            'attendanceSummary' => $attendanceSummary,
            'kehadiran' => $kehadiran,
            'catatanWali' => $catatanWali,
            'tahfidzProgress' => $tahfidzProgress,
            'tahfidzSetoran' => $tahfidzSetoran,
            'tahsinSetoran' => $tahsinSetoran,
            'signatures' => $rapor?->signature_metadata ?? [
                'tempat_tanggal' => 'Bekasi, '.now()->translatedFormat('d F Y'),
                'wali_kelas' => $siswa->kelas?->waliKelas?->nama,
                'kepala_sekolah' => 'Kepala Sekolah',
                'koordinator_quran' => 'Koordinator Quran',
                'guru_tahfidz' => $tahfidzSetoran->first()?->guru?->nama,
            ],
        ];
    }

    private function resolveSiswa(Request $request): Siswa
    {
        $user = $request->user();

        if (in_array($user->role, ['siswa_sd', 'siswa_smp'], true)) {
            return $user->siswa()->firstOrFail();
        }

        if ($user->role === 'orang_tua') {
            $ortu = $this->currentOrangTua($request);
            $siswa = Siswa::findOrFail($request->query('siswa_id'));
            if (!$this->orangTuaHasSiswa($ortu, $siswa->id)) {
                abort(403);
            }

            return $siswa;
        }

        if (in_array($user->role, ['guru', 'kepala_sekolah', 'admin'], true)) {
            return Siswa::findOrFail($request->query('siswa_id'));
        }

        abort(403);
    }

    private function fallbackItems(Siswa $siswa, string $jenis, string $tahunAjaran, string $semester): Collection
    {
        if ($jenis === 'akademik') {
            return Nilai::query()
                ->where('siswa_id', $siswa->id)
                ->where('jenis_rapor', 'akademik')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->with('mapel')
                ->get()
                ->map(fn (Nilai $nilai) => new RaporItem([
                    'mapel_id' => $nilai->mapel_id,
                    'kategori' => 'akademik',
                    'komponen' => $nilai->mapel?->nama_mapel ?? 'Mata Pelajaran',
                    'nilai' => $nilai->nilai,
                    'predikat' => $this->predicate((float) $nilai->nilai),
                    'deskripsi' => $nilai->capaian_kompetensi ?: $nilai->catatan,
                    'metadata' => [
                        'tujuan_pembelajaran' => $nilai->tujuan_pembelajaran,
                        'lingkup_materi' => $nilai->lingkup_materi,
                        'tp_scores' => $nilai->tp_scores,
                        'sumatif_scores' => $nilai->sumatif_scores,
                    ],
                ]));
        }

        if ($jenis === 'english') {
            return Nilai::query()
                ->where('siswa_id', $siswa->id)
                ->where('jenis_rapor', 'english')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->with('mapel')
                ->get()
                ->map(fn (Nilai $nilai) => new RaporItem([
                    'kategori' => 'english',
                    'komponen' => $nilai->lingkup_materi ?: ($nilai->mapel?->nama_mapel ?? 'English'),
                    'nilai' => $nilai->nilai,
                    'predikat' => $this->predicate((float) $nilai->nilai),
                    'deskripsi' => $nilai->catatan,
                ]));
        }

        return collect();
    }

    private function normalizeJenis(string $jenis): string
    {
        return match ($jenis) {
            'biasa', 'sekolah', 'academic' => 'akademik',
            'unggulan', 'english' => 'english',
            'quran', 'tahfidz' => 'quran',
            default => in_array($jenis, ['akademik', 'english', 'quran'], true) ? $jenis : 'akademik',
        };
    }

    private function predicate(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            default => 'D',
        };
    }
}
