@extends('layouts.app')

@section('title', 'Dashboard Orang Tua - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li :class="{'active': tab === 'dashboard'}" @click="tab = 'dashboard'">
        <label><i class="fas fa-th-large"></i> Dashboard</label>
    </li>
    <li :class="{'active': tab === 'nilai'}" @click="tab = 'nilai'">
        <label><i class="fas fa-chart-line"></i> Nilai Anak</label>
    </li>
    <li :class="{'active': tab === 'tahfidz'}" @click="tab = 'tahfidz'">
        <label><i class="fas fa-quran"></i> Tahfidz</label>
    </li>
    <li :class="{'active': tab === 'kehadiran'}" @click="tab = 'kehadiran'">
        <label><i class="fas fa-clipboard-check"></i> Kehadiran</label>
    </li>
    <li :class="{'active': tab === 'jadwal'}" @click="tab = 'jadwal'">
        <label><i class="fas fa-calendar-alt"></i> Jadwal</label>
    </li>
    <li :class="{'active': tab === 'tagihan'}" @click="tab = 'tagihan'">
        <label><i class="fas fa-file-invoice-dollar"></i> Tagihan SPP</label>
    </li>
    <li :class="{'active': tab === 'pesan'}" @click="tab = 'pesan'">
        <label><i class="fas fa-envelope"></i> Buku Penghubung</label>
    </li>
@endsection

@section('content')
    <div x-show="tab === 'dashboard'" x-init="childId = childId || {{ $anak->first()?->id ?? 'null' }}">
        @include('dashboard.orang-tua-sections.dashboard')
    </div>
    <div x-show="tab === 'nilai'">
        @include('dashboard.orang-tua-sections.nilai')
    </div>
    <div x-show="tab === 'tahfidz'">
        @include('dashboard.orang-tua-sections.tahfidz')
    </div>
    <div x-show="tab === 'kehadiran'">
        @include('dashboard.orang-tua-sections.kehadiran')
    </div>
    <div x-show="tab === 'jadwal'">
        @include('dashboard.orang-tua-sections.jadwal')
    </div>
    <div x-show="tab === 'tagihan'">
        @include('dashboard.orang-tua-sections.tagihan')
    </div>
    <div x-show="tab === 'pesan'">
        @include('dashboard.orang-tua-sections.pesan')
    </div>

    @if(session('success'))
        <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
@endsection
