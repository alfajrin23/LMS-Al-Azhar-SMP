@php
    $materiList = \App\Models\Materi::where('guru_id', $guru->id)
        ->with('mapel', 'kelas')
        ->orderBy('created_at', 'desc')
        ->get();
    $jadwalGuru = \App\Models\Jadwal::where('guru_id', $guru->id)->get();
    $mapelList = \App\Models\Mapel::akademik()
        ->whereIn('id', $jadwalGuru->pluck('mapel_id')->merge($guru->mapels->pluck('id'))->unique())
        ->get();
    $kelasList = \App\Models\Kelas::whereIn('id', $jadwalGuru->pluck('kelas_id')->unique())->get();
    $fileIcon = fn($path) => match (true) {
        str_ends_with($path, '.pdf') => 'fa-file-pdf',
        str_ends_with($path, '.doc') || str_ends_with($path, '.docx') => 'fa-file-word',
        str_ends_with($path, '.xls') || str_ends_with($path, '.xlsx') => 'fa-file-excel',
        str_ends_with($path, '.ppt') || str_ends_with($path, '.pptx') => 'fa-file-powerpoint',
        str_ends_with($path, '.zip') || str_ends_with($path, '.rar') => 'fa-file-archive',
        str_ends_with($path, '.jpg') || str_ends_with($path, '.jpeg') || str_ends_with($path, '.png')
            => 'fa-file-image',
        str_ends_with($path, '.mp4') => 'fa-file-video',
        str_ends_with($path, '.mp3') => 'fa-file-audio',
        default => 'fa-file',
    };
@endphp

