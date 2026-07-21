@extends('layouts.app')

@section('title', 'Kerjakan Workbook - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li>
        <a href="{{ route('siswa.workbook.index') }}"><i class="fas fa-arrow-left"></i> Kembali</a>
    </li>
@endsection

@section('content')
<div class="content-header">
    <div>
        <h1>{{ $workbook->judul }}</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">{{ $workbook->mapel->nama_mapel ?? '' }} &mdash; {{ $soals->count() }} soal</p>
    </div>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
    </div>
</div>

<form method="POST" action="{{ route('siswa.workbook.submit', $workbook->id) }}">
    @csrf
    @foreach($soals as $index => $soal)
    @php $j = $jawaban->get($soal->id); @endphp
    <div class="card" style="margin-bottom:14px">
        <div class="card-header">
            <h3><span class="badge blue" style="font-size:12px">Soal #{{ $index + 1 }}</span></h3>
            @if($j)
            <span class="badge green light"><i class="fas fa-check"></i> Sudah dijawab</span>
            @endif
        </div>
        <div style="padding:8px 0">
            <p style="font-size:15px;font-weight:500;margin-bottom:14px">{{ $soal->soal }}</p>

            @if($soal->tipe === 'pg')
                @foreach(['a','b','c','d'] as $pil)
                @php $pilihan = 'pilihan_' . $pil; @endphp
                <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;margin-bottom:6px;border:1.5px solid var(--border-light);border-radius:var(--radius-sm);cursor:pointer;background:{{ $j && $j->jawaban === $pil ? 'var(--teal-light)' : 'var(--white)' }};{{ $j ? 'pointer-events:none;opacity:0.8' : '' }}">
                    <input type="radio" name="soal_{{ $soal->id }}" value="{{ $pil }}" {{ $j && $j->jawaban === $pil ? 'checked' : '' }} {{ $j ? 'disabled' : '' }} style="accent-color:var(--teal)">
                    <span style="font-size:14px;font-weight:500">{{ strtoupper($pil) }}. {{ $soal->$pilihan }}</span>
                </label>
                @endforeach
            @else
                <textarea name="soal_{{ $soal->id }}" rows="4" placeholder="Tulis jawaban..." {{ $j ? 'disabled' : '' }} style="width:100%;padding:12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical;{{ $j ? 'background:var(--gray-50)' : '' }}">{{ $j->jawaban ?? '' }}</textarea>
            @endif
        </div>
    </div>
    @endforeach

    <div class="card" style="padding:16px 24px">
        <div style="display:flex;gap:12px;align-items:center;justify-content:space-between">
            <span style="font-size:13px;color:var(--gray-500)"><strong>{{ $soals->count() }}</strong> soal tersisa</span>
            <div style="display:flex;gap:10px">
                <a href="{{ route('siswa.workbook.index') }}" class="btn-login" style="text-decoration:none;background:var(--gray-300);color:var(--text);flex:none;padding:10px 24px;width:auto;font-size:13px"><i class="fas fa-times"></i> Batal</a>
                <button type="submit" class="btn-login" style="flex:none;padding:10px 24px;width:auto;font-size:13px;border:none;cursor:pointer"><i class="fas fa-save"></i> Simpan Jawaban</button>
            </div>
        </div>
    </div>
</form>

@if(session('success'))
    <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@endsection
