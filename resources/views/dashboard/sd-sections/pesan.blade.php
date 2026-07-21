<div class="sd-content sd-content-pesan">
    <div class="content-header">
        <h1>Buku Penghubung</h1>
        <div class="header-right"><label @click="showTulisPesan = !showTulisPesan" class="header-btn primary"
                style="cursor:pointer; background:var(--blue);"><i class="fas fa-envelope"></i> Catatan Baru</label>
            <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
        </div>
    </div>
    <div x-show="showTulisPesan" class="card" style="margin-bottom:16px">
        <div class="card-header">
            <h3><i class="fas fa-paper-plane" style="color:var(--blue)"></i> Kirim Catatan ke Guru</h3>
        </div>
        <form method="POST" action="{{ route('siswa.pesan.store') }}" style="padding:4px 0">
            @csrf
            <div class="form-group" style="margin-bottom:12px">
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tujuan</label>
                <select name="penerima_id" required class="form-select"
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);background:var(--white)">
                    <option value="">Pilih Guru</option>
                    @foreach ($guruUsers as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:12px">
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Pesan</label>
                <textarea name="isi" required rows="4" placeholder="Tulis pesan untuk guru..."
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);resize:vertical"></textarea>
            </div>
            <button type="submit" class="btn-login" style="cursor:pointer;border:none;background:var(--blue)"><i
                    class="fas fa-paper-plane"></i> Kirim</button>
            <button type="button" @click="showTulisPesan = false" class="btn-login"
                style="cursor:pointer;border:none;background:var(--gray-300);color:var(--text);margin-left:8px">Batal</button>
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-inbox" style="color:var(--orange)"></i> Kotak Masuk</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengirim</th>
                        <th>Pesan</th>
                        <th>Waktu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesan as $m)
                        <tr>
                            <td><strong>{{ $m->pengirim->name }}</strong> <span class="badge light blue">Guru</span>
                            </td>
                            <td>{{ $m->isi }}</td>
                            <td style="color:var(--gray-400);font-size:12px">{{ $m->created_at->diffForHumans() }}</td>
                            <td><label
                                    @click="replyTo='{{ $m->pengirim->name }}'; replyId={{ $m->pengirim_id }}; showReplyForm = !showReplyForm"
                                    class="btn-small outline" style="cursor:pointer;color:var(--blue);border-color:var(--blue)">Balas</label></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:var(--gray-400)">Tidak ada catatan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div x-show="showReplyForm" class="card" style="margin-bottom:16px">
        <div class="card-header">
            <h3><i class="fas fa-reply" style="color:var(--blue)"></i> Balas Catatan — <span x-text="replyTo"></span></h3>
        </div>
        <form method="POST" action="{{ route('siswa.pesan.store') }}" style="padding:4px 0">
            @csrf
            <input type="hidden" name="penerima_id" x-model="replyId">
            <div class="form-group" style="margin-bottom:12px">
                <textarea name="isi" required rows="4" placeholder="Tulis balasan..."
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);resize:vertical"></textarea>
            </div>
            <button type="submit" class="btn-login" style="cursor:pointer;border:none;background:var(--blue)"><i class="fas fa-reply"></i>
                Kirim Balasan</button>
            <button type="button" @click="showReplyForm = false" class="btn-login"
                style="cursor:pointer;border:none;background:var(--gray-300);color:var(--text);margin-left:8px">Batal</button>
        </form>
    </div>
</div>