<div x-data="{
    isEdit: false,
    actionUrl: '{{ route('guru.materi.store') }}',
    judul: '',
    mapelId: '{{ $guru->mapels->first()?->id }}',
    kelasId: '',
    tipe: 'materi',
    deskripsi: '',
    setEdit(materi) {
        this.isEdit = true;
        this.actionUrl = '/guru/materi/' + materi.id + '/update';
        this.judul = materi.judul;
        this.mapelId = materi.mapel_id;
        this.kelasId = materi.kelas_id || '';
        this.tipe = materi.tipe;
        this.deskripsi = materi.deskripsi || '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    setCreate() {
        this.isEdit = false;
        this.actionUrl = '{{ route('guru.materi.store') }}';
        this.judul = '';
        this.mapelId = '{{ $guru->mapels->first()?->id }}';
        this.kelasId = '';
        this.tipe = 'materi';
        this.deskripsi = '';
    }
}">

    <div class="content-header">
        <h1>Kelola &amp; Upload Materi</h1>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom:20px">
        <!-- Form Upload / Edit -->
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fas" :class="isEdit ? 'fa-edit' : 'fa-upload'" style="color:var(--teal)"></i>
                    <span x-text="isEdit ? 'Edit Materi Ajar' : 'Upload Materi Baru'"></span>
                </h3>
            </div>
            <form method="POST" :action="actionUrl" enctype="multipart/form-data" style="padding:4px 0">
                @csrf
                <div class="form-group" style="margin-bottom:14px">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Judul</label>
                    <input type="text" name="judul" required placeholder="Contoh: Bab 3 Aljabar Linear"
                        x-model="judul"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
                </div>
                <div style="display:flex;gap:10px;margin-bottom:14px">
                    <div class="form-group" style="flex:1">
                        <label
                            style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Mata
                            Pelajaran</label>
                        <select name="mapel_id" required class="form-select" x-model="mapelId"
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
                            (Wajib untuk kelengkapan)</label>
                        <select name="kelas_id" class="form-select" x-model="kelasId"
                            style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-bottom:14px">
                    <div class="form-group" style="flex:1">
                        <label
                            style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tipe
                            Berkas</label>
                        <select name="tipe" class="form-select" x-model="tipe"
                            style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                            <option value="materi">Materi Ajar</option>
                            <option value="referensi">Referensi</option>
                            <option value="tugas">Tugas</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1">
                        <label
                            style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Berkas
                            File</label>
                        <input type="file" name="file" :required="!isEdit"
                            style="width:100%;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <small x-show="isEdit"
                            style="color:var(--gray-400);font-size:11px;display:block;margin-top:2px">Biarkan kosong
                            jika tidak ingin mengganti berkas file.</small>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:16px">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Deskripsi
                        (Wajib min. 20 karakter untuk kelengkapan)</label>
                    <textarea name="deskripsi" rows="3" placeholder="Tuliskan ringkasan materi atau petunjuk belajar siswa di sini..."
                        x-model="deskripsi"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
                    <small style="color:var(--gray-400);font-size:11px;display:block;margin-top:2px"
                        :style="deskripsi.length >= 20 ? 'color:var(--green)' : 'color:var(--red)'">
                        Panjang karakter: <span x-text="deskripsi.length"></span> / 20 minimum.
                    </small>
                </div>
                <button type="submit" class="btn-login" style="cursor:pointer;border:none">
                    <i class="fas" :class="isEdit ? 'fa-save' : 'fa-upload'"></i>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Upload & Ajukan'"></span>
                </button>
                <button type="button" x-show="isEdit" @click="setCreate" class="btn-login"
                    style="background:var(--gray-300); color:var(--text); border:none; cursor:pointer; margin-left:8px">Batal</button>
            </form>
        </div>

        <!-- Indikator Checklist Syarat -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-clipboard-check" style="color:var(--teal)"></i> Syarat Kelengkapan Materi</h3>
            </div>
            <div style="padding:10px 0">
                <p style="font-size:13px;line-height:1.6;color:var(--gray-600);margin-bottom:16px">
                    Agar materi ajar dapat diterbitkan dan diunduh oleh siswa, pastikan semua kolom berikut lengkap
                    untuk memicu pengajuan persetujuan ke Admin:
                </p>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--gray-100);border-radius:var(--radius-sm)">
                        <span style="font-size:13px;font-weight:600"><i class="fas fa-heading"
                                style="color:var(--blue);margin-right:6px"></i> Judul Terisi</span>
                        <i class="fas fa-check-circle" style="color:var(--green)"></i>
                    </div>
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--gray-100);border-radius:var(--radius-sm)">
                        <span style="font-size:13px;font-weight:600"><i class="fas fa-file-upload"
                                style="color:var(--teal);margin-right:6px"></i> Berkas File Terlampir</span>
                        <i class="fas fa-check-circle" style="color:var(--green)"></i>
                    </div>
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--gray-100);border-radius:var(--radius-sm)">
                        <span style="font-size:13px;font-weight:600"><i class="fas fa-book"
                                style="color:var(--purple);margin-right:6px"></i> Mata Pelajaran Terpilih</span>
                        <i class="fas fa-check-circle" style="color:var(--green)"></i>
                    </div>
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--gray-100);border-radius:var(--radius-sm)">
                        <span style="font-size:13px;font-weight:600"><i class="fas fa-users"
                                style="color:var(--orange);margin-right:6px"></i> Kelas Target Ditentukan</span>
                        <span class="badge orange light" style="font-size:10px;font-weight:700">Wajib</span>
                    </div>
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--gray-100);border-radius:var(--radius-sm)">
                        <span style="font-size:13px;font-weight:600"><i class="fas fa-align-left"
                                style="color:var(--indigo);margin-right:6px"></i> Deskripsi Minimal 20 Karakter</span>
                        <span class="badge orange light" style="font-size:10px;font-weight:700">Wajib</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Daftar Materi -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-folder-open" style="color:var(--teal)"></i> Daftar Materi &amp; Kelengkapan Syarat
            </h3>
            <select x-model="filterTipeVal" class="form-select"
                style="padding:5px 10px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                <option value="">Semua Tipe</option>
                <option value="materi">Materi Ajar</option>
                <option value="referensi">Referensi</option>
                <option value="tugas">Tugas</option>
            </select>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">Format</th>
                        <th>Judul &amp; Deskripsi</th>
                        <th>Mapel / Kelas</th>
                        <th>Syarat Kelengkapan</th>
                        <th>Status</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materiList as $m)
                        @php $chk = $m->checklist(); @endphp
                        <tr x-show="!filterTipeVal || '{{ $m->tipe }}' === filterTipeVal">
                            <td style="text-align:center;font-size:22px;color:var(--teal)"><i
                                    class="fas {{ $fileIcon($m->file_path) }}"></i></td>
                            <td>
                                <strong style="font-size:14px;color:var(--gray-800)">{{ $m->judul }}</strong>
                                <p
                                    style="font-size:12px;color:var(--gray-500);margin-top:4px;max-width:350px;line-height:1.4">
                                    {{ $m->deskripsi ?? '— Tidak ada deskripsi —' }}</p>
                            </td>
                            <td>
                                <div><strong>{{ $m->mapel->kode ?? '-' }}</strong></div>
                                <div style="font-size:11px;color:var(--gray-400);margin-top:2px">
                                    {{ $m->kelas->nama_kelas ?? 'Semua Kelas' }}</div>
                            </td>
                            <td>
                                <div
                                    style="display:flex;flex-direction:column;gap:4px;font-size:11px;color:var(--gray-600)">
                                    <span style="color:{{ $chk['judul'] ? 'var(--green)' : 'var(--red)' }}">
                                        <i
                                            class="fas {{ $chk['judul'] ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        Judul
                                    </span>
                                    <span style="color:{{ $chk['file'] ? 'var(--green)' : 'var(--red)' }}">
                                        <i class="fas {{ $chk['file'] ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        File
                                    </span>
                                    <span style="color:{{ $chk['kelas'] ? 'var(--green)' : 'var(--red)' }}">
                                        <i
                                            class="fas {{ $chk['kelas'] ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        Kelas
                                    </span>
                                    <span style="color:{{ $chk['deskripsi'] ? 'var(--green)' : 'var(--red)' }}">
                                        <i
                                            class="fas {{ $chk['deskripsi'] ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        Deskripsi
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="badge light {{ $m->status === 'approved' ? 'green' : ($m->status === 'pending' ? 'orange' : ($m->status === 'rejected' ? 'red' : 'gray')) }}">
                                    {{ $m->status === 'approved' ? 'Disetujui' : ($m->status === 'pending' ? 'Menunggu Review' : ($m->status === 'rejected' ? 'Ditolak' : 'Draft (Belum Lengkap)')) }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:4px;align-items:center">
                                    <a href="{{ route('materi.download', $m->id) }}" target="_blank"
                                        class="btn-small outline" style="text-decoration:none;padding:5px 8px"><i
                                            class="fas fa-download"></i></a>
                                    <button type="button" @click="setEdit({{ json_encode($m) }})"
                                        class="btn-small outline" style="padding:5px 8px;cursor:pointer"><i
                                            class="fas fa-edit"></i></button>
                                    <form method="POST" action="{{ route('guru.materi.delete', $m->id) }}"
                                        style="display:inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                        @csrf
                                        <button type="submit" class="btn-small outline"
                                            style="border-color:var(--red);color:var(--red);padding:5px 8px;cursor:pointer"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada
                                materi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@if (session('success'))
    <div
        style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div
        style="position:fixed;bottom:20px;right:20px;background:var(--red);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
