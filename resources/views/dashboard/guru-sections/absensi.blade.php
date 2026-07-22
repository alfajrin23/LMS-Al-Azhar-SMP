@php
    $kelasOptions = $absensiKelasList ?? collect();
    $mapelOptions = $absensiMapelList ?? collect();
    $riwayatAbsensi = $riwayatAbsensi ?? collect();
    $absensiHariIni = $absensiHariIni ?? collect();
    $statusBadge = fn($s) => match($s) {
        'hadir' => '<span class="badge light green">Hadir</span>',
        'sakit' => '<span class="badge light orange">Sakit</span>',
        'izin' => '<span class="badge light blue">Izin</span>',
        'alpha' => '<span class="badge light red">Alpha</span>',
        default => '<span class="badge">-</span>',
    };
@endphp

<div x-data="{ selectedKelas: '{{ $kelasOptions->first()?->id }}' }">
    <div class="content-header">
        <div>
            <h1>Daftar Hadir Kelas</h1>
            <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Input absensi satu kelas berdasarkan tanggal, pertemuan, dan mata pelajaran</p>
        </div>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom:20px">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-users" style="color:var(--teal)"></i> Input Absensi Satu Kelas</h3></div>
            @if($kelasOptions->isEmpty() || $mapelOptions->isEmpty())
                <p style="padding:12px;color:var(--gray-400);font-size:13px">Belum ada jadwal kelas atau mapel untuk akun guru ini.</p>
            @else
                <form method="POST" action="{{ route('guru.absensi.store') }}" style="padding:4px 0">
                    @csrf
                    <div class="grid-2" style="gap:10px;margin-bottom:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Kelas</label>
                            <select name="kelas_id" x-model="selectedKelas" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                                @foreach($kelasOptions as $kelasItem)
                                    <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Mata Pelajaran</label>
                            <select name="mapel_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                                @foreach($mapelOptions as $mapelItem)
                                    <option value="{{ $mapelItem->id }}">{{ $mapelItem->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Tanggal</label>
                            <input type="date" name="tanggal" required value="{{ now()->format('Y-m-d') }}" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Pertemuan / Jam Ke</label>
                            <input type="text" name="pertemuan" value="1" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        </div>
                    </div>
                    <input type="hidden" name="tahun_ajaran" value="2026/2027">
                    <input type="hidden" name="semester" value="Ganjil">

                    @foreach($kelasOptions as $kelasItem)
                        <div x-show="selectedKelas === '{{ $kelasItem->id }}'" x-cloak>
                            <div class="table-wrap" style="max-height:360px;overflow:auto">
                                <table>
                                    <thead><tr><th>Siswa</th><th>Status</th><th>Keterangan</th></tr></thead>
                                    <tbody>
                                        @forelse($kelasItem->siswa as $siswaItem)
                                            <tr>
                                                <td><strong>{{ $siswaItem->nama }}</strong><br><small style="color:var(--gray-400)">{{ $siswaItem->nis }}</small></td>
                                                <td>
                                                    <select name="absensi[{{ $siswaItem->id }}][status]" class="form-select" style="padding:6px;border:1px solid var(--border);border-radius:4px;background:white">
                                                        <option value="hadir">Hadir</option>
                                                        <option value="sakit">Sakit</option>
                                                        <option value="izin">Izin</option>
                                                        <option value="alpha">Alpha</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="absensi[{{ $siswaItem->id }}][keterangan]" placeholder="Opsional" style="width:100%;padding:6px;border:1px solid var(--border);border-radius:4px"></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" style="text-align:center;color:var(--gray-400);padding:16px">Belum ada siswa pada kelas ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                    <button type="submit" class="btn-login" style="cursor:pointer;border:none;margin-top:12px"><i class="fas fa-save"></i> Simpan Absensi Kelas</button>
                </form>
            @endif
        </div>

        <div class="card">
            <div class="card-header"><h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Statistik Hari Ini</h3></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Total Absensi</span><div style="font-size:22px;font-weight:700;color:var(--teal)">{{ $absensiHariIni->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Hadir</span><div style="font-size:22px;font-weight:700;color:var(--green)">{{ $absensiHariIni->where('status', 'hadir')->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Sakit</span><div style="font-size:22px;font-weight:700;color:var(--orange)">{{ $absensiHariIni->where('status', 'sakit')->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Izin</span><div style="font-size:22px;font-weight:700;color:var(--blue)">{{ $absensiHariIni->where('status', 'izin')->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Alpha</span><div style="font-size:22px;font-weight:700;color:var(--red)">{{ $absensiHariIni->where('status', 'alpha')->count() }}</div></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3><i class="fas fa-list" style="color:var(--blue)"></i> Riwayat Kehadiran</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Status</th><th>Keterangan</th></tr></thead>
                <tbody>
                    @forelse($riwayatAbsensi as $a)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                            <td><strong>{{ $a->siswa->nama ?? '-' }}</strong></td>
                            <td>{{ $a->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $a->mapel->nama_mapel ?? '-' }}</td>
                            <td>{!! $statusBadge($a->status) !!}</td>
                            <td style="color:var(--gray-400);font-size:13px">{{ $a->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada data kehadiran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
