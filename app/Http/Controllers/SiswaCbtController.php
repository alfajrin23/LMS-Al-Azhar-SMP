<?php

namespace App\Http\Controllers;

use App\Models\CbtExam;
use App\Models\CbtJawaban;
use Illuminate\Http\Request;

class SiswaCbtController extends Controller
{
    public function index(Request $request)
    {
        $siswa = $request->user()->siswa;
        $exams = CbtExam::where('status', 'approved')
            ->where('metode', 'online')
            ->where(function($q) use ($siswa) {
                $q->where('kelas_id', $siswa->kelas_id)
                  ->orWhereNull('kelas_id');
            })
            ->with('mapel')
            ->latest()
            ->get();

        foreach ($exams as $exam) {
            $dijawab = CbtJawaban::whereIn('cbt_soal_id', $exam->soals->pluck('id'))
                ->where('siswa_id', $siswa->id)
                ->exists();
            $exam->sudah_dikerjakan = $dijawab;
        }

        return view('siswa-cbt-index', compact('exams', 'siswa'));
    }

    public function kerjakan(Request $request, CbtExam $cbtExam)
    {
        if ($cbtExam->status !== 'approved') {
            return redirect()->back()->with('error', 'Ujian belum disetujui');
        }

        $siswa = $request->user()->siswa;
        if (!$this->canAccessExam($siswa, $cbtExam)) {
            return redirect()->route('siswa.cbt.index')->with('error', 'Ujian tidak tersedia untuk kelas Anda.');
        }

        $soals = $cbtExam->soals()->orderBy('nomor')->get();
        $jawaban = CbtJawaban::whereIn('cbt_soal_id', $soals->pluck('id'))
            ->where('siswa_id', $siswa->id)
            ->get()->keyBy('cbt_soal_id');

        if ($jawaban->isNotEmpty()) {
            return redirect()->route('siswa.cbt.hasil', $cbtExam->id)
                ->with('info', 'Kamu sudah mengerjakan ujian ini');
        }

        return view('siswa-cbt-kerjakan', compact('cbtExam', 'soals', 'siswa'));
    }

    public function submit(Request $request, CbtExam $cbtExam)
    {
        $siswa = $request->user()->siswa;
        if ($cbtExam->status !== 'approved' || !$this->canAccessExam($siswa, $cbtExam)) {
            return redirect()->route('siswa.cbt.index')->with('error', 'Ujian tidak tersedia untuk kelas Anda.');
        }

        $soals = $cbtExam->soals()->get();
        if (CbtJawaban::whereIn('cbt_soal_id', $soals->pluck('id'))->where('siswa_id', $siswa->id)->exists()) {
            return redirect()->route('siswa.cbt.hasil', $cbtExam->id)
                ->with('info', 'Kamu sudah mengerjakan ujian ini');
        }

        foreach ($soals as $soal) {
            $jawaban = $request->input('soal_' . $soal->id);

            if ($soal->tipe === 'pg') {
                $benar = $jawaban === $soal->jawaban_benar;
                $nilai = $benar ? 100 : 0;

                CbtJawaban::create([
                    'cbt_exam_id' => $cbtExam->id,
                    'cbt_soal_id' => $soal->id,
                    'siswa_id' => $siswa->id,
                    'jawaban' => $jawaban ?? '',
                    'nilai' => $nilai,
                ]);
            } else {
                CbtJawaban::create([
                    'cbt_exam_id' => $cbtExam->id,
                    'cbt_soal_id' => $soal->id,
                    'siswa_id' => $siswa->id,
                    'jawaban' => $jawaban ?? '',
                    'nilai' => null,
                ]);
            }
        }

        return redirect()->route('siswa.cbt.hasil', $cbtExam->id)
            ->with('success', 'Ujian selesai!');
    }

    public function hasil(Request $request, CbtExam $cbtExam)
    {
        $siswa = $request->user()->siswa;
        if (!$this->canAccessExam($siswa, $cbtExam)) {
            return redirect()->route('siswa.cbt.index')->with('error', 'Hasil ujian tidak tersedia untuk kelas Anda.');
        }

        $soals = $cbtExam->soals()->get();
        $jawaban = CbtJawaban::whereIn('cbt_soal_id', $soals->pluck('id'))
            ->where('siswa_id', $siswa->id)
            ->get()->keyBy('cbt_soal_id');

        $totalPG = $soals->where('tipe', 'pg')->count();
        $correctPG = $jawaban->where('nilai', 100)->count();
        $essayMenunggu = $soals->where('tipe', 'essay')->count();

        return view('siswa-cbt-hasil', compact('cbtExam', 'soals', 'jawaban', 'siswa', 'totalPG', 'correctPG', 'essayMenunggu'));
    }

    private function canAccessExam($siswa, CbtExam $cbtExam): bool
    {
        if (!$siswa) {
            return false;
        }

        return $cbtExam->kelas_id === null || $cbtExam->kelas_id === $siswa->kelas_id;
    }
}
