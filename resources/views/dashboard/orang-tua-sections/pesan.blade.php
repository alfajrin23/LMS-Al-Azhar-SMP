<div class="content-header">
    <h1>Pesan &amp; Pengumuman</h1>
    <div class="header-right">
        <label @click="showTulisPesan = !showTulisPesan" class="header-btn primary" style="cursor:pointer"><i class="fas fa-envelope"></i> Pesan Baru</label>
        <select x-model="childId" class="child-select">
            @foreach($anak as $a)
            <option value="{{ $a->id }}">{{ $a->nama }} &mdash; {{ $a->kelas->nama_kelas ?? 'N/A' }}</option>
            @endforeach
        </select>
        <div class="avatar orange">{{ strtoupper(substr($ortu?->nama ?? $user->name, 0, 2)) }}</div>
    </div>
</div>
<div x-show="showTulisPesan" class="card" style="margin-bottom:16px">
    <div class="card-header"><h3><i class="fas fa-paper-plane" style="color:var(--teal)"></i> Kirim Pesan ke Guru</h3></div>
    <form method="POST" action="{{ route('ortu.pesan.store') }}" style="padding:4px 0">
        @csrf
        <div class="form-group" style="margin-bottom:12px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tujuan</label>
            <select name="penerima_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);background:var(--white)">
                <option value="">Pilih Guru</option>
                @foreach($daftarGuru as $g)
                <option value="{{ $g->user_id }}">{{ $g->nama }} — {{ $g->mapel->nama_mapel ?? 'Mapel' }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom:12px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Pesan</label>
            <textarea name="isi" required rows="4" placeholder="Tulis pesan untuk guru..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);resize:vertical"></textarea>
        </div>
        <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-paper-plane"></i> Kirim</button>
        <button type="button" @click="showTulisPesan = false" class="btn-login" style="cursor:pointer;border:none;background:var(--gray-300);color:var(--text);margin-left:8px">Batal</button>
    </form>
</div>
<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-envelope" style="color:var(--blue)"></i> Pesan dari Guru</h3></div>
        @forelse($pesanGuru as $p)
        <div class="msg-item" style="padding:10px 0;border-bottom:1px solid var(--border-light)">
            <div class="msg-sender" style="font-size:13px;font-weight:600">{{ $p->pengirim->name ?? 'Guru' }} <span style="color:var(--gray-400);font-weight:400">&mdash; {{ $p->created_at->format('d M Y H:i') }}</span></div>
            <div class="msg-preview" style="font-size:12px;color:var(--gray-500);margin-top:4px">{{ $p->isi }}</div>
        </div>
        @empty
        <p style="padding:12px;color:var(--gray-400);font-size:13px">Belum ada pesan dari guru.</p>
        @endforelse
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-bullhorn" style="color:var(--orange)"></i> Pengumuman</h3></div>
        @forelse($pengumuman as $p)
        <div class="ann-item" style="padding:10px 0;border-bottom:1px solid var(--border-light)">
            <div class="ann-date" style="font-size:11px;color:var(--gray-400)">{{ $p->created_at->format('d M Y') }}</div>
            <div class="ann-title" style="font-size:13px;font-weight:600;margin-top:2px">{{ $p->judul }}</div>
            <div class="ann-desc" style="font-size:12px;color:var(--gray-500);margin-top:4px">{{ $p->konten }}</div>
        </div>
        @empty
        <p style="padding:12px;color:var(--gray-400);font-size:13px">Belum ada pengumuman.</p>
        @endforelse
    </div>
</div>
