@php
    $kehadiranHariIni = \App\Models\KehadiranGuru::where('guru_id', $guru->id)
        ->where('tanggal', now()->format('Y-m-d'))
        ->first();
    $riwayatAbsensi = \App\Models\KehadiranGuru::where('guru_id', $guru->id)
        ->orderBy('tanggal', 'desc')
        ->limit(10)
        ->get();
@endphp
<div class="content-header">
    <h1>Absensi Guru</h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-fingerprint" style="color:var(--teal)"></i> Kehadiran Hari Ini ({{ now()->format('d M Y') }})</h3>
        </div>
        <div style="padding:10px 0; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; background:var(--gray-50); padding:16px; border-radius:var(--radius-sm); border:1px solid var(--border-light)">
                <div>
                    <div style="font-size:12px; color:var(--gray-500); margin-bottom:4px">Waktu Masuk</div>
                    <div style="font-size:24px; font-weight:700; color:var(--teal)">
                        {{ $kehadiranHariIni && $kehadiranHariIni->waktu_masuk ? \Carbon\Carbon::parse($kehadiranHariIni->waktu_masuk)->format('H:i') : '--:--' }}
                    </div>
                </div>
                <form action="{{ route('guru.kehadiran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="masuk">
                    <button type="submit" class="btn-login" style="cursor:pointer; border:none; padding:10px 20px" {{ $kehadiranHariIni && $kehadiranHariIni->waktu_masuk ? 'disabled' : '' }}>
                        <i class="fas fa-sign-in-alt"></i> Absen Masuk
                    </button>
                </form>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; background:var(--gray-50); padding:16px; border-radius:var(--radius-sm); border:1px solid var(--border-light)">
                <div>
                    <div style="font-size:12px; color:var(--gray-500); margin-bottom:4px">Waktu Pulang</div>
                    <div style="font-size:24px; font-weight:700; color:var(--orange)">
                        {{ $kehadiranHariIni && $kehadiranHariIni->waktu_pulang ? \Carbon\Carbon::parse($kehadiranHariIni->waktu_pulang)->format('H:i') : '--:--' }}
                    </div>
                </div>
                <form action="{{ route('guru.kehadiran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="pulang">
                    <button type="submit" class="btn-login" style="cursor:pointer; border:none; background:var(--orange); color:white; padding:10px 20px" {{ ($kehadiranHariIni && $kehadiranHariIni->waktu_pulang) || !($kehadiranHariIni && $kehadiranHariIni->waktu_masuk) ? 'disabled' : '' }}>
                        <i class="fas fa-sign-out-alt"></i> Absen Pulang
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-history" style="color:var(--blue)"></i> Riwayat Absensi (10 Hari Terakhir)</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatAbsensi as $ra)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($ra->tanggal)->format('d M Y') }}</strong></td>
                        <td>{{ $ra->waktu_masuk ? \Carbon\Carbon::parse($ra->waktu_masuk)->format('H:i') : '-' }}</td>
                        <td>{{ $ra->waktu_pulang ? \Carbon\Carbon::parse($ra->waktu_pulang)->format('H:i') : '-' }}</td>
                        <td>
                            @if($ra->status === 'hadir')
                                <span class="badge light green">Hadir</span>
                            @elseif($ra->status === 'sakit' || $ra->status === 'izin')
                                <span class="badge light orange">{{ ucfirst($ra->status) }}</span>
                            @else
                                <span class="badge light red">{{ ucfirst($ra->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:var(--gray-400); padding:20px">Belum ada riwayat absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
