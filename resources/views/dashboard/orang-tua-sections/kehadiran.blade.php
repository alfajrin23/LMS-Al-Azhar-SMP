<div class="content-header">
    <h1>Kehadiran</h1>
    <div class="header-right">
        <select x-model="childId" class="child-select">
            @foreach($anak as $a)
            <option value="{{ $a->id }}">{{ $a->nama }} &mdash; {{ $a->kelas->nama_kelas ?? 'N/A' }}</option>
            @endforeach
        </select>
        <div class="avatar orange">{{ strtoupper(substr($ortu?->nama ?? $user->name, 0, 2)) }}</div>
    </div>
</div>

@foreach($anak as $a)
@php
    $kehadiranAnak = \App\Models\Kehadiran::where('siswa_id', $a->id)->orderBy('tanggal', 'desc')->get();
    $totalHadir = $kehadiranAnak->where('status', 'hadir')->count();
    $totalIzin = $kehadiranAnak->where('status', 'izin')->count();
    $totalSakit = $kehadiranAnak->where('status', 'sakit')->count();
    $totalAlpha = $kehadiranAnak->where('status', 'alpha')->count();
    $totalKehadiran = $kehadiranAnak->count();
    $pctHadir = $totalKehadiran > 0 ? round($totalHadir / $totalKehadiran * 100) : 0;
@endphp
<div x-show="childId == {{ $a->id }}">
    <div class="grid-2" style="margin-bottom:20px">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-clipboard-check" style="color:var(--teal)"></i> Rekap Kehadiran {{ $a->nama }}</h3></div>
            <div class="att-grid">
                <div class="att-item hadir"><div class="att-number">{{ $totalHadir }}</div><div class="att-label">Hadir</div></div>
                <div class="att-item izin"><div class="att-number">{{ $totalIzin }}</div><div class="att-label">Izin</div></div>
                <div class="att-item sakit"><div class="att-number">{{ $totalSakit }}</div><div class="att-label">Sakit</div></div>
                <div class="att-item alpha"><div class="att-number">{{ $totalAlpha }}</div><div class="att-label">Alpha</div></div>
            </div>
            <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light)">
                <p style="font-size:13px"><strong>Persentase Kehadiran:</strong> <span style="color:{{ $pctHadir >= 90 ? 'var(--green)' : 'var(--orange)' }};font-weight:700">{{ $pctHadir }}%</span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-list" style="color:var(--blue)"></i> Riwayat Kehadiran</h3></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Tanggal</th><th>Status</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @forelse($kehadiranAnak->take(30) as $k)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($k->tanggal)->format('d M Y') }}</td>
                            <td><span class="badge light {{ $k->status === 'hadir' ? 'green' : ($k->status === 'izin' ? 'orange' : ($k->status === 'sakit' ? 'red' : 'gray')) }}">{{ ucfirst($k->status) }}</span></td>
                            <td style="font-size:12px;color:var(--gray-400)">{{ $k->keterangan ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--gray-400)">Belum ada data kehadiran</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach
