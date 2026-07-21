@php
    $totalSetoran = $tahfidzSetoran->count();
    $avgNilai = $tahfidzSetoran->avg('nilai');
    $totalAyat = $tahfidzSetoran->sum('jumlah_ayat');
    $surahTerakhir = $tahfidzSetoran->first()?->surah ?? '-';
    $surahBaru = $tahfidzSetoran->where('status', 'baru')->count();
    $setoranBulanIni = $tahfidzSetoran->where('tanggal', '>=', now()->startOfMonth())->count();

    // Akurasi perhitungan Juz & Persentase Al-Qur'an
    $totalAyatBaru = $tahfidzSetoran->where('status', 'baru')->sum('jumlah_ayat');
    $juzDihafal = min(30, floor($totalAyatBaru / 208));
    $juzSaatIni = min(30, $juzDihafal + 1);
    $percentHafalan = min(100, round(($totalAyatBaru / 6236) * 100, 1));
    $progressKepala = $tahfidzProgress ?? null;
    if ($progressKepala) {
        $totalAyatBaru = $progressKepala->total_ayat ?? $totalAyatBaru;
        $juzDihafal = min(30, (int) $progressKepala->juz_dihafal);
        $juzSaatIni = min(30, $juzDihafal + 1);
        $percentHafalan = min(100, round((float) $progressKepala->progress_percent, 1));
        $surahTerakhir = $progressKepala->surah ?: $surahTerakhir;
    }

    $grades = ['grade-A', 'grade-B', 'grade-C', 'grade-D', 'grade-E'];
    $gradeColor = function($v) {
        if ($v >= 90) return 'grade-A';
        if ($v >= 80) return 'grade-B';
        if ($v >= 70) return 'grade-C';
        return 'grade-D';
    };
    $gradeLetter = function($v) {
        if ($v >= 90) return 'A';
        if ($v >= 80) return 'B';
        if ($v >= 70) return 'C';
        return 'D';
    };
    $statusBadge = function($s) {
        if ($s === 'baru') return '<span class="badge blue light">Baru</span>';
        return '<span class="badge orange">Murojaah</span>';
    };

    $minggu = [];
    for ($i = 4; $i >= 1; $i--) {
        $start = now()->subWeeks($i)->startOfWeek();
        $end = now()->subWeeks($i)->endOfWeek();
        $total = $tahfidzSetoran->filter(fn($t) =>
            \Carbon\Carbon::parse($t->tanggal)->between($start, $end)
        )->sum('jumlah_ayat');
        $minggu[] = ['label' => 'Minggu ' . (4 - $i + 1), 'ayat' => $total];
    }
    $maxAyat = max(array_column($minggu, 'ayat')) ?: 1;
    $barColors = ['blue', 'red', 'orange', 'orange'];
