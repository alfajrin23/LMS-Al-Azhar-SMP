<div class="content-header">
    <h1>Tahfidz &amp; Setoran Ayat</h1>
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
    $tahfidzAnak = \App\Models\TahfidzSetoran::where('siswa_id', $a->id)->with('guru')->orderBy('tanggal', 'desc')->get();
    $totalSetoran = $tahfidzAnak->count();
    $rataTahfidz = $tahfidzAnak->count() > 0 ? round($tahfidzAnak->avg('nilai'), 1) : 0;
    $totalAyat = $tahfidzAnak->sum('jumlah_ayat');
    $jadwalBerikutnya = $tahfidzAnak->whereNotNull('tanggal_berikutnya')->first()?->tanggal_berikutnya;
@endphp
<div x-show="childId == {{ $a->id }}">
    @if($jadwalBerikutnya)
    <div style="background:#e6fcf5; border:1px solid #c3fae8; color:#0ca678; padding:14px 18px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-weight:600; font-size:14px">
        <i class="fas fa-calendar-alt" style="font-size:18px"></i>
        <span>Jadwal Setoran Berikutnya: <span style="font-weight:800">{{ \Carbon\Carbon::parse($jadwalBerikutnya)->isoFormat('D MMMM YYYY') }}</span></span>
    </div>
    @endif
    <div class="card" style="margin-bottom:20px">
        <div class="card-header"><h3><i class="fas fa-quran" style="color:var(--green)"></i> Progress Tahfidz — {{ $a->nama }}</h3></div>
        <div class="tahfidz-stats">
            <div class="tahfidz-stat"><span class="tahfidz-stat-number green">{{ $totalAyat }}</span><span class="tahfidz-stat-label">Total Ayat</span></div>
            <div class="tahfidz-stat"><span class="tahfidz-stat-number teal">{{ $totalSetoran }}</span><span class="tahfidz-stat-label">Total Setoran</span></div>
            <div class="tahfidz-stat"><span class="tahfidz-stat-number blue">{{ $rataTahfidz }}</span><span class="tahfidz-stat-label">Rata-rata Nilai</span></div>
            <div class="tahfidz-stat"><span class="tahfidz-stat-number orange">{{ $tahfidzAnak->where('status', 'baru')->count() }}</span><span class="tahfidz-stat-label">Setoran Baru</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-list" style="color:var(--teal)"></i> Riwayat Setoran</h3></div>
        <div class="table-wrap">
            <table class="tahfidz-table">
                <thead><tr><th>Tanggal</th><th>Surah</th><th>Ayat</th><th>Status</th><th>Nilai</th><th>Guru</th></tr></thead>
                <tbody>
                    @forelse($tahfidzAnak as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                        <td><strong>{{ $t->surah }}</strong></td>
                        <td>{{ $t->ayat_mulai }}:{{ $t->ayat_selesai }} ({{ $t->jumlah_ayat }} ayat)</td>
                        <td><span class="badge light {{ $t->status === 'baru' ? 'green' : 'blue' }}">{{ $t->status === 'baru' ? 'Setoran Baru' : 'Murojaah' }}</span></td>
                        <td style="font-weight:700">{{ $t->nilai ?? '-' }}</td>
                        <td>{{ $t->guru->nama ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--gray-400)">Belum ada setoran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
