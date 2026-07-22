@extends('layouts.app')

@section('title', 'Dashboard Guru - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li :class="{'active': tab === 'dashboard'}" @click="tab = 'dashboard'">
        <label><i class="fas fa-th-large"></i> Dashboard</label>
    </li>
    <li :class="{'active': tab === 'kelas'}" @click="tab = 'kelas'">
        <label><i class="fas fa-chalkboard"></i> Kelas Saya</label>
    </li>
    <li :class="{'active': tab === 'tahfidz'}" @click="tab = 'tahfidz'">
        <label><i class="fas fa-quran"></i> Tahfidz</label>
    </li>
    <li :class="{'active': tab === 'tugas'}" @click="tab = 'tugas'">
        <label><i class="fas fa-tasks"></i> Tugas &amp; Ulangan</label>
    </li>
    <li :class="{'active': tab === 'nilai'}" @click="tab = 'nilai'">
        <label><i class="fas fa-chart-line"></i> Nilai</label>
    </li>
    <li :class="{'active': tab === 'absensi'}" @click="tab = 'absensi'">
        <label><i class="fas fa-clipboard-check"></i> Absensi Siswa</label>
    </li>
    <li :class="{'active': tab === 'absensi_guru'}" @click="tab = 'absensi_guru'">
        <label><i class="fas fa-user-clock"></i> Absensi Saya</label>
    </li>
    <li :class="{'active': tab === 'catatan'}" @click="tab = 'catatan'">
        <label><i class="fas fa-star"></i> Catatan Wali</label>
    </li>
    <li :class="{'active': tab === 'kondisi_kelas'}" @click="tab = 'kondisi_kelas'">
        <label><i class="fas fa-heartbeat"></i> Kondisi Kelas</label>
    </li>
    <li :class="{'active': tab === 'materi'}" @click="tab = 'materi'">
        <label><i class="fas fa-folder-open"></i> Bahan Ajar</label>
    </li>
    <li :class="{'active': tab === 'workbook'}" @click="tab = 'workbook'">
        <label><i class="fas fa-book"></i> Workbook</label>
    </li>
    <li :class="{'active': tab === 'cbt'}" @click="tab = 'cbt'">
        <label><i class="fas fa-laptop"></i> CBT / Ulangan</label>
    </li>
    <li :class="{'active': tab === 'pengumuman'}" @click="tab = 'pengumuman'">
        <label><i class="fas fa-bullhorn"></i> Pengumuman</label>
    </li>
    <li :class="{'active': tab === 'pesan'}" @click="tab = 'pesan'">
        <label><i class="fas fa-envelope"></i> Buku Penghubung</label>
    </li>
    <li :class="{'active': tab === 'iht'}" @click="tab = 'iht'">
        <label><i class="fas fa-chalkboard-teacher"></i> In House Training (IHT)</label>
    </li>
    <li :class="{'active': tab === 'rapor_supervisi'}" @click="tab = 'rapor_supervisi'">
        <label><i class="fas fa-file-contract"></i> Rapor Supervisi</label>
    </li>
    <li :class="{'active': tab === 'kti'}" @click="tab = 'kti'">
        <label><i class="fas fa-file-signature"></i> Karya Ilmiah (KTI)</label>
    </li>
    <li :class="{'active': tab === 'laporan_mengajar'}" @click="tab = 'laporan_mengajar'">
        <label><i class="fas fa-clipboard-list"></i> Jurnal Mengajar</label>
    </li>
@endsection

@section('content')
    <div x-show="tab === 'dashboard'">
        @include('dashboard.guru-sections.dashboard')
    </div>
    <div x-show="tab === 'kelas'">
        @include('dashboard.guru-sections.kelas')
    </div>
    <div x-show="tab === 'tahfidz'">
        @include('dashboard.guru-sections.tahfidz')
    </div>
    <div x-show="tab === 'tugas'">
        @include('dashboard.guru-sections.tugas')
    </div>
    <div x-show="tab === 'nilai'">
        @include('dashboard.guru-sections.nilai')
    </div>
    <div x-show="tab === 'absensi'">
        @include('dashboard.guru-sections.absensi')
    </div>
    <div x-show="tab === 'absensi_guru'">
        @include('dashboard.guru-sections.absensi-guru')
    </div>
    <div x-show="tab === 'catatan'">
        @include('dashboard.guru-sections.catatan')
    </div>
    <div x-show="tab === 'kondisi_kelas'">
        @include('dashboard.guru-sections.kondisi-kelas')
    </div>
    <div x-show="tab === 'materi'">
        @include('dashboard.guru-sections.materi')
    </div>
    <div x-show="tab === 'workbook'">
        @include('dashboard.guru-sections.workbook')
    </div>
    <div x-show="tab === 'cbt'">
        @include('dashboard.guru-sections.cbt-index')
    </div>
    <div x-show="tab === 'pengumuman'">
        @include('dashboard.guru-sections.pengumuman')
    </div>
    <div x-show="tab === 'pesan'">
        @include('dashboard.guru-sections.pesan')
    </div>
    <div x-show="tab === 'iht'">
        @include('dashboard.guru-sections.iht')
    </div>
    <div x-show="tab === 'rapor_supervisi'">
        @include('dashboard.guru-sections.rapor_supervisi')
    </div>
    <div x-show="tab === 'kti'">
        @include('dashboard.guru-sections.kti')
    </div>
    <div x-show="tab === 'laporan_mengajar'">
        @include('dashboard.guru-sections.laporan')
    </div>
    <div x-show="tab === 'kelas-detail'">
        @include('dashboard.guru-sections.kelas-detail')
    </div>
    <div x-show="tab === 'kelas-form'">
        <div class="content-header">
            <div>
                <h1>Tambah Kelas</h1>
                <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Buat kelas baru untuk mata pelajaran</p>
            </div>
            <div class="header-right">
                <label @click="tab='kelas'" class="header-btn outline" style="cursor:pointer"><i class="fas fa-arrow-left"></i> Kembali</label>
                <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
            </div>
        </div>
        <div class="form-card" style="background:var(--white);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);max-width:600px">
            <form method="POST" action="{{ route('guru.kelas.store') }}">
                @csrf
                <div class="form-group" style="margin-bottom:16px">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:6px">Nama Kelas</label>
                    <div class="input-wrap" style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:0 14px">
                        <input type="text" name="nama_kelas" required placeholder="Contoh: 7C" style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:16px">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:6px">Tingkat</label>
                    <select name="tingkat" class="form-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <option value="Kelas 7">Kelas 7</option>
                        <option value="Kelas 8">Kelas 8</option>
                        <option value="Kelas 9">Kelas 9</option>
                    </select>
                </div>
                <div style="display:flex;gap:10px;margin-top:24px">
                    <button type="submit" class="btn-login" style="text-align:center;flex:1;cursor:pointer;border:none"><i class="fas fa-save"></i> Simpan</button>
                    <label @click="tab='kelas'" class="btn-login" style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i class="fas fa-times"></i> Batal</label>
                </div>
            </form>
        </div>
    </div>
    <div x-show="tab === 'siswa-detail'" x-html="detailSiswaHtml">
    </div>
@endsection
