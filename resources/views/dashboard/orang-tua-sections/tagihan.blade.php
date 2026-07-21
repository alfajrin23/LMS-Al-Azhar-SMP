<div class="content-header">
    <h1>Tagihan &amp; Pembayaran</h1>
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
    $tagihanAnak = \App\Models\Spp::where('siswa_id', $a->id)->with('pembayarans')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
    $totalTagihan = $tagihanAnak->sum('jumlah');
    $totalTerbayar = $tagihanAnak->filter(fn($s) => $s->status === 'lunas')->sum('jumlah');
    $belumBayar = $tagihanAnak->where('status', '!=', 'lunas');
    $totalBelumBayar = $belumBayar->sum('jumlah');
    $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp
<div x-show="childId == {{ $a->id }}">
    <div class="grid-2" style="margin-bottom:20px">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-file-invoice" style="color:var(--teal)"></i> Ringkasan SPP — {{ $a->nama }}</h3></div>
            <div style="padding:4px 0">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div><span style="font-size:12px;color:var(--gray-400)">Total SPP</span><div style="font-size:22px;font-weight:700;color:var(--teal)">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div></div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Sudah Dibayar</span><div style="font-size:22px;font-weight:700;color:var(--green)">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div></div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Belum Dibayar</span><div style="font-size:22px;font-weight:700;color:var(--orange)">Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}</div></div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Status</span><div style="font-size:18px;font-weight:700;color:{{ $totalBelumBayar > 0 ? 'var(--orange)' : 'var(--green)' }}">{{ $totalBelumBayar > 0 ? 'BELUM LUNAS' : 'LUNAS' }}</div></div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-credit-card" style="color:var(--blue)"></i> Bayar SPP</h3></div>
            <form method="POST" action="{{ route('ortu.bayar.store') }}" style="padding:4px 0">
                @csrf
                <input type="hidden" name="siswa_id" value="{{ $a->id }}">
                <div class="form-group" style="margin-bottom:12px">
                    <select name="spp_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);background:var(--white)">
                        <option value="">Pilih Tagihan</option>
                        @foreach($belumBayar as $s)
                        <option value="{{ $s->id }}">{{ $namaBulan[$s->bulan - 1] }} {{ $s->tahun }} — Rp {{ number_format($s->jumlah, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:12px">
                    <select name="metode" class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);background:var(--white)">
                        <option value="transfer">Transfer Bank</option>
                        <option value="va">Virtual Account</option>
                        <option value="qris">QRIS</option>
                        <option value="tunai">Tunai (Sekolah)</option>
                    </select>
                </div>
                <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-credit-card"></i> Bayar Sekarang</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-list" style="color:var(--blue)"></i> Riwayat Tagihan SPP</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Periode</th><th>Jumlah</th><th>Tenggat</th><th>Status</th><th>Pembayaran</th></tr></thead>
                <tbody>
                    @forelse($tagihanAnak as $s)
                    @php
                        $pembayaran = $s->pembayarans->first();
                    @endphp
                    <tr>
                        <td><strong>{{ $namaBulan[$s->bulan - 1] }} {{ $s->tahun }}</strong></td>
                        <td>Rp {{ number_format($s->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $s->tenggat ? \Carbon\Carbon::parse($s->tenggat)->format('d M Y') : '-' }}</td>
                        <td><span class="badge light {{ $s->status === 'lunas' ? 'green' : ($s->status === 'pending' ? 'orange' : 'red') }}">{{ $s->status === 'lunas' ? 'Lunas' : ($s->status === 'pending' ? 'Pending' : 'Belum Bayar') }}</span></td>
                        <td style="font-size:12px;color:var(--gray-400)">
                            @if($pembayaran)
                                {{ $pembayaran->created_at->format('d M Y') }} — {{ $pembayaran->metode }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--gray-400)">Belum ada tagihan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
