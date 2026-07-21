<?php

namespace App\Http\Controllers;

use App\Models\CbtExam;
use App\Models\CbtSoal;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;

class GuruCbtController extends Controller
{
    public function index(Request $request)
    {
        $guru = $request->user()->guru;
        $exams = CbtExam::where('guru_id', $guru->id)->with('mapel', 'kelas')->latest()->get();
        return view('dashboard.guru-sections.cbt-index', compact('exams', 'guru'));
    }

    public function create(Request $request)
    {
        $guru = $request->user()->guru;
        $kelasIds = Jadwal::where('guru_id', $guru->id)->pluck('kelas_id')->unique();
        $mapelIds = Jadwal::where('guru_id', $guru->id)->pluck('mapel_id')
            ->merge($guru->mapels->pluck('id'))
            ->unique();
        $mapels = Mapel::whereIn('id', $mapelIds)->get();
        $kelasList = Kelas::whereIn('id', $kelasIds)->get();
        return view('dashboard.guru-sections.cbt-form', compact('mapels', 'kelasList', 'guru'));
    }

    public function store(Request $request)
    {
        $guru = $request->user()->guru;
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:ulangan,uts,uas',
            'deskripsi' => 'nullable|string',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'durasi' => 'required|integer|min:1|max:300',
            'metode' => 'required|in:online,cetak',
            'generate_otomatis' => 'nullable|string',
            'jumlah_soal_gen' => 'required_if:generate_otomatis,on|nullable|integer|min:1|max:100',
            'persen_mudah' => 'required_if:generate_otomatis,on|nullable|integer|min:0|max:100',
            'persen_sedang' => 'required_if:generate_otomatis,on|nullable|integer|min:0|max:100',
            'persen_sulit' => 'required_if:generate_otomatis,on|nullable|integer|min:0|max:100',
        ]);

        $data['guru_id'] = $guru->id;
        $data['status'] = 'draft';
        $data['jumlah_soal'] = 0;
        $canUseMapel = $data['kelas_id'] ?? null
            ? $this->guruTeaches($guru, $data['kelas_id'], $data['mapel_id'])
            : $this->guruTeaches($guru, null, $data['mapel_id']);

        if (!$canUseMapel) {
            return redirect()->back()->withInput()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }

        // If generating automatically, check if there are any questions in the pool first
        if ($request->has('generate_otomatis')) {
            $poolCount = CbtSoal::whereHas('exam', function($q) use ($data) {
                $q->where('mapel_id', $data['mapel_id']);
            })->count();

            if ($poolCount === 0) {
                return redirect()->back()->withInput()->with('error', 'Belum ada bank soal untuk mata pelajaran ini. Silakan buat minimal satu soal secara manual terlebih dahulu.');
            }

            // Validate total percentage
            $totalPersen = (int)$data['persen_mudah'] + (int)$data['persen_sedang'] + (int)$data['persen_sulit'];
            if ($totalPersen !== 100) {
                return redirect()->back()->withInput()->with('error', 'Total persentase kesulitan harus berjumlah 100% (saat ini: ' . $totalPersen . '%).');
            }
        }

        $exam = CbtExam::create([
            'judul' => $data['judul'],
            'tipe' => $data['tipe'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'mapel_id' => $data['mapel_id'],
            'kelas_id' => $data['kelas_id'] ?? null,
            'durasi' => $data['durasi'],
            'guru_id' => $data['guru_id'],
            'status' => $data['status'],
            'jumlah_soal' => $data['jumlah_soal'],
            'metode' => $data['metode'],
        ]);

        if ($request->has('generate_otomatis')) {
            $jumlahSoalGen = (int)$data['jumlah_soal_gen'];
            $countMudah = (int)round($jumlahSoalGen * ((int)$data['persen_mudah'] / 100));
            $countSedang = (int)round($jumlahSoalGen * ((int)$data['persen_sedang'] / 100));
            $countSulit = $jumlahSoalGen - ($countMudah + $countSedang);

            // Fetch based on difficulty
            $poolMudah = CbtSoal::whereHas('exam', function($q) use ($data) {
                $q->where('mapel_id', $data['mapel_id']);
            })->where('kesulitan', 'mudah')->inRandomOrder()->limit($countMudah)->get();

            $poolSedang = CbtSoal::whereHas('exam', function($q) use ($data) {
                $q->where('mapel_id', $data['mapel_id']);
            })->where('kesulitan', 'sedang')->inRandomOrder()->limit($countSedang)->get();

            $poolSulit = CbtSoal::whereHas('exam', function($q) use ($data) {
                $q->where('mapel_id', $data['mapel_id']);
            })->where('kesulitan', 'sulit')->inRandomOrder()->limit($countSulit)->get();

            $selectedSoals = collect();
            $selectedSoals = $selectedSoals->concat($poolMudah)->concat($poolSedang)->concat($poolSulit);

            // Fallback: If not enough questions of desired difficulties, pull any remaining questions for this mapel
            $needed = $jumlahSoalGen - $selectedSoals->count();
            if ($needed > 0) {
                $excludeIds = $selectedSoals->pluck('id')->toArray();
                $additional = CbtSoal::whereHas('exam', function($q) use ($data) {
                    $q->where('mapel_id', $data['mapel_id']);
                })->whereNotIn('id', $excludeIds)->inRandomOrder()->limit($needed)->get();
                $selectedSoals = $selectedSoals->concat($additional);
            }

            // Clone selected questions to the new exam
            $shuffledSoals = $selectedSoals->shuffle();
            foreach ($shuffledSoals as $idx => $s) {
                CbtSoal::create([
                    'cbt_exam_id' => $exam->id,
                    'nomor' => $idx + 1,
                    'soal' => $s->soal,
                    'tipe' => $s->tipe,
                    'pilihan_a' => $s->pilihan_a,
                    'pilihan_b' => $s->pilihan_b,
                    'pilihan_c' => $s->pilihan_c,
                    'pilihan_d' => $s->pilihan_d,
                    'jawaban_benar' => $s->jawaban_benar,
                    'bobot' => $s->bobot,
                    'kesulitan' => $s->kesulitan,
                ]);
            }

            $exam->update(['jumlah_soal' => $shuffledSoals->count()]);

            return redirect()->route('dashboard')->with('active_tab', 'cbt')->with('success', 'Ujian berhasil digenerate otomatis dengan ' . $shuffledSoals->count() . ' soal!');
        }

        return redirect()->route('guru.cbt.add-soal', $exam->id)->with('success', 'Ujian dibuat, silakan tambah soal');
    }

    public function addSoal(Request $request, CbtExam $cbtExam)
    {
        $guru = $request->user()->guru;
        if ($cbtExam->guru_id !== $guru->id) {
            abort(403);
        }

        $soals = $cbtExam->soals()->orderBy('nomor')->get();
        return view('dashboard.guru-sections.cbt-add-soal', compact('cbtExam', 'soals', 'guru'));
    }

    public function storeSoal(Request $request, CbtExam $cbtExam)
    {
        $guru = $request->user()->guru;
        if ($cbtExam->guru_id !== $guru->id) {
            abort(403);
        }

        $data = $request->validate([
            'soal' => 'required|string',
            'tipe' => 'required|in:pg,essay',
            'pilihan_a' => 'required_if:tipe,pg|nullable|string',
            'pilihan_b' => 'required_if:tipe,pg|nullable|string',
            'pilihan_c' => 'required_if:tipe,pg|nullable|string',
            'pilihan_d' => 'required_if:tipe,pg|nullable|string',
            'jawaban_benar' => 'required_if:tipe,pg|nullable|string|in:a,b,c,d',
            'kesulitan' => 'required|in:mudah,sedang,sulit',
        ]);

        $nomorTerakhir = $cbtExam->soals()->max('nomor') ?? 0;

        CbtSoal::create([
            'cbt_exam_id' => $cbtExam->id,
            'nomor' => $nomorTerakhir + 1,
            'soal' => $data['soal'],
            'tipe' => $data['tipe'],
            'pilihan_a' => $data['pilihan_a'] ?? null,
            'pilihan_b' => $data['pilihan_b'] ?? null,
            'pilihan_c' => $data['pilihan_c'] ?? null,
            'pilihan_d' => $data['pilihan_d'] ?? null,
            'jawaban_benar' => $data['jawaban_benar'] ?? null,
            'kesulitan' => $data['kesulitan'],
        ]);

        $cbtExam->increment('jumlah_soal');

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function deleteSoal(CbtExam $cbtExam, CbtSoal $cbtSoal)
    {
        $guru = request()->user()->guru;
        if ($cbtExam->guru_id !== $guru->id || $cbtSoal->cbt_exam_id !== $cbtExam->id) {
            abort(403);
        }

        $cbtSoal->delete();
        $cbtExam->decrement('jumlah_soal');
        return redirect()->back()->with('success', 'Soal berhasil dihapus');
    }

    public function ajukan(Request $request, CbtExam $cbtExam)
    {
        if ($cbtExam->guru_id !== $request->user()->guru->id) {
            abort(403);
        }

        if ($cbtExam->jumlah_soal < 1) {
            return redirect()->back()->with('error', 'Minimal 1 soal untuk mengajukan');
        }
        $cbtExam->update(['status' => 'pending']);
        return redirect()->back()->with('active_tab', 'cbt')->with('success', 'Ujian diajukan ke admin untuk approval');
    }

    public function printExam(Request $request, CbtExam $cbtExam)
    {
        if ($cbtExam->guru_id !== $request->user()->guru->id) {
            abort(403);
        }

        $soals = $cbtExam->soals()->orderBy('nomor')->get();
        return view('dashboard.guru-sections.cbt-print', compact('cbtExam', 'soals'));
    }
}
