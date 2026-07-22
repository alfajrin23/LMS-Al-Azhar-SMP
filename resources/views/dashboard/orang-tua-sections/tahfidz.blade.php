<div class="content-header">
    <h1>Tahfidz &amp; Setoran Ayat</h1>
    <div class="header-right">
        <select x-model="childId" class="child-select">
            @foreach($anak as $a)
                <option value="{{ $a->id }}">{{ $a->nama }} - {{ $a->kelas->nama_kelas ?? 'N/A' }}</option>
            @endforeach
        </select>
        <div class="avatar orange">{{ strtoupper(substr($ortu?->nama ?? $user->name, 0, 2)) }}</div>
    </div>
</div>

@foreach($anak as $a)
@php
    $data = ($anakTahfidzData ?? collect())->get($a->id, ['setoran' => collect(), 'progress' => null]);
    $tahfidzAnak = $data['setoran'];
    $progress = $data['progress'];
    $totalSetoran = $tahfidzAnak->count();
    $rataTahfidz = $tahfidzAnak->count() > 0 ? round($tahfidzAnak->avg('nilai'), 1) : 0;
    $totalAyat = $progress?->total_ayat ?? $tahfidzAnak->sum('jumlah_ayat');
    $lastSetoran = $tahfidzAnak->first();
    $jadwalBerikutnya = $progress?->tanggal_pertemuan_berikutnya ?? $tahfidzAnak->whereNotNull('tanggal_berikutnya')->first()?->tanggal_berikutnya;
@endphp
<div x-show="childId == {{ $a->id }}">
    <div class="card" style="margin-bottom:20px">
        <div class="card-header"><h3><i class="fas fa-quran" style="color:var(--green)"></i> Progress Quran - {{ $a->nama }}</h3></div>
        <div class="tahfidz-stats" style="margin-bottom:16px">
            <div class="tahfidz-stat"><span class="tahfidz-stat-number green">{{ $progress?->tingkat_ummi ?? '-' }}</span><span class="tahfidz-stat-label">Tingkat UMMI</span></div>
            <div class="tahfidz-stat"><span class="tahfidz-stat-number teal">{{ $progress?->progress_percent ?? 0 }}%</span><span class="tahfidz-stat-label">Progress</span></div>
            <div class="tahfidz-stat"><span class="tahfidz-stat-number blue">{{ $totalSetoran }}</span><span class="tahfidz-stat-label">Setoran</span></div>
            <div class="tahfidz-stat"><span class="tahfidz-stat-number orange">{{ $totalAyat }}</span><span class="tahfidz-stat-label">Ayat</span></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;font-size:13px">
            <div><span style="color:var(--gray-400)">Posisi Jilid/Halaman</span><div style="font-weight:700">{{ $progress?->posisi_tilawah ?? '-' }}</div></div>
            <div><span style="color:var(--gray-400)">Surat/Ayat Terakhir</span><div style="font-weight:700">{{ $progress?->hafalan_terakhir ?? $lastSetoran?->surah ?? '-' }} {{ $lastSetoran ? $lastSetoran->ayat_selesai : '' }}</div></div>
            <div><span style="color:var(--gray-400)">Predikat</span><div style="font-weight:700">{{ $progress?->predikat ?? '-' }}</div></div>
            <div><span style="color:var(--gray-400)">Kelancaran</span><div style="font-weight:700">{{ $progress?->kelancaran ?? '-' }}</div></div>
            <div><span style="color:var(--gray-400)">Tajwid</span><div style="font-weight:700">{{ $progress?->tajwid ?? '-' }}</div></div>
            <div><span style="color:var(--gray-400)">Makhroj</span><div style="font-weight:700">{{ $progress?->makhroj ?? '-' }}</div></div>
            <div><span style="color:var(--gray-400)">Adab</span><div style="font-weight:700">{{ $progress?->adab ?? '-' }}</div></div>
            <div><span style="color:var(--gray-400)">Target Berikutnya</span><div style="font-weight:700">{{ $progress?->target_berikutnya ?? $progress?->target_deskripsi ?? '-' }}</div></div>
            <div><span style="color:var(--gray-400)">Pertemuan Berikutnya</span><div style="font-weight:700">{{ $jadwalBerikutnya ? \Carbon\Carbon::parse($jadwalBerikutnya)->format('d M Y') : '-' }}</div></div>
        </div>
        @if($progress?->catatan)
            <p style="font-size:13px;color:var(--gray-500);line-height:1.6;margin-top:12px">{{ $progress->catatan }}</p>
        @endif
    </div>

    <div class="card">
        <div class="card-header"><h3><i class="fas fa-list" style="color:var(--teal)"></i> Riwayat Setoran</h3></div>
        <div class="table-wrap">
            <table class="tahfidz-table">
                <thead><tr><th>Tanggal</th><th>Surah</th><th>Ayat</th><th>Status</th><th>Nilai</th><th>Guru</th><th>Catatan</th></tr></thead>
                <tbody>
                    @forelse($tahfidzAnak as $t)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                            <td><strong>{{ $t->surah }}</strong></td>
                            <td>{{ $t->ayat_mulai }}-{{ $t->ayat_selesai }} ({{ $t->jumlah_ayat }} ayat)</td>
                            <td><span class="badge light {{ $t->status === 'baru' ? 'green' : 'blue' }}">{{ $t->status === 'baru' ? 'Setoran Baru' : 'Murojaah' }}</span></td>
                            <td style="font-weight:700">{{ $t->nilai ?? '-' }}</td>
                            <td>{{ $t->guru->nama ?? '-' }}</td>
                            <td>{{ $t->catatan_guru ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--gray-400)">Belum ada setoran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
