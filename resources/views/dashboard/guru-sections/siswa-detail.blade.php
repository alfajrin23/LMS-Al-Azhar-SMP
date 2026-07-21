@php
    $detailSiswa = \App\Models\Siswa::with('kelas')->find($selectedSiswa?->id);
    $nilaiSiswa = \App\Models\Nilai::where('siswa_id', $detailSiswa?->id)->with('mapel')->get();
    $tahfidzSiswa = \App\Models\TahfidzSetoran::where('siswa_id', $detailSiswa?->id)
        ->with('guru')
        ->orderBy('tanggal', 'desc')
        ->get();
    $kehadiranSiswa = \App\Models\Kehadiran::where('siswa_id', $detailSiswa?->id)
        ->orderBy('tanggal', 'desc')
        ->take(30)
        ->get();
    $catatanSiswa = \App\Models\CatatanWali::where('siswa_id', $detailSiswa?->id)->latest()->first();
    $badgeSiswa = \App\Models\Badge::with(['siswa' => fn($q) => $q->where('siswa_id', $detailSiswa?->id)])
        ->get()
        ->filter(fn($b) => $b->siswa->isNotEmpty());
    $gradeColor = fn($v) => $v >= 90 ? 'grade-A' : ($v >= 80 ? 'grade-B' : 'grade-C');
    $gradeLetter = fn($v) => $v >= 90
        ? 'A'
        : ($v >= 85
            ? 'A-'
            : ($v >= 80
                ? 'B+'
                : ($v >= 75
                    ? 'B'
                    : ($v >= 70
                        ? 'B-'
                        : 'C'))));
    $rataNilai = round($nilaiSiswa->avg('nilai'), 1);
    $totalHadir = $kehadiranSiswa->where('status', 'hadir')->count();
    $rataTahfidz = round($tahfidzSiswa->avg('nilai') ?? 0);
@endphp
<div class="content-header">
    <div>
        <h1>Detail Siswa</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">{{ $detailSiswa->nama ?? '' }} —
            {{ $detailSiswa->kelas->nama_kelas ?? '' }}</p>
    </div>
    <div class="header-right">
        <label @click="tab='kelas'" class="header-btn outline" style="cursor:pointer"><i class="fas fa-arrow-left"></i>
            Kembali</label>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3><i class="fas fa-user-graduate" style="color:var(--teal)"></i> Biodata Siswa</h3>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;padding:4px 0">
        <div><span style="font-size:12px;color:var(--gray-400)">Nama</span>
            <div style="font-weight:600">{{ $detailSiswa->nama ?? '-' }}</div>
        </div>
        <div><span style="font-size:12px;color:var(--gray-400)">NIS</span>
            <div style="font-weight:600">{{ $detailSiswa->nis ?? '-' }}</div>
        </div>
        <div><span style="font-size:12px;color:var(--gray-400)">Kelas</span>
            <div style="font-weight:600">{{ $detailSiswa->kelas->nama_kelas ?? '-' }}</div>
        </div>
        <div><span style="font-size:12px;color:var(--gray-400)">Jenis Kelamin</span>
            <div style="font-weight:600">{{ $detailSiswa->jenis_kelamin ?? '-' }}</div>
        </div>
        <div><span style="font-size:12px;color:var(--gray-400)">Rata-rata Nilai</span>
            <div style="font-weight:700;color:var(--teal)">{{ $rataNilai }}</div>
        </div>
        <div><span style="font-size:12px;color:var(--gray-400)">Status</span>
            <div style="font-weight:600;color:var(--green)">{{ $rataNilai >= 75 ? 'LULUS' : 'TIDAK LULUS' }}</div>
        </div>
    </div>