@endphp
<style>
    .juz-box {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        transition: all 0.2s ease;
    }
    .juz-done {
        background-color: #20c997;
        color: white;
        box-shadow: 0 2px 4px rgba(32, 201, 151, 0.2);
    }
    .juz-current {
        background-color: #ff922b;
        color: white;
        animation: pulse-juz 2s infinite;
        box-shadow: 0 0 8px rgba(255, 146, 43, 0.5);
    }
    .juz-undone {
        background-color: #f1f3f5;
        color: #adb5bd;
        border: 1px dashed #dee2e6;
    }
    @keyframes pulse-juz {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.85;
            box-shadow: 0 0 12px rgba(255, 146, 43, 0.7);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
<div class="content-header">
    <h1>Tahfidz &amp; Setoran Ayat</h1>
    <div class="header-right">
        <div class="avatar red">{{ strtoupper(substr($siswa->nama, 0, 1) . (str_contains($siswa->nama, ' ') ? substr(explode(' ', $siswa->nama)[1], 0, 1) : '')) }}</div>
        <span style="font-weight:600;font-size:14px">{{ explode(' ', $siswa->nama)[0] }}</span>
    </div>
</div>

@php
    $jadwalBerikutnya = $tahfidzSetoran->whereNotNull('tanggal_berikutnya')->first()?->tanggal_berikutnya;
@endphp

@if($jadwalBerikutnya)
<div style="background:#e6fcf5; border:1px solid #c3fae8; color:#0ca678; padding:14px 18px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-weight:600; font-size:14px">
    <i class="fas fa-calendar-alt" style="font-size:18px"></i>
    <span>Jadwal Setoran Berikutnya: <span style="font-weight:800">{{ \Carbon\Carbon::parse($jadwalBerikutnya)->isoFormat('D MMMM YYYY') }}</span></span>
</div>
@endif

<div class="card" style="margin-bottom:20px">
    <div class="card-header"><h3><i class="fas fa-quran" style="color:var(--blue)"></i> Progress Tahfidz {{ $kelas?->nama_kelas }}</h3></div>
    <div class="tahfidz-stats" style="margin-bottom: 20px;">
        <div class="tahfidz-stat"><span class="tahfidz-stat-number blue">{{ $juzDihafal }}</span><span class="tahfidz-stat-label">Juz Dihafal</span></div>
        <div class="tahfidz-stat"><span class="tahfidz-stat-number red">{{ $setoranBulanIni }}</span><span class="tahfidz-stat-label">Setoran Bulan Ini</span></div>
        <div class="tahfidz-stat"><span class="tahfidz-stat-number orange">{{ number_format($avgNilai ?? 0, 0) }}</span><span class="tahfidz-stat-label">Rata-rata Nilai</span></div>
        <div class="tahfidz-stat"><span class="tahfidz-stat-number orange">{{ $surahBaru }}</span><span class="tahfidz-stat-label">Surah Baru</span></div>
    </div>

    <!-- Progress Bar Section -->
    <div style="margin: 20px 0 15px 0; padding-top: 15px; border-top: 1px solid var(--border-light)">
        <div style="display:flex; justify-content:space-between; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: var(--gray-500)">
            <span>Progress Hafalan Al-Qur'an: <strong>{{ $percentHafalan }}%</strong>
                @if($progressKepala)
                    <span class="badge light blue" style="margin-left:6px">Update Kepala Sekolah</span>
                @endif
            </span>
            <span style="color: var(--red)">{{ $juzDihafal }} / 30 Juz</span>
        </div>
        <div style="height: 12px; background: #e9ecef; border-radius: 6px; overflow: hidden; display: flex;">
            <div style="width: {{ $percentHafalan }}%; background: linear-gradient(90deg, #20c997, #0ca678); border-radius: 6px; transition: width 0.8s ease-in-out"></div>
        </div>
    </div>

    <!-- 30 Juz Grid Section -->
    <div style="margin-top: 20px;">
        <h4 style="font-size:13px; color:var(--gray-500); margin-bottom:12px; font-weight:600"><i class="fas fa-th" style="margin-right:6px"></i> Peta Hafalan 30 Juz</h4>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(36px, 1fr)); gap: 8px;">
            @for($j = 1; $j <= 30; $j++)
                @php
                    $statusClass = 'juz-undone';
                    if ($j <= $juzDihafal) {
                        $statusClass = 'juz-done';
                    } elseif ($j == $juzSaatIni) {
                        $statusClass = 'juz-current';
                    }
                @endphp
                <div class="juz-box {{ $statusClass }}" title="Juz {{ $j }}">
                    {{ $j }}
                </div>
            @endfor
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-header"><h3><i class="fas fa-list" style="color:var(--red)"></i> Riwayat Setoran Ayat</h3></div>
    <div class="table-wrap">
        <table class="tahfidz-table">
            <thead><tr><th>Tanggal</th><th>Surah</th><th>Ayat</th><th>Jenis</th><th>Nilai</th><th>Grade</th><th>Catatan Guru</th></tr></thead>
            <tbody>
                @forelse($tahfidzSetoran as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->isoFormat('D MMM YYYY') }}</td>
                        <td><strong>{{ $t->surah }}</strong></td>
                        <td>{{ $t->ayat_mulai }}:{{ $t->ayat_selesai }} ({{ $t->jumlah_ayat }} ayat)</td>
                        <td>{!! $statusBadge($t->status) !!}</td>
                        <td style="font-weight:700">{{ $t->nilai }}</td>
                        <td><span class="{{ $gradeColor($t->nilai) }}">{{ $gradeLetter($t->nilai) }}</span></td>
                        <td style="color:var(--gray-400);font-size:13px">{{ $t->catatan_guru ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada setoran tahfidz</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3><i class="fas fa-chart-bar" style="color:var(--orange)"></i> Grafik Perkembangan Tahfidz (4 Minggu Terakhir)</h3></div>
    <div class="h-bar-group">
        @foreach($minggu as $i => $m)
            @php $pct = max(round(($m['ayat'] / $maxAyat) * 80), 5); @endphp
            <div class="h-bar-row">
                <div class="h-bar-label">{{ $m['label'] }}</div>
                <div class="h-bar-track">
                    <div class="h-bar-fill {{ $barColors[$i] }}" style="width:{{ $pct }}%">{{ $m['ayat'] }} ayat</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
