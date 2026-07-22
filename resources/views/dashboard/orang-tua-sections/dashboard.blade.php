@php
    $gradeLetter = fn($v) => $v >= 90 ? 'A' : ($v >= 85 ? 'A-' : ($v >= 80 ? 'B+' : ($v >= 75 ? 'B' : ($v >= 70 ? 'B-' : 'C'))));
    $gradeColor = fn($v) => $v >= 90 ? 'grade-A' : ($v >= 80 ? 'grade-B' : 'grade-C');
    $statusClass = fn($d) => \Carbon\Carbon::parse($d)->isPast() ? 'status-selesai' : (\Carbon\Carbon::parse($d)->diffInDays(now()) <= 3 ? 'status-mendekati' : 'teal');
    $statusLabel = fn($d) => \Carbon\Carbon::parse($d)->isPast() ? 'Selesai' : (\Carbon\Carbon::parse($d)->diffInDays(now()) <= 3 ? 'Mendekati' : 'Aktif');
@endphp
<div class="content-header">
    <div>
        <h1>Dashboard Orang Tua</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Pantau perkembangan putra/putri Anda</p>
    </div>
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
    $nilaiAnak = \App\Models\Nilai::where('siswa_id', $a->id)->with('mapel')->get();
    $rataNilai = $nilaiAnak->count() > 0 ? round($nilaiAnak->avg('nilai'), 1) : 0;
    $kehadiranAnak = \App\Models\Kehadiran::where('siswa_id', $a->id)->get();
    $tugasAnak = \App\Models\Tugas::where('kelas_id', $a->kelas_id)->where('tipe', 'tugas')->orderBy('tanggal_deadline')->with('mapel')->get();
    $tahfidzAnak = \App\Models\TahfidzSetoran::where('siswa_id', $a->id)->get();
    $totalSetoran = $tahfidzAnak->count();
    $rataTahfidz = round($tahfidzAnak->avg('nilai') ?? 0, 1);
    $waliKelas = \App\Models\Guru::whereIn('id', \App\Models\Jadwal::where('kelas_id', $a->kelas_id)->pluck('guru_id'))->first();
@endphp
<div x-show="childId == {{ $a->id }}">
    <div class="parent-banner">
        <div class="pb-avatar" style="background:{{ ['var(--teal)','var(--blue)','var(--purple)','var(--orange)','var(--pink)'][$loop->index % 5] }}">{{ strtoupper(substr($a->nama, 0, 2)) }}</div>
        <div class="pb-info">
            <h2>{{ $a->nama }}</h2>
            <p>Kelas {{ $a->kelas->nama_kelas ?? 'N/A' }} &mdash; SMPIT {{ setting('school_name') }}</p>
            <div class="pb-meta">
                <span><i class="fas fa-user-tie"></i> Wali Kelas: {{ $waliKelas->nama ?? '-' }}</span>
                <span><i class="fas fa-id-card"></i> NIS: {{ $a->nis }}</span>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px">
        <div class="att-item hadir" style="flex:1;min-width:100px"><div class="att-number">{{ $kehadiranAnak->where('status', 'hadir')->count() }}</div><div class="att-label">&#x2705; Hadir</div></div>
        <div class="att-item izin" style="flex:1;min-width:100px"><div class="att-number">{{ $kehadiranAnak->where('status', 'izin')->count() }}</div><div class="att-label">&#x26A0;&#xFE0F; Izin</div></div>
        <div class="att-item sakit" style="flex:1;min-width:100px"><div class="att-number">{{ $kehadiranAnak->where('status', 'sakit')->count() }}</div><div class="att-label">&#x274C; Sakit</div></div>
        <div class="att-item alpha" style="flex:1;min-width:100px"><div class="att-number">{{ $kehadiranAnak->where('status', 'alpha')->count() }}</div><div class="att-label">&#x274C; Alpha</div></div>
    </div>
    <div class="grid-2" style="margin-bottom:20px">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-file-invoice" style="color:var(--blue)"></i> Rapor Singkat</h3>
                <label @click="tab='nilai'" style="cursor:pointer;font-size:12px;color:var(--blue);font-weight:600">Lihat Semua</label>
            </div>
            <div class="table-wrap">
                <table class="rapor-table">
                    <thead><tr><th>Mata Pelajaran</th><th>Nilai</th><th>Grade</th></tr></thead>
                    <tbody>
                        @forelse($nilaiAnak as $n)
                        <tr><td>{{ $n->mapel->nama_mapel }}</td><td style="font-weight:700">{{ $n->nilai }}</td><td><span class="{{ $gradeColor($n->nilai) }}">{{ $gradeLetter($n->nilai) }}</span></td></tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--gray-400)">Belum ada nilai</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--border-light)">
                <p style="font-size:12px"><strong>Rata-rata:</strong> <span style="color:var(--teal);font-weight:700">{{ $rataNilai }}</span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-exclamation-triangle" style="color:var(--red)"></i> Tugas &amp; Ulangan</h3>
                <label @click="tab='nilai'" style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer">Selengkapnya</label>
            </div>
            @forelse($tugasAnak->take(4) as $t)
            <div class="task-item">
                <i class="fas fa-file-alt" style="color:var(--teal);font-size:18px"></i>
                <div class="task-info">
                    <div class="task-title">{{ $t->judul }}</div>
                    <div class="task-meta">{{ $t->mapel->nama_mapel ?? '-' }} &ndash; {{ \Carbon\Carbon::parse($t->tanggal_deadline)->format('d M Y') }}</div>
                </div>
                <span class="badge {{ $statusClass($t->tanggal_deadline) }}">{{ $statusLabel($t->tanggal_deadline) }}</span>
            </div>
            @empty
            <p style="padding:12px;color:var(--gray-400);font-size:13px">Belum ada tugas.</p>
            @endforelse
        </div>
    </div>
    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-quran" style="color:var(--green)"></i> Tahfidz</h3>
                <label @click="tab='tahfidz'" style="cursor:pointer;font-size:12px;color:var(--blue);font-weight:600">Detail</label>
            </div>
            <div style="padding:4px 0">
                <div style="display:flex;gap:16px">
                    <div><span style="font-size:11px;color:var(--gray-400)">Total Setoran</span><div style="font-size:18px;font-weight:700;color:var(--teal)">{{ $totalSetoran }}</div></div>
                    <div><span style="font-size:11px;color:var(--gray-400)">Rata-rata Nilai</span><div style="font-size:18px;font-weight:700;color:var(--blue)">{{ $rataTahfidz }}</div></div>
                    <div><span style="font-size:11px;color:var(--gray-400)">Total Ayat</span><div style="font-size:18px;font-weight:700;color:var(--purple)">{{ $tahfidzAnak->sum('jumlah_ayat') }}</div></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-envelope" style="color:var(--blue)"></i> Pesan dari Guru</h3>
                <label @click="tab='pesan'" style="cursor:pointer;font-size:12px;color:var(--blue);font-weight:600">Semua</label>
            </div>
            @forelse($pesanGuru->take(3) as $p)
            <div class="msg-item" style="padding:7px 0">
                <div class="msg-sender" style="font-size:12px">{{ $p->pengirim->name ?? 'Guru' }} <span style="color:var(--gray-400);font-weight:400">&mdash; {{ $p->created_at->format('d M') }}</span></div>
                <div class="msg-preview" style="font-size:11px">{{ Str::limit($p->isi, 60) }}</div>
            </div>
            @empty
            <p style="padding:12px;color:var(--gray-400);font-size:13px">Belum ada pesan.</p>
            @endforelse
        </div>
    </div>
</div>
@endforeach
