@php
    $inits = strtoupper(substr($guru->nama, 0, 1) . (str_contains($guru->nama, ' ') ? substr(explode(' ', $guru->nama)[1], 0, 1) : ''));
    $totalKelas = $kelasYangDiajar->count();
    $totalSiswa = $kelasYangDiajar->sum(fn($k) => $k->siswa_count);
    $tugasBelumDinilai = $tugas->filter(fn($t) => \Carbon\Carbon::parse($t->tanggal_deadline)->isPast())->count();
    $ulanganAktif = $tugas->filter(fn($t) => $t->tipe === 'ulangan' && !\Carbon\Carbon::parse($t->tanggal_deadline)->isPast())->count();
@endphp
<div class="content-header">
    <div>
        <h1>Dashboard Guru</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Selamat datang, {{ $guru->nama }}</p>
    </div>
    <div class="header-right">
        <label @click="tab='tugas'" class="header-btn primary" style="cursor:pointer"><i class="fas fa-plus"></i> Buat Tugas</label>
        <label @click="tab='tugas'" class="header-btn outline" style="cursor:pointer"><i class="fas fa-pencil-alt"></i> Buat Ulangan</label>
        <div class="avatar blue">{{ $inits }}</div>
    </div>
</div>

<div class="quick-action">
    <label @click="tab='tugas'" class="quick-action-item" style="cursor:pointer">
        <span class="qa-icon">&#x1F4DD;</span><span class="qa-label">Buat Tugas</span>
    </label>
    <label @click="tab='nilai'" class="quick-action-item" style="cursor:pointer">
        <span class="qa-icon">&#x1F4CA;</span><span class="qa-label">Input Nilai</span>
    </label>
    <label @click="tab='pengumuman'" class="quick-action-item" style="cursor:pointer">
        <span class="qa-icon">&#x1F4E2;</span><span class="qa-label">Pengumuman</span>
    </label>
    <label @click="tab='kelas'" class="quick-action-item" style="cursor:pointer">
        <span class="qa-icon">&#x1F4C5;</span><span class="qa-label">Jadwal</span>
    </label>
</div>

<div class="grid-4" style="margin-bottom:20px">
    <div class="stat-card centered">
        <div class="stat-icon-wrap teal"><i class="fas fa-chalkboard"></i></div>
        <div class="stat-number">{{ $totalKelas }}</div>
        <div class="stat-label">Kelas Aktif</div>
    </div>
    <div class="stat-card centered">
        <div class="stat-icon-wrap blue"><i class="fas fa-users"></i></div>
        <div class="stat-number">{{ $totalSiswa }}</div>
        <div class="stat-label">Jumlah Siswa</div>
    </div>
    <div class="stat-card centered">
        <div class="stat-icon-wrap orange"><i class="fas fa-file-alt"></i></div>
        <div class="stat-number">{{ $tugasBelumDinilai }}</div>
        <div class="stat-label">Tugas Lewat Deadline</div>
    </div>
    <div class="stat-card centered">
        <div class="stat-icon-wrap purple"><i class="fas fa-pencil-alt"></i></div>
        <div class="stat-number">{{ $ulanganAktif }}</div>
        <div class="stat-label">Ulangan Aktif</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chalkboard" style="color:var(--teal)"></i> Kelas yang Diajar</h3>
            <label @click="tab='kelas'" style="cursor:pointer;color:var(--blue);font-weight:600;font-size:12px">Kelola</label>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kelas</th><th>Mapel</th><th>Siswa</th><th>Rata-rata</th><th></th></tr></thead>
                <tbody>
                    @foreach($kelasYangDiajar as $k)
                    <tr>
                        <td><strong>{{ $k->nama_kelas }}</strong></td>
                        <td>{{ $guru->mapels->pluck('kode')->implode(', ') }}</td>
                        <td>{{ $k->siswa_count }}</td>
                        <td>{{ number_format($k->rataNilai, 1) }}</td>
                        <td><label @click="selectedKelas='{{ $k->id }}'; tab='kelas-detail'" class="btn-small teal" style="cursor:pointer">Masuk</label></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:16px">
            <div class="card-header">
                <h3><i class="fas fa-tasks" style="color:var(--orange)"></i> Tugas Terbaru</h3>
                <label @click="tab='tugas'" style="cursor:pointer;color:var(--blue);font-weight:600;font-size:12px">Semua</label>
            </div>
            @forelse($tugas->sortBy('tanggal_deadline')->take(4) as $t)
                @php
                    $d = \Carbon\Carbon::parse($t->tanggal_deadline);
                    $badge = $d->isPast() ? 'status-terlambat' : ($d->diffInDays(now()) <= 3 ? 'status-mendekati' : 'teal');
                    $label = $d->isPast() ? 'Terlambat' : ($d->diffInDays(now()) <= 3 ? 'Mendekati' : 'Aktif');
                @endphp
                <div class="task-item">
                    <i class="fas fa-file-alt" style="color:var(--{{ ['blue','purple','teal','orange'][$loop->index % 4] }});font-size:18px"></i>
                    <div class="task-info">
                        <div class="task-title">{{ $t->judul }}</div>
                        <div class="task-meta">{{ $t->kelas->nama_kelas ?? '' }} &ndash; {{ $d->format('d M Y') }}</div>
                    </div>
                    <span class="badge {{ $badge }}">{{ $label }}</span>
                </div>
            @empty
                <div style="padding:20px;text-align:center;color:var(--gray-400)">Belum ada tugas</div>
            @endforelse
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-calendar-alt" style="color:var(--blue)"></i> Kalender Akademik</h3>
            </div>
            <div class="mini-calendar">
                @php
                    $now = now();
                    $first = $now->copy()->startOfMonth();
                    $last = $now->copy()->endOfMonth();
                    $startDay = $first->dayOfWeek;
                    $daysInMonth = $now->daysInMonth;
                    $hariIni = $now->day;
                    $hariNama = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                @endphp
                <table class="cal-table">
                    <thead><tr>@foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
                    <tbody>
                        @php $dayNum = 1; $weeks = []; @endphp
                        @for($w = 0; $w < 6 && $dayNum <= $daysInMonth; $w++)
                            <tr>
                                @for($d = 0; $d < 7; $d++)
                                    @if(($w == 0 && $d < $startDay) || $dayNum > $daysInMonth)
                                        <td></td>
                                    @else
                                        <td class="{{ $dayNum == $hariIni ? 'today' : '' }}">{{ $dayNum }}</td>
                                        @php $dayNum++; @endphp
                                    @endif
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-inbox" style="color:var(--blue)"></i> Pesan Masuk</h3>
        <label @click="tab='pesan'" style="cursor:pointer;color:var(--blue);font-weight:600;font-size:12px">Semua Pesan</label>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Pengirim</th><th>Pesan</th><th>Waktu</th><th></th></tr></thead>
            <tbody>
                @forelse($pesan as $m)
                <tr>
                    <td><strong>{{ $m->pengirim->name }}</strong> <span class="badge light {{ $m->pengirim->role === 'orang_tua' ? 'orange' : 'teal' }}">{{ $m->pengirim->role === 'orang_tua' ? 'Orang Tua' : 'Siswa' }}</span></td>
                    <td>{{ $m->isi }}</td>
                    <td style="color:var(--gray-400);font-size:12px">{{ $m->created_at->diffForHumans() }}</td>
                    <td><label @click="alert('Fitur balas pesan sedang dalam pengembangan');return false" class="btn-small outline" style="cursor:pointer">Balas</label></td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--gray-400)">Tidak ada pesan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
