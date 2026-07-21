@php
    $semuaSiswa = \App\Models\Siswa::with('kelas')->get();
    $semuaCatatan = \App\Models\CatatanWali::with('siswa', 'siswa.kelas', 'guru')->orderBy('created_at', 'desc')->get();
@endphp
<div class="content-header">
    <h1>Catatan Wali Kelas</h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-plus" style="color:var(--orange)"></i> Buat Catatan Wali Kelas</h3></div>
        <form method="POST" action="/guru/catatan" style="padding:4px 0">
            @csrf
            <div class="form-group" style="margin-bottom:14px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Siswa</label>
                <select name="siswa_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                    <option value="">Pilih Siswa</option>
                    @foreach($semuaSiswa as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Semester</label>
                <select name="semester" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                    <option value="{{ setting('semester_aktif') }}" selected>{{ setting('semester_aktif') }}</option>
                    <option value="Ganjil 2026/2027">Ganjil 2026/2027</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Catatan</label>
                <textarea name="catatan" required rows="4" placeholder="Tulis catatan untuk siswa ini..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
            </div>
            <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-save"></i> Simpan Catatan</button>
        </form>
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Statistik Catatan</h3></div>
        <div style="padding:4px 0">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Total Catatan</span><div style="font-size:22px;font-weight:700;color:var(--teal)">{{ $semuaCatatan->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Siswa Dicatat</span><div style="font-size:22px;font-weight:700;color:var(--blue)">{{ $semuaCatatan->pluck('siswa_id')->unique()->count() }}</div></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3><i class="fas fa-list" style="color:var(--blue)"></i> Riwayat Catatan</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Siswa</th><th>Kelas</th><th>Semester</th><th>Catatan</th><th>Wali Kelas</th><th>Tanggal</th></tr></thead>
            <tbody>
                @forelse($semuaCatatan as $c)
                <tr>
                    <td><strong>{{ $c->siswa->nama ?? '-' }}</strong></td>
                    <td>{{ $c->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $c->semester }}</td>
                    <td style="max-width:300px;font-size:13px">{{ Str::limit($c->catatan, 100) }}</td>
                    <td>{{ $c->guru->nama ?? '-' }}</td>
                    <td style="color:var(--gray-400);font-size:12px">{{ $c->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada catatan wali kelas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(session('success'))
    <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
