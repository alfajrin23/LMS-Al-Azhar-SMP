@php
    $materiList = $materiList ?? collect();
    $mapelList = $materiMapelList ?? collect();
    $kelasList = $materiKelasList ?? collect();
    $categories = [
        'kompetensi_inti' => 'Kompetensi Inti',
        'kompetensi_dasar' => 'Kompetensi Dasar',
        'alur_tahapan_pembelajaran' => 'Alur Tahapan Pembelajaran',
    ];
    $statusMeta = [
        'draft' => ['label' => 'Draft', 'class' => 'gray'],
        'pending' => ['label' => 'Menunggu Approval', 'class' => 'orange'],
        'approved' => ['label' => 'Disetujui', 'class' => 'green'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'red'],
        'revision_requested' => ['label' => 'Perlu Revisi', 'class' => 'orange'],
    ];
    $fileIcon = fn($path) => match (true) {
        str_ends_with(strtolower($path ?? ''), '.pdf') => 'fa-file-pdf',
        str_ends_with(strtolower($path ?? ''), '.doc') || str_ends_with(strtolower($path ?? ''), '.docx') => 'fa-file-word',
        str_ends_with(strtolower($path ?? ''), '.xls') || str_ends_with(strtolower($path ?? ''), '.xlsx') => 'fa-file-excel',
        str_ends_with(strtolower($path ?? ''), '.ppt') || str_ends_with(strtolower($path ?? ''), '.pptx') => 'fa-file-powerpoint',
        str_ends_with(strtolower($path ?? ''), '.zip') || str_ends_with(strtolower($path ?? ''), '.rar') => 'fa-file-archive',
        default => 'fa-file',
    };
@endphp

