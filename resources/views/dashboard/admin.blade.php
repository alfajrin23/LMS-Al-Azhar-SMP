@extends('layouts.app')

@section('title', 'Dashboard Admin - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li :class="{ 'active': tab === 'dashboard' }" @click="tab = 'dashboard'">
        <label><i class="fas fa-th-large"></i> Dashboard</label>
    </li>
    <li :class="{ 'active': tab === 'siswa' }" @click="tab = 'siswa'">
        <label><i class="fas fa-user-graduate"></i> Siswa</label>
    </li>
    <li :class="{ 'active': tab === 'guru' }" @click="tab = 'guru'">
        <label><i class="fas fa-chalkboard-teacher"></i> Guru</label>
    </li>
    <li :class="{ 'active': tab === 'ortu' }" @click="tab = 'ortu'">
        <label><i class="fas fa-users"></i> Orang Tua</label>
    </li>
    <li :class="{ 'active': tab === 'kelas' }" @click="tab = 'kelas'">
        <label><i class="fas fa-school"></i> Kelas</label>
    </li>
@endsection

@section('content')

    <div x-show="tab === 'dashboard'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Dashboard Admin</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Selamat datang, Admin Sekolah</p>
                </div>
                <div class="header-right">
                    <span class="badge teal" style="font-size:12px;padding:6px 16px"><i class="fas fa-school"></i> SDIT
                        &amp; SMPIT</span>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="grid-4" style="margin-bottom:20px">
                <div class="admin-stat-card">
                    <div class="asc-icon grad-teal"><i class="fas fa-user-graduate"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $totalSiswa ?? 0 }}</div>
                        <div class="asc-label">Total Siswa</div>
                        <div class="asc-compare up">&#x2191; 12 siswa baru bulan ini</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="asc-icon grad-blue"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $totalGuru ?? 0 }}</div>
                        <div class="asc-label">Total Guru</div>
                        <div class="asc-compare up">&#x2191; 3 guru baru tahun ini</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="asc-icon grad-orange"><i class="fas fa-users"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $totalOrtu ?? 0 }}</div>
                        <div class="asc-label">Total Orang Tua</div>
                        <div class="asc-compare up">&#x2191; 8 akun baru bulan ini</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="asc-icon grad-purple"><i class="fas fa-school"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $totalKelas ?? 0 }}</div>
                        <div class="asc-label">Kelas Aktif</div>
                        <div class="asc-compare down">&#x2193; 2 kelas dari bulan lalu</div>
                    </div>
                </div>
            </div>
            <div class="grid-2" style="margin-bottom:20px">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-signal" style="color:var(--teal)"></i> Aktivitas Login (30 Hari)</h3><label
                            @click="tab='pengaturan'"
                            style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Detail</label>
                    </div>
                    <div class="chart-area">
                        <div class="chart-bar"
                            style="height:60%;background:linear-gradient(to top,var(--teal-light),var(--teal))"><span
                                class="bar-label">Sen</span></div>
                        <div class="chart-bar"
                            style="height:80%;background:linear-gradient(to top,var(--teal-light),var(--teal))"><span
                                class="bar-label">Sel</span></div>
                        <div class="chart-bar" style="height:45%"><span class="bar-label">Rab</span></div>
                        <div class="chart-bar" style="height:90%"><span class="bar-label">Kam</span></div>
                        <div class="chart-bar" style="height:70%"><span class="bar-label">Jum</span></div>
                        <div class="chart-bar" style="height:30%"><span class="bar-label">Sab</span></div>
                        <div class="chart-bar" style="height:25%"><span class="bar-label">Min</span></div>
                        <div class="chart-bar" style="height:65%"><span class="bar-label">Sen</span></div>
                        <div class="chart-bar" style="height:85%"><span class="bar-label">Sel</span></div>
                        <div class="chart-bar" style="height:50%"><span class="bar-label">Rab</span></div>
                        <div class="chart-bar" style="height:75%"><span class="bar-label">Kam</span></div>
                        <div class="chart-bar" style="height:95%"><span class="bar-label">Jum</span></div>
                        <div class="chart-bar" style="height:35%"><span class="bar-label">Sab</span></div>
                        <div class="chart-bar" style="height:20%"><span class="bar-label">Min</span></div>
                    </div>
                    <p style="font-size:11px;color:var(--gray-400);text-align:center; margin-top:20px;">Login per hari (14
                        hari
                        terakhir)</p>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-layer-group" style="color:var(--blue)"></i> Distribusi Siswa per Jenjang</h3>
                        <label @click="tab='siswa'"
                            style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Detail</label>
                    </div>
                    <div class="h-bar-group">
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 1-3 SD</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill teal" style="width:{{ $dist['sd13']['pct'] ?? 0 }}%">{{ $dist['sd13']['count'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 4-6 SD</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill blue" style="width:{{ $dist['sd46']['pct'] ?? 0 }}%">{{ $dist['sd46']['count'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 7</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill orange" style="width:{{ $dist['smp7']['pct'] ?? 0 }}%">{{ $dist['smp7']['count'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 8</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill purple" style="width:{{ $dist['smp8']['pct'] ?? 0 }}%">{{ $dist['smp8']['count'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 9</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill pink" style="width:{{ $dist['smp9']['pct'] ?? 0 }}%">{{ $dist['smp9']['count'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history" style="color:var(--orange)"></i> Aktivitas Terbaru</h3><label
                        style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Semua</label>
                </div>
                <div class="filter-bar"><button class="filter-btn active">Semua</button><button
                        class="filter-btn">SD</button><button class="filter-btn">SMP</button></div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Jenis</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logAktivitas ?? [] as $log)
                                <tr>
                                    <td style="color:var(--gray-400);font-size:12px">{{ $log->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        @if($log->tipe == 'user')
                                            <span class="badge light teal">User</span>
                                        @elseif($log->tipe == 'kelas')
                                            <span class="badge light blue">Kelas</span>
                                        @elseif($log->tipe == 'pengumuman')
                                            <span class="badge light orange">Pengumuman</span>
                                        @elseif($log->tipe == 'tugas')
                                            <span class="badge light purple">Tugas</span>
                                        @else
                                            <span class="badge light teal">{{ ucfirst($log->tipe) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->deskripsi }}</td>
                                    <td>
                                        @if($log->status == 'selesai')
                                            <span class="badge light green">Selesai</span>
                                        @else
                                            <span class="badge light orange">Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; color:var(--gray-400);">Tidak ada aktivitas terbaru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa'">
        <div>
            <div class="content-header">
                <h1>Manajemen Siswa</h1>
                <div class="header-right">
                    <label @click="tab='siswa-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Siswa</label>
                    <label class="header-btn outline" style="cursor:pointer"><i class="fas fa-upload"></i> Import</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jenis Kelamin</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listSiswa ?? [] as $siswaData)
                                <tr>
                                    <td>{{ $siswaData->nis ?? '-' }}</td>
                                    <td><strong>{{ $siswaData->nama }}</strong></td>
                                    <td>{{ $siswaData->kelas->nama_kelas ?? '-' }}</td>
                                    <td>{{ $siswaData->jenis_kelamin ?? '-' }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label @click="tab='siswa-detail'; selectedSiswa = JSON.parse(atob('{{ base64_encode(json_encode($siswaData)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Detail</label>
                                        <label @click="tab='siswa-edit'; selectedSiswa = JSON.parse(atob('{{ base64_encode(json_encode($siswaData)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Edit</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        siswa</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listSiswa->appends(['tab' => 'siswa'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'guru'">
        <div>
            <div class="content-header">
                <h1>Manajemen Guru</h1>
                <div class="header-right">
                    <label @click="tab='guru-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Guru</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listGuru ?? [] as $guruData)
                                <tr>
                                    <td>{{ $guruData->nip ?? '-' }}</td>
                                    <td><strong>{{ $guruData->nama }}</strong></td>
                                    <td>{{ $guruData->mapels->pluck('nama_mapel')->unique()->implode(', ') ?: '-' }}</td>
                                    <td>{{ $guruData->jadwal->pluck('kelas.nama_kelas')->unique()->implode(', ') ?: '-' }}
                                    </td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label @click="tab='guru-detail'; selectedGuru = JSON.parse(atob('{{ base64_encode(json_encode($guruData)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Detail</label>
                                        <label @click="tab='guru-form'; selectedGuru = JSON.parse(atob('{{ base64_encode(json_encode($guruData)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Edit</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        guru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listGuru->appends(['tab' => 'guru'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'ortu'">
        <div>
            <div class="content-header">
                <h1>Manajemen Orang Tua</h1>
                <div class="header-right">
                    <label @click="tab='ortu-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Akun</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Anak</th>
                                <th>Kelas</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listOrtu ?? [] as $ortu)
                                <tr>
                                    <td><strong>{{ $ortu->name }}</strong></td>
                                    <td>{{ $ortu->orangTua?->siswa->pluck('nama')->implode(', ') ?: '-' }}</td>
                                    <td>{{ $ortu->orangTua?->siswa->pluck('kelas.nama_kelas')->unique()->implode(', ') ?: '-' }}
                                    </td>
                                    <td>{{ $ortu->email }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label @click="tab='ortu-detail'; selectedOrtu = JSON.parse(atob('{{ base64_encode(json_encode($ortu)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Detail</label>
                                        <label @click="tab='ortu-form'; selectedOrtu = JSON.parse(atob('{{ base64_encode(json_encode($ortu)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Edit</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        orang tua</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listOrtu->appends(['tab' => 'ortu'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'kelas'" x-data="{ jenisKelas: 'umum' }">
        <div>
            <div class="content-header">
                <h1>Manajemen Kelas</h1>
                <div class="header-right" style="display:flex; align-items:center; gap:16px;">
                    <select x-model="jenisKelas" class="form-select" style="min-width:180px; font-weight:600; padding:8px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:14px; background:white;">
                        <option value="umum">Kelas Umum</option>
                        <option value="quran">Kelas Qur'an</option>
                    </select>
                    <label @click="tab='kelas-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Kelas</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card" x-show="jenisKelas === 'umum'">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Jenjang</th>
                                <th>Jumlah Siswa</th>
                                <th>Wali Kelas</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listKelas ?? [] as $kelasData)
                                <tr>
                                    <td><strong>{{ $kelasData->nama_kelas }}</strong></td>
                                    <td>{{ str_contains($kelasData->nama_kelas, '1') || str_contains($kelasData->nama_kelas, '2') || str_contains($kelasData->nama_kelas, '3') || str_contains($kelasData->nama_kelas, '4') || str_contains($kelasData->nama_kelas, '5') || str_contains($kelasData->nama_kelas, '6') ? 'SD' : 'SMP' }}
                                    </td>
                                    <td>{{ $kelasData->siswa_count ?? 0 }}</td>
                                    <td>{{ $kelasData->waliKelas->nama ?? '-' }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label @click="tab='kelas-form'; selectedKelas = JSON.parse(atob('{{ base64_encode(json_encode($kelasData)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Edit</label>
                                        <label @click="tab='kelas-detail'; selectedKelas = JSON.parse(atob('{{ base64_encode(json_encode($kelasData)) }}'))" class="btn-small outline"
                                            style="cursor:pointer">Detail</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        kelas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listKelas->appends(['tab' => 'kelas'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
            
            <div class="card" x-show="jenisKelas === 'quran'">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Jenjang</th>
                                <th>Kategori</th>
                                <th>Tingkat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listKelasQuran ?? [] as $kq)
                                <tr>
                                    <td><strong>{{ $kq->nama_kelas }}</strong></td>
                                    <td>{{ $kq->jenjang }}</td>
                                    <td>{{ $kq->kategori }}</td>
                                    <td>{{ $kq->tingkat }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        kelas Qur'an</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listKelasQuran->appends(['tab' => 'kelas'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa-form'">
        <div>
            <div class="content-header">
                <h1>Tambah Siswa</h1>
                <div class="header-right">
                    <label @click="tab='siswa'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form action="/admin/siswa" method="POST">
                    @csrf
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>NIS</label>
                            <div class="input-wrap"><input type="text" name="nis" placeholder="NIS" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="nama" placeholder="Nama Lengkap"
                                    required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach (\App\Models\Kelas::all() as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:16px"><label>Email User (Untuk Login)</label>
                        <div class="input-wrap"><input type="email" name="email" placeholder="Email Siswa" required
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='siswa'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div x-show="tab === 'cbt'" x-data="{ selectedExam: null }">
        @include('dashboard.admin-sections.cbt-approval')
    </div>
    <div x-show="tab === 'pengaturan'">
        <div>
            <div class="content-header">
                <h1>Pengaturan</h1>
                <div class="header-right">
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-cog" style="color:var(--teal)"></i> Pengaturan Umum</h3>
                    </div>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px">Nama Sekolah: SDIT &amp; SMPIT Al
                        Azhar Jaya Indonesia</p>
                    <div class="form-group"><label>Nama Lengkap</label>
                        <div class="input-wrap"><input type="text" placeholder="Ahmad Rizky"
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                        <div class="form-error">Nama wajib diisi</div>
                    </div>
                </div>
                <div class="grid-2" style="margin-bottom:16px">
                    <div class="form-group"><label>Kelas</label>
                        <div class="input-wrap"><input type="text" placeholder="7A"
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                        <div class="form-error">Kelas wajib diisi</div>
                    </div>
                    <div class="form-group"><label>Jenis Kelamin</label><select class="form-select">
                            <option>L</option>
                            <option>P</option>
                        </select></div>
                </div>
                <div class="form-group" style="margin-bottom:16px"><label>Email</label>
                    <div class="input-wrap"><input type="email" placeholder="siswa@alazharjayaindonesia.sch.id"
                            style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                    </div>
                    <div class="form-error">Email wajib diisi</div>
                </div>
                <div class="form-group" style="margin-bottom:16px"><label>Alamat</label>
                    <div class="input-wrap"><input type="text" placeholder="Jl. ..."
                            style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                    </div>
                    <div class="form-error">Alamat wajib diisi</div>
                </div>
                <div style="display:flex;gap:10px;margin-top:8px">
                    <label @click="tab='siswa'" class="btn-login" style="text-align:center;flex:1;cursor:pointer"><i
                            class="fas fa-save"></i> Simpan</label>
                    <label @click="tab='siswa'" class="btn-login"
                        style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                            class="fas fa-times"></i> Batal</label>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'guru-form'">
        <div>
            <div class="content-header">
                <h1><span>Tambah</span> Guru</h1>
                <div class="header-right">
                    <label @click="tab='guru'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="selectedGuru ? ('/admin/guru/' + selectedGuru.id) : '/admin/guru'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!selectedGuru">
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>NIP</label>
                            <div class="input-wrap"><input type="text" name="nip" placeholder="19870101" :value="selectedGuru?.nip" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">NIP wajib diisi</div>
                        </div>
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="nama" placeholder="Ustadz Ahmad Fauzi" :value="selectedGuru?.nama" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Nama wajib diisi</div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Telepon</label>
                            <div class="input-wrap"><input type="text" name="no_telp" placeholder="08..." :value="selectedGuru?.no_telp"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                        <div class="form-group"><label>Email</label>
                            <div class="input-wrap"><input type="email" name="email" placeholder="guru@alazharjayaindonesia.sch.id" :value="selectedGuru?.user?.email" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Email wajib diisi</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login" style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='guru'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'ortu-form'">
        <div>
            <div class="content-header">
                <h1><span>Tambah</span> Orang Tua</h1>
                <div class="header-right">
                    <label @click="tab='ortu'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="selectedOrtu ? ('/admin/ortu/' + selectedOrtu.id) : '/admin/ortu'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!selectedOrtu">
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="name" placeholder="Bpk. Andi Pratama" :value="selectedOrtu?.name" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Nama wajib diisi</div>
                        </div>
                        <div class="form-group"><label>Telepon</label>
                            <div class="input-wrap"><input type="text" name="no_telp" placeholder="08..." :value="selectedOrtu?.orang_tua?.no_telp"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Email</label>
                            <div class="input-wrap"><input type="email" name="email" placeholder="ortu@email.com" :value="selectedOrtu?.email" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Email wajib diisi</div>
                        </div>
                        <div class="form-group"><label>Alamat</label>
                            <div class="input-wrap"><input type="text" name="alamat" placeholder="Jl..." :value="selectedOrtu?.orang_tua?.alamat"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login" style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='ortu'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'kelas-form'">
        <div>
            <div class="content-header">
                <h1><span>Tambah</span> Kelas</h1>
                <div class="header-right">
                    <label @click="tab='kelas'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="selectedKelas ? ('/admin/kelas/' + selectedKelas.id) : '/admin/kelas'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!selectedKelas">
                    <div class="form-group" style="margin-bottom:16px"><label>Nama Kelas</label>
                        <div class="input-wrap"><input type="text" name="nama_kelas" placeholder="1A" :value="selectedKelas?.nama_kelas" required
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                        <div class="form-error">Nama Kelas wajib diisi</div>
                    </div>
                    <div class="form-group" style="margin-bottom:16px"><label>Wali Kelas</label>
                        <select name="wali_kelas_id" class="form-select" :value="selectedKelas?.wali_kelas_id" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($allGurus ?? [] as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                        <div class="form-error">Wali Kelas wajib diisi</div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login" style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='kelas'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Siswa</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap siswa</p>
                </div>
                <div class="header-right">
                    <label @click="tab='siswa'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">AR</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-user-graduate" style="color:var(--teal)"></i> Identitas Siswa</h3><label
                        @click="tab='siswa-edit'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">NIS</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.nis || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Lengkap</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.nama || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jenis Kelamin</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.jenis_kelamin || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Kelas</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.kelas?.nama_kelas || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Tempat, Tgl Lahir</span>
                        <div style="font-weight:600" x-text="(selectedSiswa?.tempat_lahir || '') + ', ' + (selectedSiswa?.tanggal_lahir || '')"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Alamat</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.alamat || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Orang Tua</span>
                        <div style="font-weight:600" x-text="(selectedSiswa?.nama_ayah || selectedSiswa?.nama_ibu) || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">No. Telepon</span>
                        <div style="font-weight:600">-</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Email</span>
                        <div style="font-weight:600">-</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line" style="color:var(--blue)"></i> Ringkasan Akademik</h3>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;padding:8px 0">
                    <div style="text-align:center;padding:16px;background:var(--teal-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--teal)">85.3</div>
                        <div style="font-size:11px;color:var(--gray-400)">Rata-rata</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--blue-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--blue)">5</div>
                        <div style="font-size:11px;color:var(--gray-400)">Peringkat</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--green-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--green)">112</div>
                        <div style="font-size:11px;color:var(--gray-400)">Hadir</div>
                    </div>
                    <div
                        style="text-align:center;padding:16px;background:var(--orange-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--orange)">3</div>
                        <div style="font-size:11px;color:var(--gray-400)">Tugas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'guru-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Guru</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap guru</p>
                </div>
                <div class="header-right">
                    <label @click="tab='guru'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar blue">DS</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard-teacher" style="color:var(--blue)"></i> Identitas Guru</h3><label
                        @click="tab='guru-form'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">NIP</span>
                        <div style="font-weight:600" x-text="selectedGuru?.nip || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Lengkap</span>
                        <div style="font-weight:600" x-text="selectedGuru?.nama || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jenis Kelamin</span>
                        <div style="font-weight:600">-</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Mata Pelajaran</span>
                        <div style="font-weight:600" x-text="selectedGuru?.mapels?.map(m => m.nama_mapel).join(', ') || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">No. Telepon</span>
                        <div style="font-weight:600" x-text="selectedGuru?.no_telp || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Email</span>
                        <div style="font-weight:600">-</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Alamat</span>
                        <div style="font-weight:600" x-text="selectedGuru?.alamat || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Status</span>
                        <div><span class="badge teal" x-text="selectedGuru?.status || 'Aktif'"></span></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard" style="color:var(--teal)"></i> Kelas yang Diajar</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Wali Kelas</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="jadwal in (selectedGuru?.jadwal || [])" :key="jadwal.id">
                                <tr>
                                    <td><strong x-text="jadwal.kelas?.nama_kelas || '-'"></strong></td>
                                    <td x-text="jadwal.kelas?.siswa_count || '0'"></td>
                                    <td x-text="jadwal.kelas?.wali_kelas?.nama || '-'"></td>
                                    <td><label @click="tab='kelas-detail'; selectedKelas = jadwal.kelas" class="btn-small teal"
                                            style="cursor:pointer">Detail</label></td>
                                </tr>
                            </template>
                            <tr x-show="!(selectedGuru?.jadwal?.length)">
                                <td colspan="4" style="text-align:center; color:var(--gray-400)">Tidak ada data kelas</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'ortu-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Orang Tua</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap orang tua/wali</p>
                </div>
                <div class="header-right">
                    <label @click="tab='ortu'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar orange">SR</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-users" style="color:var(--orange)"></i> Identitas Orang Tua</h3><label
                        @click="tab='ortu-form'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Orang Tua</span>
                        <div style="font-weight:600" x-text="selectedOrtu?.name || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">No. Telepon</span>
                        <div style="font-weight:600" x-text="(selectedOrtu?.orang_tua || selectedOrtu?.orangTua)?.no_telp || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Email</span>
                        <div style="font-weight:600" x-text="selectedOrtu?.email || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Alamat</span>
                        <div style="font-weight:600" x-text="(selectedOrtu?.orang_tua || selectedOrtu?.orangTua)?.alamat || '-'"></div>
                    </div>
                    <div style="grid-column: span 2"><span style="font-size:12px;color:var(--gray-400)">Anak</span>
                        <div style="font-weight:600" x-text="(selectedOrtu?.orang_tua || selectedOrtu?.orangTua)?.siswa?.map(s => s.nama + ' (' + (s.kelas?.nama_kelas || '-') + ')').join(', ') || '-'"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'kelas-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Kelas</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap kelas</p>
                </div>
                <div class="header-right">
                    <label @click="tab='kelas'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">7A</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-school" style="color:var(--teal)"></i> Informasi Kelas</h3><label
                        @click="tab='kelas-form'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Kelas</span>
                        <div style="font-weight:600" x-text="selectedKelas?.nama_kelas || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jenjang</span>
                        <div style="font-weight:600" x-text="['1','2','3','4','5','6'].some(c => (selectedKelas?.nama_kelas || '').includes(c)) ? 'SD' : 'SMP'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Wali Kelas</span>
                        <div style="font-weight:600" x-text="selectedKelas?.wali_kelas?.nama || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jumlah Siswa</span>
                        <div style="font-weight:600" x-text="selectedKelas?.siswa_count || '0'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Tahun Ajaran</span>
                        <div style="font-weight:600">2025/2026</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Status</span>
                        <div><span class="badge teal">Aktif</span></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-graduate" style="color:var(--blue)"></i> Daftar Siswa</h3><label
                        style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Lihat
                        Semua</label>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>L/P</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(siswa, index) in (selectedKelas?.siswa || [])" :key="siswa.id">
                                <tr>
                                    <td x-text="index + 1"></td>
                                    <td x-text="siswa.nis || '-'"></td>
                                    <td><strong x-text="siswa.nama || '-'"></strong></td>
                                    <td x-text="(siswa.jenis_kelamin || '-').substring(0, 1)"></td>
                                    <td><label @click="tab='siswa-detail'; selectedSiswa = siswa" class="btn-small outline"
                                            style="cursor:pointer">Detail</label></td>
                                </tr>
                            </template>
                            <tr x-show="!(selectedKelas?.siswa?.length)">
                                <td colspan="5" style="text-align:center; color:var(--gray-400)">Tidak ada data siswa</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa-edit'">
        <div>
            <div class="content-header">
                <h1>Edit Siswa</h1>
                <div class="header-right">
                    <label @click="tab='siswa'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">AD</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="'/admin/siswa/' + selectedSiswa?.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>NIS</label>
                            <div class="input-wrap"><input type="text" name="nis" :value="selectedSiswa?.nis" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="nama" :value="selectedSiswa?.nama" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Kelas</label>
                            <div class="input-wrap"><input type="text" readonly disabled :value="selectedSiswa?.kelas?.nama_kelas || '-'"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%;background:transparent;">
                            </div>
                        </div>
                        <div class="form-group"><label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" :value="selectedSiswa?.jenis_kelamin" required>
                                <option value="L">L</option>
                                <option value="P">P</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:16px"><label>Email</label>
                        <div class="input-wrap"><input type="email" name="email" :value="selectedSiswa?.user?.email" required
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login" style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='siswa'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
