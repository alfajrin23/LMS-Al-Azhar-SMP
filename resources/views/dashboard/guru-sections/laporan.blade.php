@php
    $kelasOptions = $materiKelasList ?? collect();
    $mapelOptions = $materiMapelList ?? collect();
    $siswaOptions = $siswaDiajar ?? collect();
    $administrasiDocs = [
        'Cover',
        'Kalender Pendidikan',
        'Silabus',
        'Indikator Pencapaian Kinerja',
        'Analisis Keterkaitan KI dan KD',
        'Pemetaan Kompetensi dan Teknik Penilaian',
        'Penentuan KKM',
        'Analisis Kompetensi',
        'Program Tahunan',
        'Program Semester 1',
        'RPP Semester 1',
        'Program Semester 2',
        'RPP Semester 2',
        'Remedial dan Pengayaan',
        'Analisis Pencapaian Kompetensi Remedial dan Pengayaan',
        'Penilaian Keterampilan',
    ];
    $filledChecklist = ($adminChecklistList ?? collect())->keyBy('dokumen');
@endphp

<div x-data="{ activeJurnal: 'harian' }">
    <div class="content-header">
        <div>
            <h1>Jurnal Mengajar</h1>
            <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Administrasi guru berdasarkan Agenda Guru dan Buku Administrasi Guru</p>
        </div>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
        </div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        @foreach([
            'harian' => 'Jurnal Harian Mengajar',
            'hadir' => 'Daftar Hadir',
            'nilai' => 'Daftar Nilai',
            'quran' => 'Capaian Tahsin/Tahfizh',
            'sikap' => 'Jurnal Sikap',
            'pengayaan' => 'Program Pengayaan',
            'remedial' => 'Program Remedial',
            'administrasi' => 'Kelengkapan Administrasi',
        ] as $key => $label)
            <button type="button" @click="activeJurnal='{{ $key }}'"
                :class="activeJurnal === '{{ $key }}' ? 'btn-tab-active' : 'btn-tab-inactive'"
                style="padding:8px 10px;border:none;border-radius:6px;font-weight:700;cursor:pointer;font-size:12px">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <style>
        .btn-tab-active { background: var(--blue); color: #fff; }
        .btn-tab-inactive { background: var(--gray-100); color: var(--gray-500); }
        .jurnal-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        @media (max-width:720px) { .jurnal-form-grid { grid-template-columns:1fr; } }
    </style>

    <div x-show="activeJurnal === 'harian'">
        <div class="grid-2" style="margin-bottom:20px">
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-edit" style="color:var(--teal)"></i> Form Jurnal Harian</h3></div>
                <form method="POST" action="{{ route('guru.jurnal.harian.store') }}">
                    @csrf
                    <div class="jurnal-form-grid">
                        <select name="kelas_id" required class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasOptions as $k)<option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>@endforeach
                        </select>
                        <select name="mapel_id" required class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                            <option value="">Pilih Mapel</option>
                            @foreach($mapelOptions as $m)<option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>@endforeach
                        </select>
                        <input type="text" name="hari" required placeholder="Hari" value="{{ now()->locale('id')->dayName }}" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <input type="date" name="tanggal" required value="{{ now()->format('Y-m-d') }}" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <input type="text" name="jam_ke" required placeholder="Jam ke / sesi" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <select name="semester" class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white"><option>Ganjil</option><option>Genap</option></select>
                    </div>
                    <input type="hidden" name="tahun_ajaran" value="2026/2027">
                    <textarea name="bahasan_materi" required rows="3" placeholder="Bahasan materi" style="width:100%;margin-top:10px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical"></textarea>
                    <textarea name="keterangan" rows="2" placeholder="Keterangan" style="width:100%;margin-top:10px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical"></textarea>
                    <button class="btn-login" style="border:none;cursor:pointer;margin-top:12px"><i class="fas fa-save"></i> Simpan Jurnal Harian</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-history" style="color:var(--orange)"></i> Riwayat Harian</h3></div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Tanggal</th><th>Kelas</th><th>Mapel</th><th>Bahasan</th></tr></thead>
                        <tbody>
                            @forelse($jurnalHarianList ?? [] as $row)
                                <tr><td>{{ $row->tanggal->format('d M Y') }}</td><td>{{ $row->kelas->nama_kelas ?? '-' }}</td><td>{{ $row->mapel->nama_mapel ?? '-' }}</td><td>{{ $row->bahasan_materi }}</td></tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center;color:var(--gray-400);padding:16px">Belum ada jurnal harian.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="activeJurnal === 'hadir'" x-cloak class="card">
        <div class="card-header"><h3><i class="fas fa-clipboard-check" style="color:var(--teal)"></i> Daftar Hadir</h3></div>
        <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px">Daftar hadir memakai tabel kehadiran yang sama dengan menu Absensi Siswa.</p>
        <button type="button" @click="tab='absensi'" class="btn-small teal" style="border:none;cursor:pointer"><i class="fas fa-arrow-right"></i> Buka Input Daftar Hadir</button>
    </div>

    <div x-show="activeJurnal === 'nilai'" x-cloak class="card">
        <div class="card-header"><h3><i class="fas fa-table" style="color:var(--blue)"></i> Daftar Nilai</h3></div>
        <p style="font-size:13px;color:var(--gray-500)">Format operasional mengikuti Tugas 1-8, Sumatif 1-8, dan SAT/nilai akhir. Input nilai detail tersimpan pada tabel nilai dan snapshot rapor.</p>
        <button type="button" @click="tab='nilai'" class="btn-small teal" style="border:none;cursor:pointer;margin-top:10px"><i class="fas fa-arrow-right"></i> Buka Input Nilai</button>
    </div>

    <div x-show="activeJurnal === 'quran'" x-cloak class="card">
        <div class="card-header"><h3><i class="fas fa-quran" style="color:var(--teal)"></i> Capaian Tahsin/Tahfizh</h3></div>
        <p style="font-size:13px;color:var(--gray-500)">Capaian Quran memakai data tahsin_setorans, tahfidz_setoran, tahfidz_ayat_nilai, dan tahfidz_progress.</p>
        <button type="button" @click="tab='tahfidz'" class="btn-small teal" style="border:none;cursor:pointer;margin-top:10px"><i class="fas fa-arrow-right"></i> Buka Tahfidz</button>
    </div>

    <div x-show="activeJurnal === 'sikap'" x-cloak>
        <div class="grid-2">
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-star" style="color:var(--orange)"></i> Form Jurnal Sikap</h3></div>
                <form method="POST" action="{{ route('guru.jurnal.sikap.store') }}">
                    @csrf
                    <select name="siswa_id" required class="form-select" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white;margin-bottom:10px">
                        <option value="">Pilih Siswa</option>
                        @foreach($siswaOptions as $s)<option value="{{ $s->id }}">{{ $s->nama }} - {{ $s->kelas->nama_kelas ?? '-' }}</option>@endforeach
                    </select>
                    <input type="date" name="tanggal" required value="{{ now()->format('Y-m-d') }}" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:10px">
                    <textarea name="kejadian" required rows="3" placeholder="Kejadian" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical;margin-bottom:10px"></textarea>
                    <textarea name="tindakan" rows="3" placeholder="Tindakan" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical;margin-bottom:10px"></textarea>
                    <input type="text" name="paraf" placeholder="Paraf" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:10px">
                    <button class="btn-login" style="border:none;cursor:pointer"><i class="fas fa-save"></i> Simpan Jurnal Sikap</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-history" style="color:var(--blue)"></i> Riwayat Sikap</h3></div>
                @forelse($jurnalSikapList ?? [] as $row)
                    <div style="padding:9px 0;border-bottom:1px solid var(--border-light)"><strong>{{ $row->siswa->nama ?? '-' }}</strong><div style="font-size:12px;color:var(--gray-500)">{{ $row->tanggal->format('d M Y') }} - {{ $row->kejadian }}</div></div>
                @empty
                    <p style="color:var(--gray-400);font-size:13px">Belum ada jurnal sikap.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div x-show="activeJurnal === 'pengayaan'" x-cloak>
        <div class="grid-2">
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-seedling" style="color:var(--teal)"></i> Program Pengayaan</h3></div>
                <form method="POST" action="{{ route('guru.jurnal.pengayaan.store') }}">
                    @csrf
                    <div class="jurnal-form-grid">
                        <select name="kelas_id" required class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">@foreach($kelasOptions as $k)<option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>@endforeach</select>
                        <select name="mapel_id" required class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">@foreach($mapelOptions as $m)<option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>@endforeach</select>
                    </div>
                    <input type="text" name="kompetensi_dasar" placeholder="Kompetensi dasar" style="width:100%;margin-top:10px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                    <textarea name="materi" required rows="2" placeholder="Materi" style="width:100%;margin-top:10px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical"></textarea>
                    <textarea name="bentuk_pengayaan" required rows="2" placeholder="Bentuk pengayaan" style="width:100%;margin-top:10px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical"></textarea>
                    <input type="text" name="keterangan" placeholder="Keterangan" style="width:100%;margin-top:10px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                    <button class="btn-login" style="border:none;cursor:pointer;margin-top:12px"><i class="fas fa-save"></i> Simpan Pengayaan</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-history" style="color:var(--blue)"></i> Riwayat Pengayaan</h3></div>
                @forelse($programPengayaanList ?? [] as $row)
                    <div style="padding:9px 0;border-bottom:1px solid var(--border-light)"><strong>{{ $row->mapel->nama_mapel ?? '-' }} - {{ $row->kelas->nama_kelas ?? '-' }}</strong><div style="font-size:12px;color:var(--gray-500)">{{ $row->materi }}</div></div>
                @empty
                    <p style="color:var(--gray-400);font-size:13px">Belum ada program pengayaan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div x-show="activeJurnal === 'remedial'" x-cloak>
        <div class="grid-2">
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-tools" style="color:var(--orange)"></i> Program Remedial</h3></div>
                <form method="POST" action="{{ route('guru.jurnal.remedial.store') }}">
                    @csrf
                    <select name="siswa_id" required class="form-select" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white;margin-bottom:10px">@foreach($siswaOptions as $s)<option value="{{ $s->id }}">{{ $s->nama }} - {{ $s->kelas->nama_kelas ?? '-' }}</option>@endforeach</select>
                    <select name="mapel_id" required class="form-select" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white;margin-bottom:10px">@foreach($mapelOptions as $m)<option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>@endforeach</select>
                    <input type="text" name="kompetensi_dasar" placeholder="Kompetensi dasar" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:10px">
                    <textarea name="materi" required rows="2" placeholder="Materi" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);resize:vertical;margin-bottom:10px"></textarea>
                    <div class="jurnal-form-grid">
                        <input type="number" name="nilai_sebelum" min="0" max="100" placeholder="Nilai sebelum" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <input type="number" name="nilai_sesudah" min="0" max="100" placeholder="Nilai sesudah" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                    </div>
                    <input type="text" name="keterangan" placeholder="Keterangan" style="width:100%;margin-top:10px;padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                    <button class="btn-login" style="border:none;cursor:pointer;margin-top:12px"><i class="fas fa-save"></i> Simpan Remedial</button>
                </form>
            </div>
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-history" style="color:var(--blue)"></i> Riwayat Remedial</h3></div>
                @forelse($programRemedialList ?? [] as $row)
                    <div style="padding:9px 0;border-bottom:1px solid var(--border-light)"><strong>{{ $row->siswa->nama ?? '-' }} - {{ $row->mapel->nama_mapel ?? '-' }}</strong><div style="font-size:12px;color:var(--gray-500)">Sebelum {{ $row->nilai_sebelum ?? '-' }}, sesudah {{ $row->nilai_sesudah ?? '-' }} - {{ $row->status }}</div></div>
                @empty
                    <p style="color:var(--gray-400);font-size:13px">Belum ada program remedial.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div x-show="activeJurnal === 'administrasi'" x-cloak class="card">
        <div class="card-header"><h3><i class="fas fa-clipboard-list" style="color:var(--teal)"></i> Kelengkapan Administrasi</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Dokumen</th><th>Status</th><th>Tanggal Lengkap</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($administrasiDocs as $dokumen)
                        @php $row = $filledChecklist->get($dokumen); @endphp
                        <tr>
                            <td>{{ $dokumen }}</td>
                            <td><span class="badge light {{ ($row?->status ?? 'belum_lengkap') === 'terverifikasi' ? 'green' : (($row?->status ?? '') === 'lengkap' ? 'blue' : 'orange') }}">{{ str_replace('_', ' ', $row?->status ?? 'belum_lengkap') }}</span></td>
                            <td>{{ $row?->tanggal_dilengkapi?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('guru.jurnal.administrasi.store') }}" style="display:flex;gap:6px;flex-wrap:wrap">
                                    @csrf
                                    <input type="hidden" name="dokumen" value="{{ $dokumen }}">
                                    <input type="hidden" name="status" value="lengkap">
                                    <input type="date" name="tanggal_dilengkapi" value="{{ now()->format('Y-m-d') }}" style="padding:5px;border:1px solid var(--border);border-radius:4px">
                                    <button class="btn-small outline" style="cursor:pointer"><i class="fas fa-check"></i> Tandai Lengkap</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
