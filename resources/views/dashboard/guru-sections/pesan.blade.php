@php
    $siswaOptions = $siswaDiajar ?? collect();
    $ortuOptions = $orangTuaDiajar ?? collect();
    $pesan = $pesan ?? collect();
@endphp

<div x-data="{ showCompose: false, replyId: null }">
    <div class="content-header">
        <h1>Buku Penghubung</h1>
        <div class="header-right">
            <button type="button" @click="showCompose = !showCompose" class="header-btn primary" style="cursor:pointer;border:none"><i class="fas fa-envelope"></i> Pesan Baru</button>
            <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
        </div>
    </div>

    <div x-show="showCompose" class="card" style="margin-bottom:16px">
        <div class="card-header"><h3><i class="fas fa-paper-plane" style="color:var(--teal)"></i> Kirim Pesan</h3></div>
        <form method="POST" action="{{ route('guru.pesan.store') }}">
            @csrf
            <select name="penerima_id" required class="form-select" style="width:100%;padding:9px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white;margin-bottom:10px">
                <optgroup label="Siswa">
                    @foreach($siswaOptions as $s)
                        <option value="{{ $s->user_id }}">{{ $s->nama }} - {{ $s->kelas->nama_kelas ?? '-' }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Orang Tua">
                    @foreach($ortuOptions as $ortu)
                        <option value="{{ $ortu->user_id }}">{{ $ortu->nama }} - {{ $ortu->siswa->pluck('nama')->implode(', ') }}</option>
                    @endforeach
                </optgroup>
            </select>
            <select name="siswa_id" required class="form-select" style="width:100%;padding:9px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white;margin-bottom:10px">
                <option value="">Pilih siswa terkait</option>
                @foreach($siswaOptions as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }} - {{ $s->kelas->nama_kelas ?? '-' }}</option>
                @endforeach
            </select>
            <input type="text" name="subjek" required placeholder="Subjek" style="width:100%;padding:9px;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:10px">
            <select name="kategori" class="form-select" style="width:100%;padding:9px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white;margin-bottom:10px">
                @foreach(['Akademik','Kehadiran','Sikap','Tugas','Tahsin/Tahfidz','Informasi Sekolah','Lainnya'] as $kategori)
                    <option value="{{ $kategori }}">{{ $kategori }}</option>
                @endforeach
            </select>
            <textarea name="isi" required rows="4" placeholder="Isi pesan" style="width:100%;padding:9px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical"></textarea>
            <button class="btn-login" style="border:none;cursor:pointer;margin-top:12px"><i class="fas fa-paper-plane"></i> Kirim</button>
        </form>
    </div>

    <style>
        .btn-tab-active { background: var(--blue); color: #fff; }
        .btn-tab-inactive { background: var(--gray-100); color: var(--gray-500); }
    </style>

    <div class="card">
        <div class="card-header"><h3><i class="fas fa-inbox" style="color:var(--blue)"></i> Kotak Masuk</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Pengirim</th><th>Siswa</th><th>Subjek</th><th>Kategori</th><th>Pesan</th><th>Waktu</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($pesan as $m)
                        <tr>
                            <td><strong>{{ $m->pengirim->name }}</strong><br><span class="badge light {{ $m->pengirim->role === 'orang_tua' ? 'orange' : 'teal' }}">{{ str_replace('_', ' ', $m->pengirim->role) }}</span></td>
                            <td>{{ $m->siswa->nama ?? '-' }}</td>
                            <td>{{ $m->subjek ?? 'Buku Penghubung' }}</td>
                            <td><span class="badge light blue">{{ $m->kategori ?? 'Lainnya' }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($m->isi, 120) }}</td>
                            <td style="color:var(--gray-400);font-size:12px">{{ $m->created_at->diffForHumans() }}</td>
                            <td>
                                <button type="button" @click="replyId = replyId === {{ $m->id }} ? null : {{ $m->id }}" class="btn-small outline" style="cursor:pointer"><i class="fas fa-reply"></i> Balas</button>
                                <form x-show="replyId === {{ $m->id }}" x-cloak method="POST" action="{{ route('guru.pesan.store') }}" style="margin-top:8px;display:flex;flex-direction:column;gap:6px;min-width:220px">
                                    @csrf
                                    <input type="hidden" name="penerima_id" value="{{ $m->pengirim_id }}">
                                    <input type="hidden" name="siswa_id" value="{{ $m->siswa_id }}">
                                    <input type="hidden" name="parent_message_id" value="{{ $m->id }}">
                                    <input type="hidden" name="subjek" value="{{ $m->subjek }}">
                                    <input type="hidden" name="kategori" value="{{ $m->kategori }}">
                                    <textarea name="isi" required rows="2" placeholder="Tulis balasan" style="padding:7px;border:1px solid var(--border);border-radius:4px;resize:vertical"></textarea>
                                    <button class="btn-small teal" style="border:none;cursor:pointer"><i class="fas fa-paper-plane"></i> Kirim Balasan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:20px">Tidak ada pesan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
