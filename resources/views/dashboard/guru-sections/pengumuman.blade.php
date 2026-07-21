<div class="content-header">
    <h1>Pengumuman</h1>
    <div class="header-right">
        <label @click="showTulisPesan = !showTulisPesan" class="header-btn primary" style="cursor:pointer"><i class="fas fa-plus"></i> Buat Pengumuman</label>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>

<div x-show="showTulisPesan" class="card" style="margin-bottom:16px">
    <div class="card-header"><h3><i class="fas fa-bullhorn" style="color:var(--teal)"></i> Buat Pengumuman Baru</h3></div>
    <form method="POST" action="{{ route('guru.pengumuman.store') }}" style="padding:4px 0">
        @csrf
        <div class="form-group" style="margin-bottom:12px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Judul</label>
            <input type="text" name="judul" required placeholder="Judul pengumuman" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
        </div>
        <div class="form-group" style="margin-bottom:12px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Target</label>
            <select name="target_role" class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);background:var(--white)">
                <option value="">Semua</option>
                <option value="siswa_sd">Siswa SD</option>
                <option value="siswa_smp">Siswa SMP</option>
                <option value="guru">Guru</option>
                <option value="orang_tua">Orang Tua</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:12px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Konten</label>
            <textarea name="konten" required rows="5" placeholder="Isi pengumuman..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);resize:vertical"></textarea>
        </div>
        <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-paper-plane"></i> Publikasikan</button>
        <button type="button" @click="showTulisPesan = false" class="btn-login" style="cursor:pointer;border:none;background:var(--gray-300);color:var(--text);margin-left:8px">Batal</button>
    </form>
</div>

<div class="card">
    <div class="card-header"><h3><i class="fas fa-bullhorn" style="color:var(--orange)"></i> Pengumuman Sekolah</h3></div>
    @forelse($pengumuman as $p)
    <div class="ann-item">
        <div class="ann-date">{{ \Carbon\Carbon::parse($p->created_at)->format('d F Y') }}</div>
        <div class="ann-title">{{ $p->judul }}</div>
        <div class="ann-desc">{{ $p->konten }}</div>
    </div>
    @empty
    <div style="padding:20px;text-align:center;color:var(--gray-400)">Tidak ada pengumuman</div>
    @endforelse
</div>
