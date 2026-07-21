<div class="content-header">
    <h1>Pengumuman <span>SMPIT {{ setting('school_name') }}</span></h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h3><i class="fas fa-bullhorn" style="color:var(--orange)"></i> Semua Pengumuman</h3></div>
    @forelse($pengumuman as $p)
    <div class="ann-item"><div class="ann-date">{{ \Carbon\Carbon::parse($p->created_at)->format('d F Y') }}</div><div class="ann-title">{{ $p->judul }}</div><div class="ann-desc">{{ $p->konten }}</div></div>
    @empty
    <div style="padding:20px;text-align:center;color:var(--gray-400)">Tidak ada pengumuman</div>
    @endforelse
</div>