</div>
<div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px">
    <div class="card" style="flex:2;min-width:300px">
        <div class="card-header">
            <h3><i class="fas fa-chart-line" style="color:var(--blue)"></i> Nilai per Mata Pelajaran</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mapel</th>
                        <th>Nilai</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilaiSiswa as $n)
                        <tr>
                            <td>{{ $n->mapel->nama_mapel }}</td>
                            <td style="font-weight:700">{{ $n->nilai }}@if ($n->nilai_bahasa)
                                    <span style="font-weight:normal;font-size:11px;color:var(--indigo)">(Eng:
                                        {{ $n->nilai_bahasa }})</span>
                                @endif
                            </td>
                            <td><span class="{{ $gradeColor($n->nilai) }}">{{ $gradeLetter($n->nilai) }}</span></td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;color:var(--gray-400)">Belum ada nilai</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($nilaiSiswa->count() > 0)
                <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border-light)">
                    <h4 style="font-size:13px;font-weight:600;margin-bottom:10px">Grafik Nilai</h4>
                    <div class="h-bar-group">
                        @php
                            $maxNilai = max($nilaiSiswa->max('nilai'), 1);
                            $barColors = ['green', 'teal', 'blue', 'orange', 'purple', 'pink', 'cyan'];
                        @endphp
                        @foreach ($nilaiSiswa as $n)
                            <div class="h-bar-row">
                                <div class="h-bar-label" style="font-size:11px">{{ $n->mapel->kode }}</div>
                                <div class="h-bar-track">
                                    <div class="h-bar-fill {{ $barColors[$loop->index % count($barColors)] }}"
                                        style="width:{{ round(($n->nilai / 100) * 80) }}%">{{ $n->nilai }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="card" style="flex:1;min-width:200px">
            <div class="card-header">
                <h3><i class="fas fa-quran" style="color:var(--green)"></i> Tahfidz</h3>
            </div>
            <div style="padding:4px 0">
                <div><span style="font-size:12px;color:var(--gray-400)">Total Ayat</span>
                    <div style="font-weight:700">{{ $tahfidzSiswa->sum('jumlah_ayat') }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400);margin-top:8px;display:block">Rata-rata Nilai
                        Tahfidz</span>
                    <div style="font-weight:700;color:var(--teal)">{{ $rataTahfidz }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400);margin-top:8px;display:block">Total Setoran</span>
                    <div style="font-weight:700">{{ $tahfidzSiswa->count() }}</div>
                </div>
            </div>
        </div>
        <div class="card" style="flex:1;min-width:200px">
            <div class="card-header">
                <h3><i class="fas fa-clipboard-check" style="color:var(--orange)"></i> Kehadiran (30 hari)</h3>
            </div>
            <div style="padding:4px 0">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div><span style="font-size:12px;color:var(--gray-400)">Hadir</span>
                        <div style="font-weight:700;color:var(--green)">{{ $totalHadir }}</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Sakit</span>
                        <div style="font-weight:700;color:var(--orange)">
                            {{ $kehadiranSiswa->where('status', 'sakit')->count() }}</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Izin</span>
                        <div style="font-weight:700;color:var(--blue)">
                            {{ $kehadiranSiswa->where('status', 'izin')->count() }}</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Alpha</span>
                        <div style="font-weight:700;color:var(--red)">
                            {{ $kehadiranSiswa->where('status', 'alpha')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px">
        <div class="card" style="flex:1;min-width:300px">
            <div class="card-header">
                <h3><i class="fas fa-star" style="color:var(--orange)"></i> Catatan Wali Kelas</h3>
            </div>
            @if ($catatanSiswa)
                <p style="font-size:14px;color:var(--gray-500);line-height:1.7;font-style:italic">
                    &quot;{{ $catatanSiswa->catatan }}&quot;</p>
                <p style="font-size:12px;color:var(--gray-400);margin-top:8px">—
                    {{ $catatanSiswa->guru->nama ?? 'Wali Kelas' }}, {{ $catatanSiswa->semester }}</p>
            @else
                <p style="font-size:14px;color:var(--gray-400);font-style:italic">Belum ada catatan wali kelas.</p>
            @endif
        </div>
        <div class="card" style="flex:1;min-width:200px">
            <div class="card-header">
                <h3><i class="fas fa-trophy" style="color:var(--teal)"></i> Prestasi &amp; Badge</h3>
            </div>
            @forelse($badgeSiswa as $b)
                <div style="display:flex;align-items:center;gap:10px;padding:6px 0">
                    <span style="font-size:24px">{{ $b->icon }}</span>
                    <div>
                        <div style="font-weight:600;font-size:13px">{{ $b->nama }}</div>
                        <div style="font-size:11px;color:var(--gray-400)">{{ $b->deskripsi }}</div>
                    </div>
                </div>
            @empty
                <p style="font-size:14px;color:var(--gray-400);font-style:italic">Belum ada badge.</p>
            @endforelse
        </div>
    </div>
    @if ($tahfidzSiswa->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list" style="color:var(--blue)"></i> Riwayat Setoran Tahfidz</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Surah</th>
                            <th>Ayat</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th>Guru</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tahfidzSiswa as $t)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                                <td><strong>{{ $t->surah }}</strong></td>
                                <td>{{ $t->ayat_mulai }}:{{ $t->ayat_selesai }} ({{ $t->jumlah_ayat }} ayat)</td>
                                <td><span
                                        class="badge {{ $t->status === 'baru' ? 'green light' : 'blue' }}">{{ $t->status === 'baru' ? 'Baru' : 'Murojaah' }}</span>
                                </td>
                                <td style="font-weight:700">{{ $t->nilai ?? '-' }}</td>
                                <td>{{ $t->guru->nama ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
