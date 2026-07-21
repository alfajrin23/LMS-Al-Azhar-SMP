@extends('layouts.app')
@section('title', 'Tambah Soal CBT - LMS Al Azhar Jaya Indonesia')
@section('sidebar')
    <li>
        <a href="{{ route('dashboard') }}" style="color:var(--gray-500);text-decoration:none;display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:14px;border-radius:var(--radius-sm);transition:all 0.2s">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('dashboard') }}" style="color:var(--gray-500);text-decoration:none;display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:14px;border-radius:var(--radius-sm);transition:all 0.2s">
            <i class="fas fa-laptop"></i> CBT / Ulangan
        </a>
    </li>
    <li>
        <a href="{{ route('dashboard') }}" style="color:var(--gray-500);text-decoration:none;display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:14px;border-radius:var(--radius-sm);transition:all 0.2s">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </li>
@endsection
@section('content')
@php
    $guru = auth()->user()->guru;
@endphp
<div class="content-header">
    <div>
        <h1>Tambah Soal: {{ $cbtExam->judul }}</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">{{ strtoupper($cbtExam->tipe) }} &mdash; {{ $cbtExam->mapel->nama_mapel ?? '' }} &mdash; {{ $cbtExam->jumlah_soal }} soal &mdash; {{ $cbtExam->durasi }} menit</p>
    </div>
    <div class="header-right">
        <a href="{{ route('dashboard') }}" class="header-btn outline" style="text-decoration:none;cursor:pointer"><i class="fas fa-arrow-left"></i> Kembali</a>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
@if($cbtExam->status === 'draft')
<div class="card" style="margin-bottom:20px">
    <div class="card-header"><h3><i class="fas fa-plus-circle" style="color:var(--teal)"></i> Tambah Soal Baru</h3></div>
    <form method="POST" action="{{ route('guru.cbt.store-soal', $cbtExam->id) }}" style="padding:4px 0" x-data="{ tipe: 'pg' }">
        @csrf
        <div class="form-group" style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Soal</label>
            <textarea name="soal" required rows="3" placeholder="Tulis soal..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tipe Soal</label>
            <div style="display:flex;gap:16px">
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                    <input type="radio" name="tipe" value="pg" x-model="tipe" style="accent-color:var(--teal)">
                    <span style="font-size:14px">Pilihan Ganda</span>
                </label>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                    <input type="radio" name="tipe" value="essay" x-model="tipe" style="accent-color:var(--teal)">
                    <span style="font-size:14px">Essay</span>
                </label>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tingkat Kesulitan</label>
            <select name="kesulitan" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                <option value="sedang">Sedang</option>
                <option value="mudah">Mudah</option>
                <option value="sulit">Sulit</option>
            </select>
        </div>
        <div x-show="tipe === 'pg'" x-transition>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
                <div><label style="font-size:11px;color:var(--gray-400);font-weight:600">Pilihan A</label><input type="text" name="pilihan_a" placeholder="Opsi A" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)"></div>
                <div><label style="font-size:11px;color:var(--gray-400);font-weight:600">Pilihan B</label><input type="text" name="pilihan_b" placeholder="Opsi B" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)"></div>
                <div><label style="font-size:11px;color:var(--gray-400);font-weight:600">Pilihan C</label><input type="text" name="pilihan_c" placeholder="Opsi C" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)"></div>
                <div><label style="font-size:11px;color:var(--gray-400);font-weight:600">Pilihan D</label><input type="text" name="pilihan_d" placeholder="Opsi D" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-family:var(--font)"></div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-400);margin-bottom:4px">Jawaban Benar</label>
                <div style="display:flex;gap:16px">
                    @foreach(['a','b','c','d'] as $opt)
                    <label style="display:flex;align-items:center;gap:4px;cursor:pointer">
                        <input type="radio" name="jawaban_benar" value="{{ $opt }}" style="accent-color:var(--teal)">
                        <span style="font-size:14px;text-transform:uppercase">{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-save"></i> Simpan Soal</button>
    </form>
</div>
@endif
@if($soals->count() > 0)
<div class="card">
    <div class="card-header"><h3><i class="fas fa-list" style="color:var(--blue)"></i> Daftar Soal ({{ $soals->count() }})</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>No</th><th>Soal</th><th>Tipe</th><th>Kesulitan</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($soals as $s)
                <tr>
                    <td>{{ $s->nomor }}</td>
                    <td style="max-width:300px">{{ Str::limit($s->soal, 100) }}</td>
                    <td><span class="badge {{ $s->tipe === 'pg' ? 'blue' : 'orange' }} light" style="font-size:10px">{{ $s->tipe }}</span></td>
                    <td>
                        <span class="badge {{ $s->kesulitan === 'mudah' ? 'green' : ($s->kesulitan === 'sulit' ? 'red' : 'orange') }} light" style="font-size:10px;text-transform:capitalize">
                            {{ $s->kesulitan ?? 'sedang' }}
                        </span>
                    </td>
                    <td>
                        @if($cbtExam->status === 'draft')
                        <form method="POST" action="{{ route('guru.cbt.delete-soal', [$cbtExam->id, $s->id]) }}" style="display:inline" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-small outline" style="cursor:pointer;border:none;font-family:var(--font);color:var(--red);border-color:var(--red);background:none"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@if($cbtExam->status === 'draft' && $cbtExam->jumlah_soal > 0)
<div style="margin-top:16px;text-align:center">
    <form method="POST" action="{{ route('guru.cbt.ajukan', $cbtExam->id) }}" style="display:inline">
        @csrf
        <button type="submit" class="btn-login" style="width:auto;padding:10px 32px;display:inline-block;border:none;cursor:pointer"><i class="fas fa-paper-plane"></i> Ajukan ke Admin</button>
    </form>
</div>
@endif
@if(session('success'))
    <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@endsection
