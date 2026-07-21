@php
    $statusClass = function ($d) {
        $deadline = \Carbon\Carbon::parse($d);
        if ($deadline->isPast()) {
            return 'status-selesai';
        }
        if ($deadline->diffInDays(now()) <= 3) {
            return 'status-mendekati';
        }
        return 'teal';
    };
    $statusLabel = function ($d) {
        $deadline = \Carbon\Carbon::parse($d);
        if ($deadline->isPast()) {
            return 'Selesai';
        }
        if ($deadline->diffInDays(now()) <= 3) {
            return 'Mendekati';
        }
        return 'Aktif';
    };
    $jadwalGuru = \App\Models\Jadwal::where('guru_id', $guru->id)->get();
    $mapelList = \App\Models\Mapel::akademik()->whereIn('id', $jadwalGuru->pluck('mapel_id')->unique())->get();
    $kelasList = \App\Models\Kelas::whereIn('id', $jadwalGuru->pluck('kelas_id')->unique())->get();
    $pengumpulanTugas = \App\Models\PengumpulanTugas::with('siswa', 'tugas.mapel')
        ->whereHas('tugas', fn($q) => $q->where('guru_id', $guru->id))
        ->get()
        ->groupBy('tugas_id');
@endphp
<div class="content-header">
    <h1>Tugas &amp; Ulangan</h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-plus" style="color:var(--teal)"></i> Buat Tugas / Ulangan Baru</h3>
        </div>
        <form method="POST" action="/guru/tugas" enctype="multipart/form-data" style="padding:4px 0">
            @csrf
            <input type="hidden" name="tipe" x-model="tipeTugas">
            <div class="form-group" style="margin-bottom:14px">
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Judul</label>
                <input type="text" name="judul" required placeholder="Judul tugas/ulangan"
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
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Kelas</label>
                    <select name="kelas_id" required class="form-select"
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
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tipe</label>
                    <select x-model="tipeTugas" name="tipe" class="form-select"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <option value="tugas">Tugas</option>
                        <option value="ulangan">Ulangan</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Deadline</label>
                    <input type="date" name="tanggal_deadline" required
                        value="{{ now()->addDays(7)->format('Y-m-d') }}"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Deskripsi
                    (opsional)</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi tugas/ulangan..."
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label
                    style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Upload
                    File (opsional)</label>
                <input type="file" name="file"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png"
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                <p style="font-size:11px;color:var(--gray-400);margin-top:4px">PDF, DOC, XLS, PPT, ZIP, JPG, PNG. Maks
                    10MB.</p>
            </div>
            <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-save"></i>
                Simpan</button>
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Statistik</h3>
        </div>
        <div style="padding:4px 0">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Total Tugas</span>
                    <div style="font-size:22px;font-weight:700;color:var(--teal)">
                        {{ $tugas->where('tipe', 'tugas')->count() }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Total Ulangan</span>
                    <div style="font-size:22px;font-weight:700;color:var(--blue)">
                        {{ $tugas->where('tipe', 'ulangan')->count() }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Aktif</span>
                    <div style="font-size:22px;font-weight:700;color:var(--orange)">
                        {{ $tugas->filter(fn($t) => !\Carbon\Carbon::parse($t->tanggal_deadline)->isPast())->count() }}
                    </div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Selesai</span>
                    <div style="font-size:22px;font-weight:700;color:var(--green)">
                        {{ $tugas->filter(fn($t) => \Carbon\Carbon::parse($t->tanggal_deadline)->isPast())->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div x-data="{ selectedTugasId: null }">
    <div style="display:flex;gap:8px;margin-bottom:16px">
        <select x-model="filterKelasVal" class="form-select"
            style="padding:6px 12px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
            <option value="">Semua Kelas</option>
            @foreach ($kelasList as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
        <select x-model="filterTipeVal" class="form-select"
            style="padding:6px 12px;font-size:12px;border:1px solid var(--border);border-radius:var(--radius-sm)">
            <option value="">Semua Tipe</option>
            <option value="tugas">Tugas</option>
            <option value="ulangan">Ulangan</option>
        </select>
    </div>

    <div class="card" style="margin-bottom:16px">
        <div class="card-header">
            <h3><i class="fas fa-tasks" style="color:var(--orange)"></i> Tugas Aktif</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kelas</th>
                        <th>Deadline</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugas->where('tipe', 'tugas') as $t)
                        <tr x-show="!filterKelasVal || '{{ $t->kelas_id }}' === filterKelasVal">
                            <td><strong>{{ $t->judul }}</strong></td>
                            <td>{{ $t->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal_deadline)->format('d M Y') }}</td>
                            <td>
                                @if ($t->file_path)
                                    <a href="{{ route('guru.tugas.download', $t->id) }}" target="_blank"
                                        class="btn-small outline" style="text-decoration:none;font-size:11px"><i
                                            class="fas fa-download"></i></a>
                                @else
                                    <span style="color:var(--gray-400);font-size:11px">—</span>
                                @endif
                            </td>
                            <td><span
                                    class="badge {{ $statusClass($t->tanggal_deadline) }}">{{ $statusLabel($t->tanggal_deadline) }}</span>
                            </td>
                            <td>
                                <button
                                    @click="selectedTugasId = selectedTugasId === '{{ $t->id }}' ? null : '{{ $t->id }}'"
                                    class="btn-small outline"
                                    style="cursor:pointer;border:none;font-size:11px;padding:4px 10px;border-radius:var(--radius-sm);background:var(--blue);color:#fff">
                                    <i class="fas fa-eye"></i> Pengumpulan
                                    ({{ ($pengumpulanTugas[$t->id] ?? collect())->count() }})
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--gray-400)">Belum ada tugas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-pencil-alt" style="color:var(--purple)"></i> Ulangan Aktif</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Tanggal</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugas->where('tipe', 'ulangan') as $t)
                        <tr x-show="!filterKelasVal || '{{ $t->kelas_id }}' === filterKelasVal">
                            <td><strong>{{ $t->judul }}</strong></td>
                            <td>{{ $t->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal_deadline)->format('d M Y') }}</td>
                            <td>
                                @if ($t->file_path)
                                    <a href="{{ route('guru.tugas.download', $t->id) }}" target="_blank"
                                        class="btn-small outline" style="text-decoration:none;font-size:11px"><i
                                            class="fas fa-download"></i></a>
                                @else
                                    <span style="color:var(--gray-400);font-size:11px">—</span>
                                @endif
                            </td>
                            <td><span
                                    class="badge {{ $statusClass($t->tanggal_deadline) }}">{{ $statusLabel($t->tanggal_deadline) }}</span>
                            </td>
                            <td>
                                <button
                                    @click="selectedTugasId = selectedTugasId === '{{ $t->id }}' ? null : '{{ $t->id }}'"
                                    class="btn-small outline"
                                    style="cursor:pointer;border:none;font-size:11px;padding:4px 10px;border-radius:var(--radius-sm);background:var(--blue);color:#fff">
                                    <i class="fas fa-eye"></i> Pengumpulan
                                    ({{ ($pengumpulanTugas[$t->id] ?? collect())->count() }})
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;color:var(--gray-400)">Belum ada ulangan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Panel Detail Pengumpulan --}}
    <div x-show="selectedTugasId" class="card"
        style="margin-top:16px;border:2px solid var(--blue);border-radius:var(--radius-md)">
        @foreach ($tugas as $t)
            <div x-show="selectedTugasId === '{{ $t->id }}'">
                <div class="card-header"
                    style="border-bottom:1px solid var(--border-light);padding:12px 16px;display:flex;justify-content:space-between;align-items:center">
                    <h3 style="margin:0;font-size:15px"><i class="fas fa-users" style="color:var(--blue)"></i>
                        Pengumpulan: {{ $t->judul }}</h3>
                    <button @click="selectedTugasId = null"
                        style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--gray-400)">&times;</button>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>File</th>
                                <th>Catatan</th>
                                <th>Dikumpulkan</th>
                                <th>Nilai</th>
                                <th>Catatan Guru</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($pengumpulanTugas[$t->id] ?? collect()) as $p)
                                <tr>
                                    <td><strong>{{ $p->siswa->nama ?? '-' }}</strong></td>
                                    <td>
                                        @if ($p->file_path)
                                            <a href="{{ route('guru.tugas.pengumpulan.download', $p->id) }}" target="_blank"
                                                class="btn-small outline"
                                                style="text-decoration:none;font-size:11px"><i
                                                    class="fas fa-download"></i></a>
                                        @else
                                            <span style="color:var(--gray-400);font-size:11px">—</span>
                                        @endif
                                    </td>
                                    <td style="max-width:150px;font-size:12px">{{ $p->catatan_siswa ?? '-' }}</td>
                                    <td style="font-size:12px">
                                        {{ $p->dikumpulkan_at ? $p->dikumpulkan_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        @if ($p->nilai !== null)
                                            <span
                                                style="font-weight:700;color:var(--green)">{{ $p->nilai }}</span>
                                        @else
                                            <span style="color:var(--gray-400);font-size:11px">—</span>
                                        @endif
                                    </td>
                                    <td style="max-width:150px;font-size:12px">{{ $p->catatan_guru ?? '-' }}</td>
                                    <td>
                                        <button @click="$refs.nilaiForm{{ $p->id }}.classList.toggle('show')"
                                            class="btn-small"
                                            style="cursor:pointer;border:none;font-size:10px;padding:3px 8px;border-radius:var(--radius-sm);background:var(--orange);color:#fff">
                                            <i class="fas fa-star"></i> Nilai
                                        </button>
                                        <form method="POST" action="{{ route('guru.nilai-tugas.store') }}"
                                            x-ref="nilaiForm{{ $p->id }}"
                                            style="display:none;margin-top:8px;padding:8px;background:#f9f9f9;border-radius:var(--radius-sm);gap:6px;flex-direction:column"
                                            class="nilai-form" x-transition>
                                            @csrf
                                            <input type="hidden" name="pengumpulan_id" value="{{ $p->id }}">
                                            <input type="number" name="nilai" min="0" max="100"
                                                placeholder="0-100" required
                                                style="width:80px;padding:4px 6px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:12px;font-family:var(--font)">
                                            <input type="text" name="catatan_guru"
                                                placeholder="Catatan (opsional)"
                                                style="flex:1;padding:4px 6px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:12px;font-family:var(--font)">
                                            <button type="submit" class="btn-small"
                                                style="cursor:pointer;border:none;font-size:10px;padding:4px 8px;border-radius:var(--radius-sm);background:var(--green);color:#fff"><i
                                                    class="fas fa-save"></i> Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;color:var(--gray-400)">Belum ada
                                        pengumpulan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>

@if (session('success'))
    <div
        style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
