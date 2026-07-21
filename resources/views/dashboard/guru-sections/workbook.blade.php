@php
    $workbooks = \App\Models\Workbook::where('guru_id', $guru->id)
        ->with('mapel', 'kelas')
        ->orderBy('created_at', 'desc')
        ->get();
    $jadwalGuru = \App\Models\Jadwal::where('guru_id', $guru->id)->get();
    $mapelList = \App\Models\Mapel::akademik()
        ->whereIn('id', $jadwalGuru->pluck('mapel_id')->merge($guru->mapels->pluck('id'))->unique())
        ->get();
    $kelasList = \App\Models\Kelas::whereIn('id', $jadwalGuru->pluck('kelas_id')->unique())->get();
@endphp
<div class="content-header">
    <h1>Workbook &amp; Bank Soal</h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-plus" style="color:var(--teal)"></i> Buat Workbook Baru</h3>
        </div>
        <form method="POST" action="{{ route('guru.workbook.store') }}" style="padding:4px 0">
            @csrf
            <div class="form-group" style="margin-bottom:14px">
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Judul</label>
                <input type="text" name="judul" required placeholder="Judul workbook"
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
            </div>
            <div style="display:flex;gap:10px;margin-bottom:14px">
                <div class="form-group" style="flex:1">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Mata
                        Pelajaran</label>
                    <select name="mapel_id" required class="form-select"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <option value="">Pilih Mapel</option>
                        @foreach ($mapelList as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Kelas
                        (opsional)</label>
                    <select name="kelas_id" class="form-select"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:14px">
                <div class="form-group" style="flex:1">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tipe</label>
                    <select name="tipe" class="form-select"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <option value="penugasan_di_rumah">Penugasan di Rumah</option>
                        <option value="remedial">Remedial</option>
                        <option value="tugas_pengganti">Tugas Pengganti</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">&nbsp;</label>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Deskripsi
                    (opsional)</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi workbook..."
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
            </div>
            <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-save"></i> Buat
                Workbook</button>
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Statistik</h3>
        </div>
        <div style="padding:4px 0">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Total Workbook</span>
                    <div style="font-size:22px;font-weight:700;color:var(--teal)">{{ $workbooks->count() }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Total Soal</span>
                    <div style="font-size:22px;font-weight:700;color:var(--blue)">
                        {{ $workbooks->sum(fn($w) => $w->soals->count()) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@foreach ($workbooks as $wb)
    <div class="card" style="margin-bottom:14px">
        <div class="card-header">
            <h3><i class="fas fa-book" style="color:var(--teal)"></i> {{ $wb->judul }}</h3>
            <div style="display:flex;gap:8px;align-items:center">
                <span class="badge teal">{{ $wb->tipe }}</span>
                <span class="badge blue">{{ $wb->mapel->kode ?? '-' }}</span>
                <span class="badge orange" style="cursor:pointer"
                    @click="selectedWorkbook='{{ $wb->id }}'; selectedWorkbookTab='soal'"><i
                        class="fas fa-plus"></i> Tambah Soal</span>
            </div>
        </div>
        <div style="padding:8px 0;font-size:13px;color:var(--gray-400)">
            {{ $wb->deskripsi ?: '' }}
            @if ($wb->kelas)
                <span style="margin-left:12px"><i class="fas fa-users"></i> {{ $wb->kelas->nama_kelas }}</span>
            @endif
            <span style="margin-left:12px"><i class="fas fa-question-circle"></i> {{ $wb->soals->count() }} soal</span>
        </div>
        <div x-show="selectedWorkbook == '{{ $wb->id }}'" x-transition
            style="margin-top:12px;border-top:1px solid var(--border-light);padding-top:16px">
            <h4 style="font-size:14px;font-weight:600;margin-bottom:12px"><i class="fas fa-plus-circle"
                    style="color:var(--teal)"></i> Tambah Soal ke "{{ $wb->judul }}"</h4>
            <form method="POST" action="{{ route('guru.workbook.soal', $wb->id) }}" style="max-width:500px">
                @csrf
                <div class="form-group" style="margin-bottom:12px">
                    <label
                        style="display:block;font-size:12px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Soal</label>
                    <textarea name="soal" required rows="3" placeholder="Tulis soal..."
                        style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);resize:vertical"></textarea>
                </div>
                <div style="display:flex;gap:8px;margin-bottom:12px">
                    <div class="form-group" style="flex:1">
                        <label
                            style="display:block;font-size:12px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tipe</label>
                        <select name="tipe" x-model="tipeSoal" class="form-select"
                            style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);background:var(--white)">
                            <option value="pg">Pilihan Ganda</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1">
                        <label
                            style="display:block;font-size:12px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Bobot</label>
                        <input type="number" name="bobot" value="1" min="1" max="100"
                            style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)">
                    </div>
                </div>
                <div x-show="selectedWorkbookTab == 'soal'" x-transition>
                    <div x-show="tipeSoal == 'pg'"
                        style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
                        <div><label style="font-size:11px;color:var(--gray-400)">A</label><input type="text"
                                name="pilihan_a" placeholder="Pilihan A"
                                style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)">
                        </div>
                        <div><label style="font-size:11px;color:var(--gray-400)">B</label><input type="text"
                                name="pilihan_b" placeholder="Pilihan B"
                                style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)">
                        </div>
                        <div><label style="font-size:11px;color:var(--gray-400)">C</label><input type="text"
                                name="pilihan_c" placeholder="Pilihan C"
                                style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)">
                        </div>
                        <div><label style="font-size:11px;color:var(--gray-400)">D</label><input type="text"
                                name="pilihan_d" placeholder="Pilihan D"
                                style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)">
                        </div>
                    </div>
                    <div x-show="tipeSoal == 'pg'" class="form-group" style="margin-bottom:12px">
                        <label
                            style="display:block;font-size:12px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Jawaban
                            Benar</label>
                        <select name="jawaban_benar" class="form-select"
                            style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font);background:var(--white)">
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-login"
                    style="cursor:pointer;border:none;padding:8px 18px;font-size:13px"><i class="fas fa-save"></i>
                    Simpan Soal</button>
            </form>
            @if ($wb->soals->count() > 0)
                <div style="margin-top:16px">
                    <h4 style="font-size:13px;font-weight:600;margin-bottom:8px">Daftar Soal
                        ({{ $wb->soals->count() }})</h4>
                    @foreach ($wb->soals as $soal)
                        <div
                            style="background:var(--gray-100);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:6px;font-size:13px">
                            <strong>#{{ $soal->nomor }}</strong>
                            {{ \Illuminate\Support\Str::limit($soal->soal, 100) }}
                            <span class="badge {{ $soal->tipe == 'pg' ? 'blue' : 'orange' }} light"
                                style="margin-left:8px;font-size:10px">{{ $soal->tipe }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endforeach
@if (session('success'))
    <div
        style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