<div x-data="{
    activeCategory: 'kompetensi_inti',
    historyId: null,
    isEdit: false,
    actionUrl: '{{ route('guru.materi.store') }}',
    form: {
        judul: '',
        kategori: 'kompetensi_inti',
        kode: '',
        mapel_id: '{{ $mapelList->first()?->id }}',
        kelas_id: '{{ $kelasList->first()?->id }}',
        tahun_ajaran: '2026/2027',
        semester: 'Ganjil',
        deskripsi: '',
        isi: ''
    },
    edit(item) {
        this.isEdit = true;
        this.actionUrl = '/guru/materi/' + item.id + '/update';
        this.form = {
            judul: item.judul || '',
            kategori: item.kategori || 'kompetensi_inti',
            kode: item.kode || '',
            mapel_id: item.mapel_id || '',
            kelas_id: item.kelas_id || '',
            tahun_ajaran: item.tahun_ajaran || '2026/2027',
            semester: item.semester || 'Ganjil',
            deskripsi: item.deskripsi || '',
            isi: item.isi || ''
        };
        this.activeCategory = this.form.kategori;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    reset() {
        this.isEdit = false;
        this.actionUrl = '{{ route('guru.materi.store') }}';
        this.form = {
            judul: '',
            kategori: this.activeCategory,
            kode: '',
            mapel_id: '{{ $mapelList->first()?->id }}',
            kelas_id: '{{ $kelasList->first()?->id }}',
            tahun_ajaran: '2026/2027',
            semester: 'Ganjil',
            deskripsi: '',
            isi: ''
        };
    }
}">
    <div class="content-header">
        <div>
            <h1>Bahan Ajar</h1>
            <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Kompetensi Inti, Kompetensi Dasar, dan Alur Tahapan Pembelajaran</p>
        </div>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
        </div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        @foreach($categories as $key => $label)
            <button type="button" @click="activeCategory='{{ $key }}'; form.kategori='{{ $key }}'"
                :class="activeCategory === '{{ $key }}' ? 'btn-tab-active' : 'btn-tab-inactive'"
                style="padding:8px 12px;border:none;border-radius:6px;font-weight:700;cursor:pointer">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <style>
        .btn-tab-active { background: var(--blue); color: #fff; }
        .btn-tab-inactive { background: var(--gray-100); color: var(--gray-500); }
        .bahan-actions { display:flex; gap:5px; flex-wrap:wrap; }
        .bahan-actions form { display:inline; }
    </style>

    <div class="grid-2" style="margin-bottom:20px">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas" :class="isEdit ? 'fa-edit' : 'fa-plus-circle'" style="color:var(--teal)"></i> <span x-text="isEdit ? 'Edit Bahan Ajar' : 'Tambah Bahan Ajar'"></span></h3>
            </div>
            <form method="POST" :action="actionUrl" enctype="multipart/form-data" style="padding:4px 0">
                @csrf
                <input type="hidden" name="kategori" x-model="form.kategori">
                <div class="grid-2" style="gap:10px;margin-bottom:12px">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Judul</label>
                        <input type="text" name="judul" x-model="form.judul" required style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Kode / Nomor</label>
                        <input type="text" name="kode" x-model="form.kode" placeholder="Contoh: KI-01" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                    </div>
                </div>
                <div class="grid-2" style="gap:10px;margin-bottom:12px">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Mata Pelajaran</label>
                        <select name="mapel_id" x-model="form.mapel_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                            <option value="">Pilih Mapel</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Kelas</label>
                        <select name="kelas_id" x-model="form.kelas_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid-2" style="gap:10px;margin-bottom:12px">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" x-model="form.tahun_ajaran" required style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Semester</label>
                        <select name="semester" x-model="form.semester" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" x-model="form.deskripsi" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical"></textarea>
                </div>
                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">Isi / Ringkasan Kompetensi</label>
                    <textarea name="isi" rows="4" x-model="form.isi" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical"></textarea>
                </div>
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--gray-500);margin-bottom:4px">File</label>
                    <input type="file" name="file" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                    <small x-show="isEdit" style="display:block;margin-top:4px;color:var(--gray-400)">Biarkan kosong jika file tidak diganti.</small>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button type="submit" name="submit_action" value="draft" class="btn-small outline" style="cursor:pointer"><i class="fas fa-save"></i> Simpan Draft</button>
                    <button type="submit" name="submit_action" value="request_approval" class="btn-small teal" style="border:none;cursor:pointer"><i class="fas fa-paper-plane"></i> Request Approval</button>
                    <button type="button" x-show="isEdit" @click="reset" class="btn-small outline" style="cursor:pointer"><i class="fas fa-times"></i> Batal</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header"><h3><i class="fas fa-route" style="color:var(--purple)"></i> Alur Approval</h3></div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;color:var(--gray-600)">
                <div><strong>1. Draft</strong><br><span>Guru dapat menyimpan, mengedit, atau menghapus.</span></div>
                <div><strong>2. Request Approval</strong><br><span>Aksi eksplisit mengubah status menjadi pending untuk Kepala Sekolah.</span></div>
                <div><strong>3. Review</strong><br><span>Kepala Sekolah menyetujui, menolak, atau meminta revisi dengan catatan.</span></div>
                <div><strong>4. Published</strong><br><span>Siswa dan orang tua hanya dapat mengunduh file berstatus approved sesuai kelas.</span></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-folder-open" style="color:var(--teal)"></i> Daftar Bahan Ajar</h3>
            <span class="badge light blue">{{ $materiList->count() }} dokumen</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Mapel / Kelas</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materiList as $m)
                        @php $meta = $statusMeta[$m->status] ?? ['label' => ucfirst($m->status), 'class' => 'gray']; @endphp
                        <tr x-show="activeCategory === '{{ $m->kategori }}'">
                            <td style="text-align:center;font-size:20px;color:var(--teal)"><i class="fas {{ $fileIcon($m->file_path) }}"></i></td>
                            <td>
                                <strong>{{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->judul }}</strong>
                                <div style="font-size:12px;color:var(--gray-400);margin-top:4px">{{ \Illuminate\Support\Str::limit($m->deskripsi, 90) }}</div>
                            </td>
                            <td>{{ $categories[$m->kategori] ?? $m->kategori }}</td>
                            <td>
                                <div>{{ $m->mapel->nama_mapel ?? '-' }}</div>
                                <small style="color:var(--gray-400)">{{ $m->kelas->nama_kelas ?? '-' }}</small>
                            </td>
                            <td>{{ $m->tahun_ajaran }}<br><small style="color:var(--gray-400)">{{ $m->semester }}</small></td>
                            <td>
                                <span class="badge light {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                @if($m->catatan_reviewer)
                                    <div style="font-size:11px;color:var(--gray-500);margin-top:4px">{{ \Illuminate\Support\Str::limit($m->catatan_reviewer, 70) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="bahan-actions">
                                    @if($m->file_path)
                                        <a href="{{ route('materi.download', $m) }}" class="btn-small outline" title="Download" style="text-decoration:none"><i class="fas fa-download"></i></a>
                                    @endif
                                    @if(in_array($m->status, ['draft', 'rejected', 'revision_requested'], true))
                                        <button type="button" @click="edit(@js($m->only(['id','judul','kategori','kode','mapel_id','kelas_id','tahun_ajaran','semester','deskripsi','isi'])))" class="btn-small outline" style="cursor:pointer"><i class="fas fa-edit"></i></button>
                                        <form method="POST" action="{{ route('guru.materi.request-approval', $m) }}">
                                            @csrf
                                            <button type="submit" class="btn-small teal" style="border:none;cursor:pointer"><i class="fas fa-paper-plane"></i></button>
                                        </form>
                                    @endif
                                    @if($m->status === 'pending')
                                        <form method="POST" action="{{ route('guru.materi.cancel-approval', $m) }}">
                                            @csrf
                                            <button type="submit" class="btn-small outline" style="cursor:pointer"><i class="fas fa-ban"></i></button>
                                        </form>
                                    @endif
                                    @if($m->status === 'draft')
                                        <form method="POST" action="{{ route('guru.materi.delete', $m) }}" onsubmit="return confirm('Hapus draft Bahan Ajar ini?')">
                                            @csrf
                                            <button type="submit" class="btn-small outline" style="border-color:var(--red);color:var(--red);cursor:pointer"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                    <button type="button" @click="historyId = historyId === {{ $m->id }} ? null : {{ $m->id }}" class="btn-small outline" style="cursor:pointer"><i class="fas fa-history"></i></button>
                                </div>
                                <div x-show="historyId === {{ $m->id }}" x-cloak style="margin-top:8px;font-size:11px;color:var(--gray-500);line-height:1.5">
                                    @forelse($m->approvalHistories as $history)
                                        <div>{{ $history->created_at->format('d M Y H:i') }} - {{ $history->actor->name ?? 'Sistem' }}: {{ $history->action }} ({{ $history->status_to }})</div>
                                    @empty
                                        <div>Belum ada riwayat approval.</div>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada Bahan Ajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
