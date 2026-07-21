<div class="content-header">
    <h1>Buku Penghubung</h1>
    <div class="header-right">
        <label onclick="alert('Fitur pesan baru sedang dalam pengembangan');return false" class="header-btn primary" style="cursor:pointer"><i class="fas fa-envelope"></i> Pesan Baru</label>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h3><i class="fas fa-inbox" style="color:var(--blue)"></i> Kotak Masuk</h3></div>
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
