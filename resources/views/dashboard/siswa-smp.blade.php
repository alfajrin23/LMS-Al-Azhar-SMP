@extends('layouts.app')

@section('title', 'Dashboard Siswa SMP - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li :class="{ 'active': tab === 'dashboard' }" @click="tab = 'dashboard'">
        <label><i class="fas fa-th-large"></i> Dashboard</label>
    </li>
    <li :class="{ 'active': tab === 'mapel' }" @click="tab = 'mapel'">
        <label><i class="fas fa-book"></i> Mata Pelajaran</label>
    </li>
    <li :class="{ 'active': tab === 'tugas' }" @click="tab = 'tugas'">
        <label><i class="fas fa-tasks"></i> Tugas</label>
    </li>
    <li :class="{ 'active': tab === 'tahfidz' }" @click="tab = 'tahfidz'">
        <label><i class="fas fa-quran"></i> Tahfidz</label>
    </li>
    <li :class="{ 'active': tab === 'ulangan' }" @click="tab = 'ulangan'">
        <label><i class="fas fa-pencil-alt"></i> Ulangan</label>
    </li>
    <li :class="{ 'active': tab === 'nilai' }" @click="tab = 'nilai'">
        <label><i class="fas fa-chart-line"></i> Nilai</label>
    </li>
    <li :class="{ 'active': tab === 'pengumuman' }" @click="tab = 'pengumuman'">
        <label><i class="fas fa-bullhorn"></i> Pengumuman</label>
    </li>
    <li :class="{ 'active': tab === 'jadwal' }" @click="tab = 'jadwal'">
        <label><i class="fas fa-calendar-alt"></i> Jadwal</label>
    </li>
    <li :class="{ 'active': tab === 'pesan' }" @click="tab = 'pesan'">
        <label><i class="fas fa-envelope"></i> Buku Penghubung</label>
    </li>
    <li :class="{ 'active': tab === 'workbook' }" @click="tab = 'workbook'">
        <label><i class="fas fa-book"></i> Workbook</label>
    </li>
    <li :class="{ 'active': tab === 'cbt' }" @click="tab = 'cbt'">
        <label><i class="fas fa-laptop"></i> Ujian CBT</label>
    </li>
    <li :class="{ 'active': tab === 'rapor' }" @click="tab = 'rapor'">
        <label><i class="fas fa-file-invoice"></i> Rapor</label>
    </li>
    <li :class="{ 'active': tab === 'prestasi' }" @click="tab = 'prestasi'">
        <label><i class="fas fa-medal"></i> Prestasi Siswa</label>
    </li>
    @if (isset($isKelas9) && $isKelas9)
        <li :class="{ 'active': tab === 'kti' }" @click="tab = 'kti'">
            <label><i class="fas fa-file-signature"></i> Karya Ilmiah (KTI)</label>
        </li>
    @endif
@endsection

@section('content')
    <div x-show="tab === 'dashboard'">
        @include('dashboard.smp-sections.dashboard')
    </div>
    <div x-show="tab === 'mapel'">
        @include('dashboard.smp-sections.mapel')
    </div>
    <div x-show="tab === 'tugas'">
        @include('dashboard.smp-sections.tugas')
    </div>
    <div x-show="tab === 'tahfidz'">
        @include('dashboard.smp-sections.tahfidz')
    </div>
    <div x-show="tab === 'ulangan'">
        @include('dashboard.smp-sections.ulangan')
    </div>
    <div x-show="tab === 'nilai'">
        @include('dashboard.smp-sections.nilai')
    </div>
    <div x-show="tab === 'pengumuman'">
        @include('dashboard.smp-sections.pengumuman')
    </div>
    <div x-show="tab === 'jadwal'">
        @include('dashboard.smp-sections.jadwal')
    </div>
    <div x-show="tab === 'pesan'">
        @include('dashboard.smp-sections.pesan')
    </div>
    <div x-show="tab === 'workbook'">
        @include('dashboard.smp-sections.workbook')
    </div>
    <div x-show="tab === 'cbt'">
        @include('dashboard.smp-sections.cbt')
    </div>
    <div x-show="tab === 'rapor'">
        @include('dashboard.smp-sections.rapor')
    </div>
    <div x-show="tab === 'prestasi'">
        <div class="content-header"><h1>Prestasi Siswa <span>SMPIT {{ setting('school_name') }}</span></h1></div>
        <div class="card"><div style="padding:40px;text-align:center;color:var(--gray-400)"><i class="fas fa-medal" style="font-size:48px;margin-bottom:16px;opacity:0.3"></i><p>Belum ada rekapan prestasi saat ini</p></div></div>
    </div>
    @if (isset($isKelas9) && $isKelas9)
        <div x-show="tab === 'kti'">
            @include('dashboard.smp-sections.kti')
        </div>
    @endif
@endsection
