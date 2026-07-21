@extends('layouts.app')

@section('title', 'Kerjakan Ujian - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li>
        <a href="{{ route('siswa.cbt.index') }}"><i class="fas fa-arrow-left"></i> Kembali</a>
    </li>
@endsection

@section('content')
<div class="content-header">
    <div>
        <h1>{{ $cbtExam->judul }}</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">{{ $cbtExam->mapel->nama_mapel ?? '' }} &mdash; {{ $soals->count() }} soal &mdash; {{ $cbtExam->durasi }} menit</p>
    </div>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;background:var(--orange-bg);border:1px solid var(--orange)">
    <div style="display:flex;align-items:center;gap:10px;padding:4px 0">
        <i class="fas fa-exclamation-triangle" style="color:var(--orange);font-size:18px"></i>
        <span style="font-size:13px;color:var(--text)">Bacalah soal dengan teliti sebelum menjawab. Jawaban akan dinilai secara otomatis untuk soal PG.</span>
    </div>
</div>

<form method="POST" action="{{ route('siswa.cbt.submit', $cbtExam->id) }}">
    @csrf
    @foreach($soals as $index => $soal)
    <div class="card" style="margin-bottom:14px">
        <div class="card-header">
            <h3><span class="badge blue" style="font-size:12px">Soal #{{ $index + 1 }}</span></h3>
            <span class="badge {{ $soal->tipe === 'pg' ? 'blue' : 'orange' }} light" style="font-size:10px">{{ $soal->tipe === 'pg' ? 'PG' : 'Essay' }}</span>
        </div>
        <div style="padding:8px 0">
            <p style="font-size:15px;font-weight:500;margin-bottom:14px">{{ $soal->soal }}</p>

            @if($soal->tipe === 'pg')
                @foreach(['a','b','c','d'] as $pil)
                @php $pilihan = 'pilihan_' . $pil; @endphp
                <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;margin-bottom:6px;border:1.5px solid var(--border-light);border-radius:var(--radius-sm);cursor:pointer;background:var(--white);transition:.15s">
                    <input type="radio" name="soal_{{ $soal->id }}" value="{{ $pil }}" style="accent-color:var(--teal)">
                    <span style="font-size:14px;font-weight:500">{{ strtoupper($pil) }}. {{ $soal->$pilihan }}</span>
                </label>
                @endforeach
            @else
                <textarea name="soal_{{ $soal->id }}" rows="4" placeholder="Tulis jawaban essay..." style="width:100%;padding:12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
            @endif
        </div>
    </div>
    @endforeach

    <div class="card" style="padding:16px 24px">
        <div style="display:flex;gap:12px;align-items:center;justify-content:space-between">
            <span style="font-size:13px;color:var(--gray-500)"><strong>{{ $soals->count() }}</strong> soal harus dijawab</span>
            <div style="display:flex;gap:10px">
                <a href="{{ route('siswa.cbt.index') }}" class="btn-login" style="text-decoration:none;background:var(--gray-300);color:var(--text);flex:none;padding:10px 24px;width:auto;font-size:13px"><i class="fas fa-times"></i> Batal</a>
                <button type="submit" class="btn-login" style="flex:none;padding:10px 24px;width:auto;font-size:13px;border:none;cursor:pointer" onclick="return confirm('Yakin ingin mengumpulkan? Jawaban tidak bisa diubah lagi.')"><i class="fas fa-paper-plane"></i> Kumpulkan Jawaban</button>
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
