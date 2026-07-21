<?php

namespace App\Http\Controllers;

use App\Models\Workbook;
use App\Models\WorkbookJawaban;
use App\Models\WorkbookSoal;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaWorkbookController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        $workbooks = Workbook::where('kelas_id', $siswa->kelas_id)
            ->with('mapel', 'soals')
            ->withCount('soals')
            ->get();

        foreach ($workbooks as $wb) {
            $dijawab = WorkbookJawaban::whereIn('workbook_soal_id', $wb->soals->pluck('id'))
                ->where('siswa_id', $siswa->id)
                ->count();
            $wb->dijawab = $dijawab;
        }

        return view('siswa-workbook-index', compact('workbooks', 'siswa'));
    }

    public function kerjakan(Request $request, Workbook $workbook)
    {
        $siswa = $request->user()->siswa;
        if ($siswa->kelas_id !== $workbook->kelas_id) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $soals = $workbook->soals()->get();
        $jawaban = WorkbookJawaban::whereIn('workbook_soal_id', $soals->pluck('id'))
            ->where('siswa_id', $siswa->id)
            ->get()->keyBy('workbook_soal_id');

        return view('siswa-workbook-kerjakan', compact('workbook', 'soals', 'jawaban', 'siswa'));
    }

    public function submit(Request $request, Workbook $workbook)
    {
        $siswa = $request->user()->siswa;
        if (!$siswa || $siswa->kelas_id !== $workbook->kelas_id) {
            return redirect()->route('siswa.workbook.index')->with('error', 'Akses ditolak');
        }

        $soals = $workbook->soals()->get();

        foreach ($soals as $soal) {
            $jawaban = $request->input('soal_' . $soal->id);

            if ($soal->tipe === 'pg') {
                $benar = $jawaban === $soal->jawaban_benar;
                $nilai = $benar ? 100 : 0;

                WorkbookJawaban::updateOrCreate(
                    ['workbook_soal_id' => $soal->id, 'siswa_id' => $siswa->id],
                    ['jawaban' => $jawaban ?? '', 'nilai' => $nilai]
                );
            } else {
                WorkbookJawaban::updateOrCreate(
                    ['workbook_soal_id' => $soal->id, 'siswa_id' => $siswa->id],
                    ['jawaban' => $jawaban ?? '', 'nilai' => null]
                );
            }
        }

        return redirect()->route('siswa.workbook.hasil', $workbook->id)
            ->with('success', 'Workbook selesai dikerjakan!');
    }

    public function hasil(Request $request, Workbook $workbook)
    {
        $siswa = $request->user()->siswa;
        if (!$siswa || $siswa->kelas_id !== $workbook->kelas_id) {
            return redirect()->route('siswa.workbook.index')->with('error', 'Akses ditolak');
        }

        $soals = $workbook->soals()->get();
        $jawaban = WorkbookJawaban::whereIn('workbook_soal_id', $soals->pluck('id'))
            ->where('siswa_id', $siswa->id)
            ->get()->keyBy('workbook_soal_id');

        $totalPG = $soals->where('tipe', 'pg')->count();
        $correctPG = $jawaban->where('nilai', 100)->count();

        return view('siswa-workbook-hasil', compact('workbook', 'soals', 'jawaban', 'siswa', 'totalPG', 'correctPG'));
    }
}
