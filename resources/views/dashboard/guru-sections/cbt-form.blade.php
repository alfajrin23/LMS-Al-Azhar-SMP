@extends('layouts.app')

@section('title', 'Buat Ujian CBT - LMS Al Azhar Jaya Indonesia')

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
    $mapels = \App\Models\Mapel::akademik()->get();
    $kelasList = \App\Models\Kelas::all();
@endphp
<div class="content-header">
    <div>
        <h1>Buat Ujian CBT Baru</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Buat ujian berbasis komputer untuk siswa</p>
    </div>
    <div class="header-right">
        <a href="{{ route('dashboard') }}" class="header-btn outline" style="text-decoration:none;cursor:pointer"><i class="fas fa-arrow-left"></i> Kembali</a>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>

<div class="card" style="max-width:600px" x-data="{ generateOtomatis: false }">
    <div class="card-header"><h3><i class="fas fa-plus" style="color:var(--teal)"></i> Informasi Ujian</h3></div>
    
    @if(session('error'))
        <div style="background:var(--red-light);color:var(--red);padding:10px 14px;border-radius:var(--radius-sm);margin:10px;font-size:13px;font-weight:600">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('guru.cbt.store') }}" style="padding:4px 0">
        @csrf
        <div class="form-group" style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Judul Ujian</label>
            <input type="text" name="judul" required placeholder="Contoh: UTS Matematika Kelas 8" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tipe Ujian</label>
            <select name="tipe" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                <option value="ulangan">Ulangan Harian</option>
                <option value="uts">UTS (Mid Semester)</option>
                <option value="uas">UAS (Akhir Semester)</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Metode Pengerjaan</label>
            <div style="display:flex;gap:20px;margin-top:4px">
                <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer">
                    <input type="radio" name="metode" value="online" checked required>
                    <i class="fas fa-laptop" style="color:var(--teal)"></i> Ujian Online (CBT)
                </label>
                <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer">
                    <input type="radio" name="metode" value="cetak" required>
                    <i class="fas fa-print" style="color:var(--blue)"></i> Ujian Kertas (Cetak PDF)
                </label>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:14px;background:var(--gray-100);padding:10px;border-radius:var(--radius-sm)">
            <label style="display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;cursor:pointer">
                <input type="checkbox" name="generate_otomatis" x-model="generateOtomatis">
                <i class="fas fa-magic" style="color:var(--purple)"></i> Generate Soal Otomatis dari Bank Soal
            </label>
            
            <div x-show="generateOtomatis" style="margin-top:12px;border-top:1px solid var(--border-light);padding-top:10px" x-transition>
                <div class="form-group" style="margin-bottom:10px">
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Jumlah Soal</label>
                    <input type="number" name="jumlah_soal_gen" min="1" max="100" value="10" :required="generateOtomatis" style="width:100%;padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px">
                </div>
                <div style="display:flex;gap:10px">
                    <div style="flex:1">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--gray-500);margin-bottom:2px">% Mudah</label>
                        <input type="number" name="persen_mudah" min="0" max="100" value="30" :required="generateOtomatis" style="width:100%;padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px">
                    </div>
                    <div style="flex:1">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--gray-500);margin-bottom:2px">% Sedang</label>
                        <input type="number" name="persen_sedang" min="0" max="100" value="50" :required="generateOtomatis" style="width:100%;padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px">
                    </div>
                    <div style="flex:1">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--gray-500);margin-bottom:2px">% Sulit</label>
                        <input type="number" name="persen_sulit" min="0" max="100" value="20" :required="generateOtomatis" style="width:100%;padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px">
                    </div>
                </div>
                <small style="display:block;margin-top:6px;color:var(--gray-400);font-size:11px">Total persentase harus berjumlah 100%.</small>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Deskripsi (opsional)</label>
            <textarea name="deskripsi" rows="3" placeholder="Deskripsi ujian..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
        </div>
        <div style="display:flex;gap:10px;margin-bottom:14px">
            <div class="form-group" style="flex:1">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Mata Pelajaran</label>
                <select name="mapel_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                    <option value="">Pilih Mapel</option>
                    @foreach($mapels as $m)
                    <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->kode }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="flex:1">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Kelas (opsional)</label>
                <select name="kelas_id" class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Durasi (menit)</label>
            <input type="number" name="durasi" required value="60" min="1" max="300" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
        </div>
        <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-save"></i> Simpan &amp; Lanjutkan</button>
    </form>
</div>
@endsection
