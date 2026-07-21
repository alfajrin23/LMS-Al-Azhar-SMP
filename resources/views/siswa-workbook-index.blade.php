@extends('layouts.app')

@section('title', 'Workbook Bank Soal - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li :class="{'active': tab === 'workbook'}" @click="tab = 'workbook'">
        <label><i class="fas fa-book"></i> Workbook</label>
    </li>
    <li>
        <a href="{{ route('dashboard') }}"><i class="fas fa-arrow-left"></i> Kembali</a>
    </li>
@endsection

@section('content')
<div x-data="{ tab: 'workbook' }">
    <div x-show="tab === 'workbook'">
        <div class="content-header">
            <div>
                <h1>Workbook Bank Soal</h1>
                <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Kerjakan latihan soal untuk meningkatkan pemahaman</p>
            </div>
            <div class="header-right">
                <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
            </div>
        </div>

        @forelse($workbooks as $wb)
        <div class="card" style="margin-bottom:14px">
            <div class="card-header">
                <h3><i class="fas fa-book" style="color:var(--teal)"></i> {{ $wb->judul }}</h3>
                <div style="display:flex;gap:8px;align-items:center">
                    <span class="badge blue">{{ $wb->mapel->kode ?? $wb->mapel->nama_mapel ?? '-' }}</span>
                    <span class="badge teal">{{ $wb->soals_count }} soal</span>
                </div>
            </div>
            <div style="padding:4px 0;font-size:13px;color:var(--gray-500)">
                {{ $wb->deskripsi ?: '' }}
            </div>
            <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between">
                <div class="progress-wrap" style="flex:1;margin-right:16px">
                    <div class="progress-label">
                        <span>{{ $wb->dijawab }}/{{ $wb->soals_count }} terjawab</span>
                        <span>{{ $wb->soals_count > 0 ? round(($wb->dijawab / $wb->soals_count) * 100) : 0 }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="fill" style="width: {{ $wb->soals_count > 0 ? round(($wb->dijawab / $wb->soals_count) * 100) : 0 }}%"></div>
                    </div>
                </div>
                @if($wb->dijawab > 0 && $wb->dijawab >= $wb->soals_count)
                    <a href="{{ route('siswa.workbook.hasil', $wb->id) }}" class="btn-small teal" style="text-decoration:none"><i class="fas fa-eye"></i> Lihat Hasil</a>
                @else
                    <a href="{{ route('siswa.workbook.kerjakan', $wb->id) }}" class="btn-small teal" style="text-decoration:none"><i class="fas fa-pencil-alt"></i> Kerjakan</a>
                @endif
            </div>
        </div>
        @empty
        <div class="card">
            <div style="padding:40px;text-align:center;color:var(--gray-400)">
                <i class="fas fa-book" style="font-size:48px;margin-bottom:16px;opacity:0.3"></i>
                <p>Belum ada workbook untuk kelas ini</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
