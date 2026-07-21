@php
    $semuaSiswa = \App\Models\Siswa::with('kelas')->get();
    $riwayatAbsensi = \App\Models\Kehadiran::with('siswa', 'siswa.kelas')->orderBy('tanggal', 'desc')->take(50)->get();
    $statusBadge = fn($s) => match($s) {
        'hadir' => '<span class="badge light green">Hadir</span>',
        'sakit' => '<span class="badge light orange">Sakit</span>',
        'izin' => '<span class="badge light blue">Izin</span>',
        'alpha' => '<span class="badge light red">Alpha</span>',
        default => '<span class="badge">-</span>',
    };
@endphp
<div class="content-header">
    <h1>Absensi / Kehadiran</h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-plus" style="color:var(--teal)"></i> Input Kehadiran</h3></div>
        <form method="POST" action="/guru/absensi" style="padding:4px 0">
            @csrf
            <div class="form-group" style="margin-bottom:14px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Siswa</label>
                <select name="siswa_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                    <option value="">Pilih Siswa</option>
                    @foreach($semuaSiswa as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:14px">
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ now()->format('Y-m-d') }}" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
                </div>
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Status</label>
                    <select name="status" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <option value="hadir">Hadir</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="alpha">Alpha</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Keterangan (opsional)</label>
                <input type="text" name="keterangan" placeholder="Contoh: Izin acara keluarga" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
            </div>
            <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-save"></i> Simpan</button>
        </form>
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Statistik Hari Ini</h3></div>
        @php
            $hariIni = now()->format('Y-m-d');
            $absensiHariIni = \App\Models\Kehadiran::where('tanggal', $hariIni)->get();
        @endphp
        <div style="padding:4px 0">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Total Absensi</span><div style="font-size:22px;font-weight:700;color:var(--teal)">{{ $absensiHariIni->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Hadir</span><div style="font-size:22px;font-weight:700;color:var(--green)">{{ $absensiHariIni->where('status', 'hadir')->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Sakit</span><div style="font-size:22px;font-weight:700;color:var(--orange)">{{ $absensiHariIni->where('status', 'sakit')->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Izin</span><div style="font-size:22px;font-weight:700;color:var(--blue)">{{ $absensiHariIni->where('status', 'izin')->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Alpha</span><div style="font-size:22px;font-weight:700;color:var(--red)">{{ $absensiHariIni->where('status', 'alpha')->count() }}</div></div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h3><i class="fas fa-list" style="color:var(--blue)"></i> Riwayat Kehadiran (50 terakhir)</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Keterangan</th></tr></thead>
            <tbody>
                @forelse($riwayatAbsensi as $a)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                    <td><strong>{{ $a->siswa->nama ?? '-' }}</strong></td>
                    <td>{{ $a->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{!! $statusBadge($a->status) !!}</td>
                    <td style="color:var(--gray-400);font-size:13px">{{ $a->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada data kehadiran</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if(session('success'))
    <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
