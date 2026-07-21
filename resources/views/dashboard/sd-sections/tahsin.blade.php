@php
    $tahsinSetoran = $tahsinSetoran ?? collect();
    $totalSetoran = $tahsinSetoran->count();
    $avgNilai = round($tahsinSetoran->avg('nilai') ?? 0);
    $setoranBulanIni = $tahsinSetoran->where('tanggal', '>=', now()->startOfMonth())->count();
    $materiTerakhir = $tahsinSetoran->first()?->materi_tahsin ?? '-';
    $tuntas = $tahsinSetoran->where('status', 'tuntas')->count();
    $perluPerbaikan = $tahsinSetoran->where('status', 'perlu_perbaikan')->count();
    $progress = $totalSetoran > 0 ? min(100, round(($tuntas / $totalSetoran) * 100)) : 0;
    $statusBadge = function ($status) {
        return match ($status) {
            'tuntas' => '<span class="badge green">Tuntas</span>',
            'perlu_perbaikan' => '<span class="badge orange">Perlu Perbaikan</span>',
            default => '<span class="badge blue light">Proses</span>',
        };
    };
@endphp

<div class="content-header">
    <h1>Tahsin <span>SDIT {{ setting('school_name') }}</span></h1>
    <div class="header-right">
        <div class="avatar red">{{ strtoupper(substr($siswa->nama, 0, 1) . (str_contains($siswa->nama, ' ') ? substr(explode(' ', $siswa->nama)[1], 0, 1) : '')) }}</div>
        <span style="font-weight:600;font-size:14px">{{ explode(' ', $siswa->nama)[0] }}</span>
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-header"><h3><i class="fas fa-book-open" style="color:var(--blue)"></i> Progress Tahsin {{ $kelas?->nama_kelas }}</h3></div>
    <div class="tahfidz-stats" style="margin-bottom:20px">
        <div class="tahfidz-stat"><span class="tahfidz-stat-number blue">{{ $totalSetoran }}</span><span class="tahfidz-stat-label">Total Pertemuan</span></div>
        <div class="tahfidz-stat"><span class="tahfidz-stat-number red">{{ $setoranBulanIni }}</span><span class="tahfidz-stat-label">Bulan Ini</span></div>
        <div class="tahfidz-stat"><span class="tahfidz-stat-number orange">{{ $avgNilai }}</span><span class="tahfidz-stat-label">Rata-rata Nilai</span></div>
        <div class="tahfidz-stat"><span class="tahfidz-stat-number orange">{{ $perluPerbaikan }}</span><span class="tahfidz-stat-label">Perlu Perbaikan</span></div>
    </div>

    <div style="margin:20px 0 4px 0; padding-top:15px; border-top:1px solid var(--border-light)">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px; font-weight:600; color:var(--gray-500)">
            <span>Progress Ketuntasan: <strong>{{ $progress }}%</strong></span>
            <span style="color:var(--red)">{{ $tuntas }} / {{ $totalSetoran }} Pertemuan</span>
        </div>
        <div style="height:12px; background:#e9ecef; border-radius:6px; overflow:hidden">
            <div style="width:{{ $progress }}%; background:linear-gradient(90deg, #20c997, #0ca678); border-radius:6px; height:12px; transition:width 0.8s ease-in-out"></div>
        </div>
    </div>

    <div style="margin-top:14px; font-size:13px; color:var(--gray-500)">
        Materi terakhir: <strong style="color:var(--text)">{{ $materiTerakhir }}</strong>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3><i class="fas fa-list" style="color:var(--red)"></i> Riwayat Tahsin</h3></div>
    <div class="table-wrap">
        <table class="tahfidz-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Materi</th>
                    <th>Jilid/Halaman</th>
                    <th>Guru</th>
                    <th>Nilai</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tahsinSetoran as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->isoFormat('D MMM YYYY') }}</td>
                        <td><strong>{{ $t->materi_tahsin }}</strong></td>
                        <td>{{ $t->jilid_halaman ?? '-' }}</td>
                        <td>{{ $t->guru?->nama ?? '-' }}</td>
                        <td style="font-weight:700">{{ $t->nilai ?? '-' }}</td>
                        <td>{!! $statusBadge($t->status) !!}</td>
                        <td style="color:var(--gray-400);font-size:13px">{{ $t->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada data tahsin</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
