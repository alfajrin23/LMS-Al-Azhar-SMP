@php
if (!isset($selectedKelas)) {
    $selectedKelas = $kelasYangDiajar->first()->id ?? null;
}
$detailKelas = \App\Models\Kelas::with('siswa')->find($selectedKelas);
$siswaList = $detailKelas?->siswa ?? collect();
$rataKelas = round(
    \App\Models\Nilai::whereIn('siswa_id', $siswaList->pluck('id'))
        ->whereIn('mapel_id', $guru->mapels->pluck('id'))
        ->avg('nilai') ?? 0,
    1,
);
$maxKelas =
    \App\Models\Nilai::whereIn('siswa_id', $siswaList->pluck('id'))
        ->whereIn('mapel_id', $guru->mapels->pluck('id'))
        ->max('nilai') ?? 0;
$minKelas =
    \App\Models\Nilai::whereIn('siswa_id', $siswaList->pluck('id'))
        ->whereIn('mapel_id', $guru->mapels->pluck('id'))
        ->min('nilai') ?? 0;
$lulusKelas =
    $siswaList->count() > 0
    ? round(
        (\App\Models\Nilai::whereIn('siswa_id', $siswaList->pluck('id'))
            ->whereIn('mapel_id', $guru->mapels->pluck('id'))
            ->where('nilai', '>=', 70)
            ->count() /
            max($siswaList->count(), 1)) *
        100,
    )
    : 0;
$tugasKelas = \App\Models\Tugas::whereIn('kelas_id', [$selectedKelas])
    ->where('guru_id', $guru->id)
    ->orderBy('tanggal_deadline')
    ->get();
@endphp
<div class="content-header">
    <div>
        <h1>Detail Kelas {{ $detailKelas->nama_kelas ?? '' }}</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">{{ $guru->mapels->pluck('nama_mapel')->implode(', ') }} —
            {{ $guru->nama }}</p>
    </div>
    <div class="header-right">
        <label @click="tab='kelas'" class="header-btn outline" style="cursor:pointer"><i class="fas fa-arrow-left"></i>
            Kembali</label>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3><i class="fas fa-users" style="color:var(--teal)"></i> Daftar Siswa <span
                style="color:var(--gray-400);font-weight:400">({{ $siswaList->count() }} siswa)</span></h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>L/P</th>
                    <th>Nilai {{ $guru->mapels->pluck('kode')->implode(', ') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswaList as $i => $s)
                                    @php
                    $nilaiSiswa = \App\Models\Nilai::where('siswa_id', $s->id)
                        ->whereIn('mapel_id', $guru->mapels->pluck('id'))
                        ->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $s->nis }}</td>
                                        <td><strong>{{ $s->nama }}</strong></td>
                                        <td>{{ $s->jenis_kelamin }}</td>
                                        <td><strong>{{ $nilaiSiswa?->nilai ?? '-' }}</strong>
                                            @if ($nilaiSiswa?->nilai_bahasa)
                                                <span style="font-size:11px;color:var(--indigo);font-weight:normal">(Eng:
                                                    {{ $nilaiSiswa->nilai_bahasa }})</span>
                                            @endif
                                        </td>
                                        <td><label @click="
                                            tab = 'siswa-detail';
                                            detailSiswaHtml = '<div style=\'padding:60px 20px; text-align:center; color:var(--gray-400)\'><i class=\'fas fa-circle-notch fa-spin\'></i> Mengambil data siswa...</div>';
                                            fetch('/guru/kelas/siswa/{{ $s->id }}/detail')
                                                .then(response => response.text())
                                                .then(html => {
                                                    detailSiswaHtml = html;
                                                })
                                                .catch(err => {
                                                    detailSiswaHtml = '<div style=\'padding:20px; color:red; text-align:center;\'>Gagal memuat data.</div>';
                                                });
                                        " class="btn-small outline" style="cursor:pointer">
                                                Detail
                                            </label>
                                        </td>
                                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--gray-400)">Belum ada siswa di kelas ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Statistik Kelas</h3>
        </div>
        <div class="h-bar-group">
            <div class="h-bar-row">
                <div class="h-bar-label">Rata-rata</div>
                <div class="h-bar-track">
                    <div class="h-bar-fill teal" style="width:{{ $rataKelas }}%">{{ $rataKelas }}</div>
                </div>
            </div>
            <div class="h-bar-row">
                <div class="h-bar-label">Tertinggi</div>
                <div class="h-bar-track">
                    <div class="h-bar-fill blue" style="width:{{ $maxKelas }}%">{{ $maxKelas }}</div>
                </div>
            </div>
            <div class="h-bar-row">
                <div class="h-bar-label">Terendah</div>
                <div class="h-bar-track">
                    <div class="h-bar-fill orange" style="width:{{ max($minKelas, 5) }}%">{{ $minKelas }}</div>
                </div>
            </div>
            <div class="h-bar-row">
                <div class="h-bar-label">Lulus</div>
                <div class="h-bar-track">
                    <div class="h-bar-fill green" style="width:{{ $lulusKelas }}%">{{ $lulusKelas }}%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-tasks" style="color:var(--orange)"></i> Tugas Terbaru</h3>
            <label @click="tab='tugas'"
                style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer">Semua</label>
        </div>
        @forelse($tugasKelas as $t)
            @php
    $d = \Carbon\Carbon::parse($t->tanggal_deadline);
    $badge = $d->isPast() ? 'status-terlambat' : ($d->diffInDays(now()) <= 3 ? 'status-mendekati' : 'teal');
    $label = $d->isPast() ? 'Terlambat' : ($d->diffInDays(now()) <= 3 ? 'Mendekati' : 'Aktif');
            @endphp
            <div class="task-item">
                <i class="fas fa-file-alt"
                    style="color:var(--{{ ['blue', 'teal', 'orange', 'purple'][$loop->index % 4] }});font-size:18px"></i>
                <div class="task-info">
                    <div class="task-title">{{ $t->judul }}</div>
                    <div class="task-meta">Deadline: {{ $d->format('d M Y') }}</div>
                </div>
                <span class="badge {{ $badge }}">{{ $label }}</span>
            </div>
        @empty
            <div style="padding:20px;text-align:center;color:var(--gray-400)">Belum ada tugas</div>
        @endforelse
    </div>
</div>
