<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\CatatanWali;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\LaporanMengajar;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pengumuman;
use App\Models\Pesan;
use App\Models\Siswa;
use App\Models\TahfidzProgress;
use App\Models\TahfidzSetoran;
use App\Models\TahsinSetoran;
use App\Models\Tugas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->user()->role;

        $views = [
            'siswa_sd' => 'dashboard.siswa-sd',
            'siswa_smp' => 'dashboard.siswa-smp',
            'guru' => 'dashboard.guru',
            'orang_tua' => 'dashboard.orang-tua',
            'admin' => 'dashboard.admin',
            'kepala_sekolah' => 'dashboard.kepala-sekolah',
        ];

        $view = $views[$role] ?? 'dashboard';
        $data = [];

        if ($role === 'siswa_sd' || $role === 'siswa_smp') {
            $user = $request->user();
            $siswa = $user->siswa;
            $kelas = $siswa->kelas;

            $sudahIsiKondisi = \App\Models\KondisiKelas::query()->where('siswa_id', $siswa->id)
                ->where('tanggal', now()->format('Y-m-d'))
                ->exists();

            $remedialActive = \App\Models\Remedial::query()->where('siswa_id', $siswa->id)
                ->where('status', 'pending')
                ->with('mapel', 'nilai')
                ->get();
                
            $isKelas9 = $this->isKelas9($kelas);
            $nilaiKti = $isKelas9 ? \App\Models\NilaiKti::query()->where('siswa_id', $siswa->id)->first() : null;
            $ktiBimbingans = $isKelas9 ? \App\Models\KtiBimbingan::query()->where('siswa_id', $siswa->id)->orderBy('created_at', 'desc')->get() : collect();

            $data = [
                'user' => $user,
                'siswa' => $siswa,
                'kelas' => $kelas,
                'rapors' => \App\Models\Rapor::query()
                    ->where('siswa_id', $siswa->id)
                    ->with(['items.mapel'])
                    ->orderByDesc('tahun_ajaran')
                    ->orderByDesc('semester')
                    ->get()
                    ->keyBy('jenis_rapor'),
                'mapels' => Mapel::query()
                    ->whereNotIn('nama_mapel', ['Istirahat', 'Dzuhur Time', 'Ashar Time', 'Upacara / Flash', 'Dhuha Time', 'Upacara / Pentas Seni', 'Qailullah', 'Sholat dan Makan', 'Pulang / Penjemputan Orang Tua', 'Snack Time', 'Transisi / Pindah ke Kelas', 'Shalat Ashar', 'Kegiatan Pramuka'])
                    ->where(function($q) use ($kelas, $siswa) {
                        $jadwalMapels = Jadwal::query()->where('kelas_id', $kelas?->id)->pluck('mapel_id');
                        if ($jadwalMapels->isNotEmpty()) {
                            $q->whereIn('id', $jadwalMapels);
                        } else {
                            $nilaiMapels = \App\Models\Nilai::query()->where('siswa_id', $siswa->id)->pluck('mapel_id')->unique();
                            if ($nilaiMapels->isNotEmpty()) {
                                $q->whereIn('id', $nilaiMapels);
                            } else {
                                $q->whereRaw('1 = 0');
                            }
                        }
                    })
                    ->get(),
                'jadwalHariIni' => Jadwal::query()->where('kelas_id', $kelas?->id)
                    ->where('hari', now()->locale('id')->dayName)
                    ->with('mapel', 'guru')
                    ->orderBy('jam_mulai')
                    ->get(),
                'semuaJadwal' => Jadwal::query()->where('kelas_id', $kelas?->id)
                    ->with('mapel', 'guru')
                    ->orderBy('hari')
                    ->orderBy('jam_mulai')
                    ->get(),
                'tugas' => Tugas::query()->where('kelas_id', $kelas?->id)
                    ->where('tipe', 'tugas')
                    ->with('mapel')
                    ->orderBy('tanggal_deadline')
                    ->get(),
                'ulangan' => Tugas::query()->where('kelas_id', $kelas?->id)
                    ->where('tipe', 'ulangan')
                    ->with('mapel')
                    ->orderBy('tanggal_deadline')
                    ->get(),
                'nilai' => Nilai::query()->where('siswa_id', $siswa->id)
                    ->with(['mapel', 'banding'])
                    ->get(),
                'nilaiSekolah' => Nilai::query()->where('siswa_id', $siswa->id)
                    ->where('jenis_nilai', 'biasa')
                    ->with('mapel')
                    ->get(),
                'nilaiUnggulan' => Nilai::query()->where('siswa_id', $siswa->id)
                    ->where('jenis_nilai', 'unggulan')
                    ->with('mapel')
                    ->get(),
                'tahfidzSetoran' => TahfidzSetoran::query()->where('siswa_id', $siswa->id)
                    ->with('guru')
                    ->orderBy('tanggal', 'desc')
                    ->get(),
                'tahfidzProgress' => TahfidzProgress::query()->where('siswa_id', $siswa->id)->first(),
                'tahsinSetoran' => TahsinSetoran::query()->where('siswa_id', $siswa->id)
                    ->with(['guru', 'kelas'])
                    ->orderBy('tanggal', 'desc')
                    ->get(),
                'pengumuman' => Pengumuman::where(function ($q) use ($role) {
                        $q->whereNull('target_role')
                            ->orWhere('target_role', $role)
                            ->orWhere('target_role', 'semua');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get(),
                'pesan' => Pesan::query()->where('penerima_id', $user->id)
                    ->with(['pengirim', 'siswa'])
                    ->orderBy('created_at', 'desc')
                    ->get(),
                'guruKelas' => $kelas?->load('siswa'),
                'kehadiran' => Kehadiran::query()->where('siswa_id', $siswa->id)
                    ->orderBy('tanggal', 'desc')
                    ->get(),
                'kehadiranSummary' => $this->attendanceSummary(
                    Kehadiran::query()->where('siswa_id', $siswa->id)->get()
                ),
                'catatanWali' => CatatanWali::query()->where('siswa_id', $siswa->id)
                    ->with('guru')
                    ->latest()
                    ->first(),
                'badges' => Badge::query()->with(['siswa' => function ($q) use ($siswa) {
                    $q->where('siswa_id', '=', $siswa->id, 'and');
                }])->get(),
                'guruUsers' => \App\Models\User::query()->where('role', 'guru')->get(),
                'nilaiChart' => Nilai::query()->where('siswa_id', $siswa->id)
                    ->where('jenis_nilai', 'biasa')
                    ->with('mapel')
                    ->get()
                    ->map(fn($n) => ['nama_mapel' => $n->mapel->nama_mapel ?? $n->mapel->kode ?? 'Mapel', 'nilai' => $n->nilai]),
                'sudahIsiKondisi' => $sudahIsiKondisi,
                'remedialActive' => $remedialActive,
                'isKelas9' => $isKelas9,
                'nilaiKti' => $nilaiKti,
                'ktiBimbingans' => $ktiBimbingans,
            ];

            $siswaKelas = $kelas
                ? \App\Models\Siswa::query()->where('kelas_id', $kelas->id)->get()
                : collect();
            $rataMap = [];
            foreach ($siswaKelas as $s) {
                $rataMap[$s->id] = round(Nilai::query()->where('siswa_id', $s->id)->where('jenis_nilai', 'biasa')->avg('nilai') ?? 0, 1);
            }
            arsort($rataMap);
            $total = count($rataMap);
            $rank = $total > 0 ? 1 : 0;
            foreach ($rataMap as $id => $r) {
                if ($id === $siswa->id) break;
                $rank++;
            }
            $data['peringkat'] = ['rank' => $rank, 'total' => $total];
        }

        if ($role === 'guru') {
            $user = $request->user();
            $guru = $user->guru;

            $kelasIds = Jadwal::where('guru_id', $guru->id)->pluck('kelas_id')->unique();
            $kelasYangDiajar = Kelas::whereIn('id', $kelasIds)
                ->withCount('siswa')
                ->get()
                ->map(function ($k) use ($guru) {
                    $siswaIds = $k->siswa()->pluck('id');
                    $mapelIds = $guru->mapels->pluck('id');
                    $k->rataNilai = Nilai::whereIn('siswa_id', $siswaIds)
                        ->whereIn('mapel_id', $mapelIds)
                        ->avg('nilai') ?? 0;
                    return $k;
                });

            $tugas = Tugas::where('guru_id', $guru->id)
                ->with(['mapel', 'kelas'])
                ->orderBy('tanggal_deadline')
                ->get();

            $pesan = Pesan::where('penerima_id', $user->id)
                ->with(['pengirim', 'siswa'])
                ->orderBy('created_at', 'desc')
                ->get();

            $pengumuman = Pengumuman::orderBy('created_at', 'desc')->get();

            $siswaIdsAll = \App\Models\Siswa::whereIn('kelas_id', $kelasIds)->pluck('id');
            $remedialActive = \App\Models\Remedial::whereIn('siswa_id', $siswaIdsAll)
                ->where('status', 'pending')
                ->with(['siswa.kelas', 'mapel', 'nilai'])
                ->get();

            $kondisiKelasHistory = \App\Models\KondisiKelas::whereIn('kelas_id', $kelasIds)
                ->selectRaw('tanggal, kelas_id, AVG(hubungan_guru_siswa) as avg_hubungan, AVG(siswa_nyaman) as avg_nyaman, AVG(siswa_minta_bantuan) as avg_bantuan')
                ->groupBy('tanggal', 'kelas_id')
                ->orderBy('tanggal', 'desc')
                ->with('kelas')
                ->get();

            $ktiBimbinganReviews = \App\Models\KtiBimbingan::where('status', 'pending')
                ->with(['siswa.kelas'])
                ->orderBy('created_at', 'desc')
                ->get();

            $siswaKelas9 = \App\Models\Siswa::whereHas('kelas', fn ($q) => $this->whereKelas9($q))
                ->with('kelas')
                ->get();

            $nilaiKtiRekap = \App\Models\NilaiKti::whereIn('siswa_id', $siswaKelas9->pluck('id'))->get();
            $materiJadwal = Jadwal::where('guru_id', $guru->id)->get();
            $materiMapelIds = $materiJadwal->pluck('mapel_id')->merge($guru->mapels->pluck('id'))->filter()->unique();
            $materiKelasIds = $materiJadwal->pluck('kelas_id')->filter()->unique();

            $data = [
                'user'                 => $user,
                'guru'                 => $guru,
                'kelasYangDiajar'      => $kelasYangDiajar,
                'tugas'                => $tugas,
                'pesan'                => $pesan,
                'pengumuman'           => $pengumuman,
                'remedialActive'       => $remedialActive,
                'kondisiKelasHistory'  => $kondisiKelasHistory,
                'ktiBimbinganReviews'  => $ktiBimbinganReviews,
                'siswaKelas9'          => $siswaKelas9,
                'nilaiKtiRekap'        => $nilaiKtiRekap,
                'materiList'           => \App\Models\Materi::where('guru_id', $guru->id)
                    ->with(['mapel', 'kelas', 'approvalHistories.actor'])
                    ->latest()
                    ->get(),
                'materiMapelList'      => Mapel::akademik()->whereIn('id', $materiMapelIds)->get(),
                'materiKelasList'      => Kelas::whereIn('id', $materiKelasIds)->get(),
                'absensiKelasList'     => Kelas::whereIn('id', $kelasIds)->with('siswa')->get(),
                'absensiMapelList'     => Mapel::akademik()->whereIn('id', $materiMapelIds)->get(),
                'riwayatAbsensi'       => Kehadiran::whereIn('kelas_id', $kelasIds)
                    ->orWhereHas('siswa', fn ($q) => $q->whereIn('kelas_id', $kelasIds))
                    ->with(['siswa.kelas', 'mapel'])
                    ->orderByDesc('tanggal')
                    ->take(50)
                    ->get(),
                'absensiHariIni'       => Kehadiran::whereIn('kelas_id', $kelasIds)
                    ->whereDate('tanggal', now()->format('Y-m-d'))
                    ->get(),
                'jurnalHarianList'     => LaporanMengajar::where('guru_id', $guru->id)
                    ->where('tipe', 'jurnal_harian')
                    ->with(['mapel', 'kelas'])
                    ->latest('tanggal')
                    ->take(30)
                    ->get(),
                'jurnalSikapList'      => \App\Models\JurnalSikap::where('guru_id', $guru->id)
                    ->with(['siswa.kelas'])
                    ->latest('tanggal')
                    ->take(30)
                    ->get(),
                'programPengayaanList' => \App\Models\ProgramPengayaan::where('guru_id', $guru->id)
                    ->with(['mapel', 'kelas'])
                    ->latest()
                    ->take(30)
                    ->get(),
                'programRemedialList'  => \App\Models\ProgramRemedial::where('guru_id', $guru->id)
                    ->with(['siswa.kelas', 'mapel'])
                    ->latest()
                    ->take(30)
                    ->get(),
                'adminChecklistList'   => \App\Models\AdministrasiGuruChecklist::where('guru_id', $guru->id)
                    ->latest()
                    ->get(),
                'siswaDiajar'          => Siswa::whereIn('kelas_id', $kelasIds)
                    ->with('kelas')
                    ->orderBy('nama')
                    ->get(),
                'orangTuaDiajar'       => \App\Models\OrangTua::whereHas('siswa', fn ($q) => $q->whereIn('kelas_id', $kelasIds))
                    ->with(['user', 'siswa.kelas'])
                    ->orderBy('nama')
                    ->get(),
            ];
        }

        if ($role === 'admin') {
            $teachersList = Guru::with('mapels')->get();
            $guruReportsData = [];

            foreach ($teachersList as $g) {
                $today = now()->format('Y-m-d');
                $startOfWeek = now()->startOfWeek()->format('Y-m-d');
                $endOfWeek = now()->endOfWeek()->format('Y-m-d');
                $startOfMonth = now()->startOfMonth()->format('Y-m-d');
                $endOfMonth = now()->endOfMonth()->format('Y-m-d');

                $hasHarian = LaporanMengajar::query()->where('guru_id', $g->id)
                    ->where('tipe', 'harian')
                    ->where('tanggal', $today)
                    ->exists();

                $hasMingguan = LaporanMengajar::query()->where('guru_id', $g->id)
                    ->where('tipe', 'mingguan')
                    ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                    ->exists();

                $hasBulanan = LaporanMengajar::query()->where('guru_id', $g->id)
                    ->where('tipe', 'bulanan')
                    ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->exists();

                $allReports = LaporanMengajar::query()->where('guru_id', $g->id)
                    ->orderBy('tanggal', 'desc')
                    ->get()
                    ->map(function ($l) {
                        $isiTeks = '';
                        if (is_array($l->isi)) {
                            if ($l->tipe === 'harian') {
                                $kList = [];
                                if (!empty($l->isi['checklists'])) {
                                    $tidakList = [];
                                    foreach ($l->isi['checklists'] as $key => $chk) {
                                        if (($chk['status'] ?? '') === 'tidak') {
                                            $tidakList[] = $key;
                                        }
                                    }
                                    if (!empty($tidakList)) {
                                        $kList[] = "• Indikator 'Tidak': " . implode(', ', $tidakList);
                                    }
                                }
                                if (!empty($l->isi['kendala'])) {
                                    foreach ($l->isi['kendala'] as $k) {
                                        $kList[] = "• [Kendala {$k['bidang']}] {$k['deskripsi']}";
                                    }
                                }
                                if (!empty($l->isi['pemetaan_masalah_siswa'])) {
                                    foreach ($l->isi['pemetaan_masalah_siswa'] as $s) {
                                        $kList[] = "• [Siswa {$s['nama_siswa']}] {$s['permasalahan']}";
                                    }
                                }
                                if (!empty($l->isi['refleksi']['kendala_terbesar'])) {
                                    $kList[] = "• [Refleksi] Kendala: {$l->isi['refleksi']['kendala_terbesar']}";
                                }

                                if (empty($kList)) {
                                    $kList[] = "KBM Terlaksana Baik (Tidak ada kendala)";
                                }

                                if (!empty($l->isi['catatan_umum'])) {
                                    $kList[] = "Catatan: " . $l->isi['catatan_umum'];
                                }
                                $isiTeks = implode("\n", $kList);
                            } elseif ($l->tipe === 'mingguan') {
                                $mList = [];
                                if (!empty($l->isi['rekap_pembelajaran'])) {
                                    foreach ($l->isi['rekap_pembelajaran'] as $r) {
                                        if (!empty($r['materi'])) {
                                            $mList[] = "• {$r['hari']}: {$r['materi']} (HOTS: {$r['hots']})";
                                        }
                                    }
                                }
                                if (!empty($l->isi['tindak_lanjut'])) {
                                    foreach ($l->isi['tindak_lanjut'] as $t) {
                                        $mList[] = "• [Tindak Lanjut] {$t['program']} untuk {$t['sasaran']}";
                                    }
                                }
                                if (!empty($l->isi['catatan_umum'])) {
                                    $mList[] = "Catatan: " . $l->isi['catatan_umum'];
                                }
                                $isiTeks = implode("\n", $mList);
                            } else {
                                $bList = [];
                                if (!empty($l->isi['capaian_belajar_bulanan'])) {
                                    foreach ($l->isi['capaian_belajar_bulanan'] as $c) {
                                        if (!empty($c['keterangan']) || !empty($c['capaian'])) {
                                            $bList[] = "• CP {$c['elemen_cp']}: Capaian {$c['capaian']} (Target: {$c['target']})";
                                        }
                                    }
                                }
                                if (!empty($l->isi['catatan_umum'])) {
                                    $bList[] = "Catatan: " . $l->isi['catatan_umum'];
                                }
                                $isiTeks = implode("\n", $bList);
                            }
                        } else {
                            $isiTeks = $l->isi;
                        }

                        return [
                            'id' => $l->id,
                            'tipe' => ucfirst($l->tipe),
                            'tanggal' => $l->tanggal->format('d M Y'),
                            'isi' => $isiTeks,
                        ];
                    });

                $classNames = Jadwal::query()->where('guru_id', $g->id)
                    ->with('kelas')
                    ->get()
                    ->pluck('kelas.nama_kelas')
                    ->unique()
                    ->implode(', ') ?: '—';

                $guruReportsData[] = [
                    'id' => $g->id,
                    'nama' => $g->nama,
                    'mapel' => $g->mapel->nama_mapel ?? '—',
                    'kelas' => $classNames,
                    'harian' => $hasHarian ? 'Lengkap' : 'Belum Isi',
                    'mingguan' => $hasMingguan ? 'Lengkap' : 'Belum Isi',
                    'bulanan' => $hasBulanan ? 'Lengkap' : 'Belum Isi',
                    'reports' => $allReports,
                ];
            }

            $data = [
                'guruReportsData' => $guruReportsData
            ];
        }
        if ($role === 'kepala_sekolah') {
            $totalSiswa = \App\Models\Siswa::count();
            $totalGuru = \App\Models\Guru::count();
            $totalOrtu = \App\Models\User::query()->where('role', 'orang_tua')->count();
            $totalKelas = \App\Models\Kelas::count();

            $siswaBaru = \App\Models\Siswa::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();

            $guruBaru = \App\Models\Guru::whereYear('created_at', now()->year)->count();

            $ortuBaru = \App\Models\User::where('role', 'orang_tua')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();

            $kelasBaru = \App\Models\Kelas::whereYear('created_at', now()->year)->count();



            $akademikNasional = round(\App\Models\Nilai::query()->where('jenis_nilai', 'biasa')->avg('nilai') ?? 0, 1);
            $internasional = round(\App\Models\Nilai::query()->where('jenis_nilai', 'unggulan')->avg('nilai') ?? 0, 1);

            $avgMakhroj = \App\Models\TahfidzAyatNilai::avg('makhroj') ?? 0;
            $avgTajwid = \App\Models\TahfidzAyatNilai::avg('tajwid') ?? 0;
            $avgKelancaran = \App\Models\TahfidzAyatNilai::avg('kelancaran') ?? 0;
            $tahfidzScale4 = ($avgMakhroj + $avgTajwid + $avgKelancaran) / 3;
            $tahfidz = $tahfidzScale4 > 0 ? round(($tahfidzScale4 / 4) * 100, 1) : 0;
            $progressTahfidzKepala = TahfidzProgress::query()->avg('progress_percent');
            if ($progressTahfidzKepala !== null) {
                $tahfidz = round($progressTahfidzKepala, 1);
            }

            $targetAkademik = 80;
            $targetInternasional = 75;
            $targetTahfidz = 85;

            $stsAkademik = $akademikNasional >= $targetAkademik ? 'Sangat Baik' : 'Perlu Peningkatan';
            $clrAkademik = $akademikNasional >= $targetAkademik ? 'var(--teal)' : 'var(--orange)';
            $typAkademik = $akademikNasional >= $targetAkademik ? 'up' : 'down';

            $stsInternasional = $internasional >= $targetInternasional ? 'Sangat Baik' : 'Perlu Peningkatan';
            $clrInternasional = $internasional >= $targetInternasional ? 'var(--teal)' : 'var(--orange)';
            $typInternasional = $internasional >= $targetInternasional ? 'up' : 'down';

            $stsTahfidz = $tahfidz >= $targetTahfidz ? 'Sangat Baik' : 'Perlu Peningkatan';
            $clrTahfidz = $tahfidz >= $targetTahfidz ? 'var(--teal)' : 'var(--orange)';
            $typTahfidz = $tahfidz >= $targetTahfidz ? 'up' : 'down';

            $bulan = now()->month;
            $semester = ($bulan >= 7 && $bulan <= 12) ? 'Semester Ganjil' : 'Semester Genap';


            $tigaHariLalu = now()->subDays(3)->format('Y-m-d');
            $kelasGakSehat = [];

            $kondisiKelasRecs = \App\Models\KondisiKelas::where('tanggal', '>=', $tigaHariLalu)
                ->with(['kelas.waliKelas'])
                ->get();

            $kelasGroup = $kondisiKelasRecs->groupBy('kelas_id');
            $todayStr = now()->format('Y-m-d');

            foreach ($kelasGroup as $kelasId => $records) {
                $recordsByDate = $records->groupBy(function ($item) {
                    return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                });

                $sumDailyAvgs = 0;
                $countDays = 0;

                foreach ($recordsByDate as $date => $dailyRecords) {
                    $dailySum = 0;
                    foreach ($dailyRecords as $rec) {
                        $dailySum += ($rec->hubungan_guru_siswa + $rec->siswa_nyaman + $rec->siswa_minta_bantuan) / 3;
                    }
                    $sumDailyAvgs += ($dailySum / $dailyRecords->count());
                    $countDays++;
                }

                $avgKelas = $countDays > 0 ? $sumDailyAvgs / $countDays : 0;

                $avgToday = 0;
                $hasTodayData = $recordsByDate->has($todayStr);

                if ($hasTodayData) {
                    $dailyRecords = $recordsByDate->get($todayStr);
                    $dailySum = 0;
                    foreach ($dailyRecords as $rec) {
                        $dailySum += ($rec->hubungan_guru_siswa + $rec->siswa_nyaman + $rec->siswa_minta_bantuan) / 3;
                    }
                    $avgToday = $dailySum / $dailyRecords->count();
                }

                $isWarning = false;
                if ($countDays > 0 && $avgKelas <= 3.5) {
                    if ($hasTodayData && $avgToday > 3.5) {
                        $isWarning = false; // Trend sudah membaik hari ini
                    } else {
                        $isWarning = true;
                    }
                }

                if ($isWarning) {
                    $kls = $records->first()->kelas;
                    $kelasGakSehat[] = [
                        'kelas' => $kls->nama_kelas ?? 'Unknown',
                        'wali' => $kls->waliKelas ? $kls->waliKelas->nama : 'Belum Ada Wali Kelas',
                        'wali_user_id' => $kls->waliKelas ? $kls->waliKelas->user_id : null,
                        'status' => $avgKelas <= 2.5 ? 'Kritis' : 'Waspada',
                        'stressLevel' => $avgKelas <= 2.5 ? 'Tinggi (Kenyamanan Rendah)' : 'Sedang (Mulai Menurun)',
                        'detail' => 'Penurunan skor Vulnerability Index (kenyamanan dan dukungan) terdeteksi dalam 3 hari terakhir.',
                        'mood' => number_format($avgKelas, 1) . ' / 5.0',
                        'bg' => $avgKelas <= 2.5 ? '#fef2f2' : '#fffbeb',
                        'border' => $avgKelas <= 2.5 ? '#fca5a5' : '#fcd34d',
                        'color' => $avgKelas <= 2.5 ? '#ef4444' : '#f59e0b',
                    ];
                }
            }

            $guruKinerja = [];
            $allGuru = \App\Models\Guru::with('mapels')->get();
            foreach ($allGuru as $g) {
                $today = now()->format('Y-m-d');
                $startOfWeek = now()->startOfWeek()->format('Y-m-d');
                $endOfWeek = now()->endOfWeek()->format('Y-m-d');
                $startOfMonth = now()->startOfMonth()->format('Y-m-d');

                $yesterday = now()->subDay()->format('Y-m-d');
                $lastWeekStart = now()->subWeek()->startOfWeek()->format('Y-m-d');
                $lastWeekEnd = now()->subWeek()->endOfWeek()->format('Y-m-d');
                $lastMonthStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $lastMonthEnd = now()->subMonth()->endOfMonth()->format('Y-m-d');

                $checkHarian = $g->created_at->format('Y-m-d') <= $yesterday;
                $checkMingguan = $g->created_at->format('Y-m-d') <= $lastWeekEnd;
                $checkBulanan = $g->created_at->format('Y-m-d') <= $lastMonthEnd;

                $hasHarian = !$checkHarian || \App\Models\LaporanMengajar::where('guru_id', $g->id)
                    ->where('tipe', 'harian')->where('tanggal', $yesterday)->exists();
                $hasMingguan = !$checkMingguan || \App\Models\LaporanMengajar::where('guru_id', $g->id)
                    ->where('tipe', 'mingguan')->whereBetween('tanggal', [$lastWeekStart, $lastWeekEnd])->exists();
                $hasBulanan = !$checkBulanan || \App\Models\LaporanMengajar::where('guru_id', $g->id)
                    ->where('tipe', 'bulanan')->whereBetween('tanggal', [$lastMonthStart, $lastMonthEnd])->exists();

                $skor = 100;
                $details = [];
                if (!$hasHarian) {
                    $skor -= 10;
                    $details[] = 'Harian (Kemarin) blm diisi';
                }
                if (!$hasMingguan) {
                    $skor -= 10;
                    $details[] = 'Pekanan (Lalu) blm diisi';
                }
                if (!$hasBulanan) {
                    $skor -= 10;
                    $details[] = 'Bulanan (Lalu) blm diisi';
                }

                $detailStr = empty($details) ? 'Disiplin mengisi administrasi Monev secara lengkap (Sesuai SOP).' : 'Tunggakan: ' . implode(', ', $details) . '.';

                if ($skor >= 90) {
                    $status = 'Sangat Baik (Safe Zone)';
                    $color = 'var(--teal)';
                } elseif ($skor >= 70) {
                    $status = 'Perlu Perhatian (Observation Zone)';
                    $color = 'var(--orange)';
                } elseif ($skor >= 50) {
                    $status = 'Kritis (Risk Zone - Curriculum Lockdown)';
                    $color = 'var(--red)';
                } else {
                    $status = 'Sangat Kritis (Terminal Zone - Rekomendasi PHK)';
                    $color = '#000000';
                }

                $guruKinerja[] = [
                    'user_id' => $g->user_id,
                    'nama' => $g->nama,
                    'mapel' => $g->mapel->nama_mapel ?? '-',
                    'skor' => $skor,
                    'status' => $status,
                    'detail' => $detailStr,
                    'color' => $color
                ];
            }
            usort($guruKinerja, function ($a, $b) {
                return $a['skor'] <=> $b['skor'];
            });

            $semuaKelas = \App\Models\Kelas::with('waliKelas')->withCount('siswa')->get();
            $tahfidzSiswaOptions = \App\Models\Siswa::with(['kelas', 'kelasQuran'])
                ->whereHas('user', fn ($q) => $q->whereIn('role', ['siswa_sd', 'siswa_smp']))
                ->orderBy('nama')
                ->get();
            $tahfidzProgressRows = \App\Models\Siswa::with(['kelas', 'kelasQuran', 'tahfidzProgress'])
                ->whereHas('user', fn ($q) => $q->whereIn('role', ['siswa_sd', 'siswa_smp']))
                ->orderBy('nama')
                ->get();

            $data = [
                'statSiswa' => $totalSiswa,
                'statGuru' => $totalGuru,
                'statOrtu' => $totalOrtu,
                'statKelas' => $totalKelas,
                'akademikNasional' => $akademikNasional,
                'internasional' => $internasional,
                'tahfidz' => $tahfidz,
                'siswaBaru' => $siswaBaru,
                'guruBaru' => $guruBaru,
                'ortuBaru' => $ortuBaru,
                'kelasBaru' => $kelasBaru,
                'targetAkademik' => $targetAkademik,
                'targetInternasional' => $targetInternasional,
                'targetTahfidz' => $targetTahfidz,
                'stsAkademik' => $stsAkademik,
                'clrAkademik' => $clrAkademik,
                'typAkademik' => $typAkademik,
                'stsInternasional' => $stsInternasional,
                'clrInternasional' => $clrInternasional,
                'typInternasional' => $typInternasional,
                'stsTahfidz' => $stsTahfidz,
                'clrTahfidz' => $clrTahfidz,
                'typTahfidz' => $typTahfidz,
                'semester' => $semester,
                'kelasGakSehat' => $kelasGakSehat,
                'guruKinerja' => $guruKinerja,
                'semuaKelas' => $semuaKelas,
                'listSiswa' => \App\Models\Siswa::with('kelas', 'user')->paginate(10, ['*'], 'page_siswa'),
                'listGuru' => \App\Models\Guru::with('user', 'mapels', 'jadwal.kelas')->paginate(10, ['*'], 'page_guru'),
                'listOrtu' => \App\Models\User::where('role', 'orang_tua')->with('orangTua.siswa.kelas')->paginate(10, ['*'], 'page_ortu'),
                'listKelas' => \App\Models\Kelas::with('waliKelas')->withCount('siswa')->paginate(10, ['*'], 'page_kelas'),
                'listKelasQuran' => \App\Models\KelasQuran::paginate(10, ['*'], 'page_kelas_quran'),
                'allGurus' => \App\Models\Guru::all(),
                'tahfidzSiswaOptions' => $tahfidzSiswaOptions,
                'tahfidzKelasOptions' => \App\Models\Kelas::withCount('siswa')->orderBy('id')->get(),
                'tahfidzKelasQuranOptions' => \App\Models\KelasQuran::orderBy('jenjang')->orderBy('tingkat')->orderBy('nama_kelas')->get(),
                'tahfidzProgressRows' => $tahfidzProgressRows,
                'materiApprovalList' => \App\Models\Materi::with(['guru.user', 'mapel', 'kelas', 'reviewer', 'approvalHistories.actor'])
                    ->whereIn('status', ['pending', 'approved', 'rejected', 'revision_requested'])
                    ->latest()
                    ->get(),
                'materiApprovalGurus' => \App\Models\Guru::orderBy('nama')->get(),
                'materiApprovalMapels' => \App\Models\Mapel::akademik()->orderBy('nama_mapel')->get(),
                'materiApprovalKelas' => \App\Models\Kelas::orderBy('jenjang')->orderBy('kode_kelas')->get(),
            ];
        } elseif ($role === 'admin') {
            $totalSiswa = \App\Models\Siswa::count();
            $totalGuru = \App\Models\Guru::count();
            $totalOrtu = \App\Models\User::where('role', 'orang_tua')->count();
            $totalKelas = \App\Models\Kelas::count() + \App\Models\KelasQuran::count();
            
            $logAktivitas = \App\Models\LogAktivitas::latest()->take(6)->get();

            $siswaSD13 = \App\Models\Siswa::whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '1%')->orWhere('nama_kelas', 'like', '2%')->orWhere('nama_kelas', 'like', '3%');
            })->count();
            $siswaSD46 = \App\Models\Siswa::whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '4%')->orWhere('nama_kelas', 'like', '5%')->orWhere('nama_kelas', 'like', '6%');
            })->count();
            $siswa7 = \App\Models\Siswa::whereHas('kelas', function($q) { $q->where('nama_kelas', 'like', '7%'); })->count();
            $siswa8 = \App\Models\Siswa::whereHas('kelas', function($q) { $q->where('nama_kelas', 'like', '8%'); })->count();
            $siswa9 = \App\Models\Siswa::whereHas('kelas', fn ($q) => $this->whereKelas9($q))->count();

            $dist = [
                'sd13' => ['count' => $siswaSD13, 'pct' => $totalSiswa > 0 ? round(($siswaSD13/$totalSiswa)*100) : 0],
                'sd46' => ['count' => $siswaSD46, 'pct' => $totalSiswa > 0 ? round(($siswaSD46/$totalSiswa)*100) : 0],
                'smp7' => ['count' => $siswa7, 'pct' => $totalSiswa > 0 ? round(($siswa7/$totalSiswa)*100) : 0],
                'smp8' => ['count' => $siswa8, 'pct' => $totalSiswa > 0 ? round(($siswa8/$totalSiswa)*100) : 0],
                'smp9' => ['count' => $siswa9, 'pct' => $totalSiswa > 0 ? round(($siswa9/$totalSiswa)*100) : 0],
            ];

            $kkmList = \App\Models\Mapel::all()->map(function ($m) {
                return [
                    'mapel' => $m->nama_mapel,
                    'biasa' => 75, 
                    'unggulan' => 80
                ];
            });

            $anomalies = \App\Models\Nilai::with(['siswa.kelas', 'mapel', 'tugas.guru'])
                ->whereRaw('ABS(nilai - COALESCE(nilai_bahasa, nilai)) > 15')
                ->take(10)->get()
                ->map(function ($n) {
                    return [
                        'nama' => $n->siswa->nama ?? '-',
                        'kelas' => $n->siswa->kelas->nama_kelas ?? '-',
                        'mapel' => $n->mapel->nama_mapel ?? '-',
                        'biasa' => (int) $n->nilai,
                        'unggulan' => (int) $n->nilai_bahasa,
                        'status' => 'Flagged',
                        'guru' => $n->tugas->guru->nama ?? '-'
                    ];
                });
            
            $soalList = \App\Models\CbtSoal::inRandomOrder()->take(5)->get()->map(function ($s, $i) {
                return [
                    'no' => $i + 1,
                    'teks' => \Illuminate\Support\Str::limit(strip_tags($s->pertanyaan), 60),
                    'tipe' => 'Otomatis'
                ];
            });

            $guruReportsData = \App\Models\Guru::with(['laporanMengajar', 'mapels'])->get()->map(function ($g) {
                $harian = $g->laporanMengajar->where('tipe', 'harian')->count();
                $mingguan = $g->laporanMengajar->where('tipe', 'mingguan')->count();
                $bulanan = $g->laporanMengajar->where('tipe', 'bulanan')->count();
                
                return [
                    'nama' => $g->nama,
                    'mapel' => $g->mapels->first()->nama_mapel ?? 'Umum',
                    'kelas' => 'Semua Kelas',
                    'harian' => $harian > 0 ? 'Lengkap' : 'Belum Isi',
                    'mingguan' => $mingguan > 0 ? 'Lengkap' : 'Belum Isi',
                    'bulanan' => $bulanan > 0 ? 'Lengkap' : 'Belum Isi'
                ];
            });

            $materiAjarData = \App\Models\Materi::with('guru')->latest()->take(10)->get()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'judul' => $m->judul,
                    'guru' => $m->guru->nama ?? '-',
                    'status' => ucfirst($m->status)
                ];
            });

            $siswaKelas9 = \App\Models\Siswa::whereHas('kelas', fn ($q) => $this->whereKelas9($q))
                ->with(['kelas'])
                ->get();

            $ktiData = \App\Models\NilaiKti::whereIn('siswa_id', $siswaKelas9->pluck('id'))->get()->keyBy('siswa_id');
            $tahfidzData = \App\Models\TahfidzSetoran::whereIn('siswa_id', $siswaKelas9->pluck('id'))
                ->selectRaw('siswa_id, COUNT(*) as jumlah_surat, MAX(tanggal) as last_setor')
                ->groupBy('siswa_id')->get()->keyBy('siswa_id');

            $karyaTahfidzList = $siswaKelas9->map(function ($s) use ($ktiData, $tahfidzData) {
                $kti = $ktiData->get($s->id);
                $t = $tahfidzData->get($s->id);
                return [
                    'nama' => $s->nama,
                    'kelas' => $s->kelas->nama_kelas,
                    'progres_kti' => $kti ? ($kti->current_bab ?? 'Bab 1') : 'Belum Mulai',
                    'target_tahfidz' => $t ? $t->jumlah_surat . ' Surat' : '0 Surat',
                    'status_tahfidz' => $t ? 'Lancar' : 'Belum Setor',
                    'last_setor' => $t ? \Carbon\Carbon::parse($t->last_setor)->diffForHumans() : '-'
                ];
            });

            $data = [
                'totalSiswa' => $totalSiswa,
                'totalGuru' => $totalGuru,
                'totalOrtu' => $totalOrtu,
                'totalKelas' => $totalKelas,
                'logAktivitas' => $logAktivitas,
                'dist' => $dist,
                'listSiswa' => \App\Models\Siswa::with('kelas', 'user')->paginate(10, ['*'], 'page_siswa'),
                'listGuru' => \App\Models\Guru::with('user')->paginate(10, ['*'], 'page_guru'),
                'listOrtu' => \App\Models\User::where('role', 'orang_tua')->with('orangTua.siswa.kelas')->paginate(10, ['*'], 'page_ortu'),
                'listKelas' => \App\Models\Kelas::with('waliKelas')->withCount('siswa')->paginate(10, ['*'], 'page_kelas'),
                'listKelasQuran' => \App\Models\KelasQuran::paginate(10, ['*'], 'page_kelas_quran'),
                'allGurus' => \App\Models\Guru::all(),
                'kkmList' => $kkmList,
                'anomalies' => $anomalies,
                'soalList' => $soalList,
                'guruReportsData' => $guruReportsData,
                'materiAjarData' => $materiAjarData,
                'karyaTahfidzList' => $karyaTahfidzList
            ];
        }

        if ($role === 'orang_tua') {
            $user = $request->user();
            $ortu = $user->orangTua;
            $anak = $ortu
                ? $ortu->siswa()->with(['kelas', 'user'])->orderBy('nama')->get()
                : collect();
            $kelasIds = $anak->pluck('kelas_id')->filter()->unique();
            $guruIds = Jadwal::whereIn('kelas_id', $kelasIds)->pluck('guru_id')->unique();
            $anakTahfidzData = $anak->mapWithKeys(function ($child) {
                return [
                    $child->id => [
                        'setoran' => TahfidzSetoran::where('siswa_id', $child->id)
                            ->with('guru')
                            ->orderByDesc('tanggal')
                            ->get(),
                        'progress' => TahfidzProgress::where('siswa_id', $child->id)->first(),
                    ],
                ];
            });

            $data = [
                'user' => $user,
                'ortu' => $ortu,
                'anak' => $anak,
                'anakTahfidzData' => $anakTahfidzData,
                'daftarGuru' => Guru::with(['user', 'mapel', 'mapels'])
                    ->whereIn('id', $guruIds)
                    ->orderBy('nama')
                    ->get(),
                'pesanGuru' => Pesan::where('penerima_id', $user->id)
                    ->with(['pengirim', 'siswa'])
                    ->orderBy('created_at', 'desc')
                    ->get(),
                'pengumuman' => Pengumuman::where(function ($q) {
                        $q->whereNull('target_role')
                            ->orWhere('target_role', 'orang_tua')
                            ->orWhere('target_role', 'semua');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get(),
            ];
        }

        $view = $views[$role] ?? 'dashboard';
        return view($view, $data);
    }

    private function attendanceSummary($records): array
    {
        $total = $records->count();
        $hadir = $records->where('status', 'hadir')->count();
        $sakit = $records->where('status', 'sakit')->count();
        $izin = $records->where('status', 'izin')->count();
        $alpha = $records->where('status', 'alpha')->count();

        return [
            'total_hari_efektif' => $total,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpha' => $alpha,
            'total_tidak_hadir' => $sakit + $izin + $alpha,
            'persentase_hadir' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
            'tidak_hadir_records' => $records->whereIn('status', ['sakit', 'izin', 'alpha'])->values(),
        ];
    }

    private function isKelas9(?Kelas $kelas): bool
    {
        if (!$kelas) {
            return false;
        }

        return str_starts_with((string) $kelas->kode_kelas, '9')
            || str_starts_with((string) $kelas->nama_kelas, '9');
    }

    private function whereKelas9($query)
    {
        return $query->where('kode_kelas', 'like', '9%')
            ->orWhere('nama_kelas', 'like', '9%');
    }


    public function storeSiswa(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'nis'           => 'required|string|max:20|unique:siswa,nis',
            'nama'          => 'required|string|max:255',
            'kelas_id'      => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'email'         => 'required|email|unique:users,email',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => bcrypt('password123'),
            'role'     => 'siswa_smp',
        ]);

        \App\Models\Siswa::create([
            'user_id'       => $user->id,
            'nis'           => $request->nis,
            'nama'          => $request->nama,
            'kelas_id'      => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return redirect()->route('dashboard', ['tab' => 'siswa'])
            ->with('success', 'Siswa berhasil ditambahkan. Password default: password123');
    }

    public function storeGuru(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'nip'     => 'required|string|max:50|unique:guru,nip',
            'nama'    => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'email'   => 'required|email|unique:users,email',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => bcrypt('password123'),
            'role'     => 'guru',
        ]);

        \App\Models\Guru::create([
            'user_id' => $user->id,
            'nip'     => $request->nip,
            'nama'    => $request->nama,
            'no_telp' => $request->no_telp,
        ]);

        return redirect()->route('dashboard', ['tab' => 'guru'])
            ->with('success', 'Guru berhasil ditambahkan. Password default: password123');
    }

    public function storeOrtu(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt('password123'),
            'role'     => 'orang_tua',
        ]);

        \App\Models\OrangTua::create([
            'user_id' => $user->id,
            'nama'    => $request->name,
            'no_telp' => $request->no_telp,
            'alamat'  => $request->alamat,
        ]);

        return redirect()->route('dashboard', ['tab' => 'ortu'])
            ->with('success', 'Orang tua berhasil ditambahkan. Password default: password123');
    }

    public function storeKelas(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'guru_id'    => 'nullable|exists:guru,id',
        ]);

        $namaParts  = explode(' ', trim($request->nama_kelas));
        $kodeKelas  = end($namaParts); // ambil kata terakhir sebagai kode

        \App\Models\Kelas::create([
            'nama_kelas'  => $request->nama_kelas,
            'kode_kelas'  => $kodeKelas,
            'guru_id'     => $request->guru_id ?: null,
        ]);

        return redirect()->route('dashboard', ['tab' => 'kelas'])
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateSiswa(\Illuminate\Http\Request $request, \App\Models\Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|in:L,P',
            'email' => 'required|email|max:255',
        ]);

        $siswa->update([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);
        
        $siswa->user()->update([
            'email' => $request->email,
        ]);

        return redirect()->route('dashboard', ['tab' => 'siswa'])->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function updateGuru(\Illuminate\Http\Request $request, \App\Models\Guru $guru)
    {
        $request->validate([
            'nip' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
        ]);

        $guru->update([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
        ]);

        $guru->user()->update([
            'email' => $request->email,
        ]);

        return redirect()->route('dashboard', ['tab' => 'guru'])->with('success', 'Data guru berhasil diperbarui.');
    }

    public function updateOrtu(\Illuminate\Http\Request $request, \App\Models\User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->orangTua) {
            $user->orangTua->update([
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
            ]);
        }

        return redirect()->route('dashboard', ['tab' => 'ortu'])->with('success', 'Data orang tua berhasil diperbarui.');
    }

    public function updateKelas(\Illuminate\Http\Request $request, \App\Models\Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'guru_id'    => 'nullable|exists:guru,id',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'guru_id'    => $request->guru_id,
        ]);

        return redirect()->route('dashboard', ['tab' => 'kelas'])->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function kirimPesanKinerja(Request $request)
    {
        $request->validate([
            'penerima_id' => 'required|exists:users,id',
            'tipe' => 'required|in:selamat,bimbingan'
        ]);

        $subjek = $request->tipe === 'selamat'
            ? 'Apresiasi Kinerja Mengajar'
            : 'Panggilan Program Bimbingan (Coaching)';

        $isi = $request->tipe === 'selamat'
            ? 'Selamat! Berdasarkan evaluasi sistem, Indikator Kinerja Anda (Accountability Score) sangat baik. Terima kasih atas kedisiplinan Anda dalam mengisi laporan. Pertahankan!'
            : 'Berdasarkan evaluasi sistem SAKT (Sistem Audit Kompetensi Terukur), Anda memiliki beberapa tunggakan pelaporan administratif (Jurnal Mengajar). Mohon segera melengkapi dan temui Tim Penjamin Mutu untuk pendampingan.';

        \App\Models\Pesan::create([
            'pengirim_id' => auth()->id(),
            'penerima_id' => $request->penerima_id,
            'subjek' => $subjek,
            'isi' => $isi,
            'dibaca' => false
        ]);

        return response()->json(['success' => true]);
    }

    public function kirimPesanWali(Request $request)
    {
        $request->validate([
            'wali_user_id' => 'required|exists:users,id',
            'kelas' => 'required|string',
        ]);

        \App\Models\Pesan::create([
            'pengirim_id' => $request->user()->id,
            'penerima_id' => $request->wali_user_id,
            'subjek' => 'Peringatan Kondisi Kelas ' . $request->kelas,
            'isi' => 'Rekomendasi penyesuaian jadwal tugas dan pendekatan khusus telah dikirimkan untuk kelas ' . $request->kelas . ' karena terdeteksi penurunan skor kenyamanan dan dukungan siswa (Vulnerability Index). Mohon segera ditindaklanjuti.',
            'dibaca' => false,
        ]);

        return response()->json(['success' => true]);
    }
}
