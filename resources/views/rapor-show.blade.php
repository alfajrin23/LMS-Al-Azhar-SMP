@extends('layouts.app')

@section('title', 'Rapor '.ucfirst($jenis).' - '.$siswa->nama)

@section('content')
<style>
    .rapor-shell { max-width: 960px; margin: 0 auto; }
    .rapor-toolbar { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; }
    .rapor-tabs { display:flex; flex-wrap:wrap; gap:8px; }
    .rapor-tabs a { padding:8px 12px; border:1px solid var(--border); border-radius:6px; text-decoration:none; color:var(--gray-600); font-weight:700; font-size:13px; }
    .rapor-tabs a.active { background:var(--blue); border-color:var(--blue); color:white; }
    .rapor-doc { background:white; border:1px solid var(--border-light); border-radius:8px; padding:24px; box-shadow:var(--shadow); }
    .rapor-header { text-align:center; border-bottom:3px double #2563eb; padding-bottom:14px; margin-bottom:18px; }
    .rapor-header h1 { margin:0; font-size:22px; color:#1d4ed8; }
    .rapor-header p { margin:4px 0 0; font-size:12px; color:#64748b; }
    .rapor-section { margin-bottom:18px; }
    .rapor-section h2 { font-size:15px; color:#1d4ed8; margin:0 0 10px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; }
    .rapor-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:10px; }
    .rapor-grid.compact { grid-template-columns:repeat(3, minmax(0, 1fr)); }
    .rapor-grid span, .signature-grid span { display:block; color:#64748b; font-size:12px; margin-bottom:2px; }
    .rapor-grid strong { color:#0f172a; font-size:14px; }
    .rapor-doc table { width:100%; border-collapse:collapse; font-size:12px; }
    .rapor-doc th, .rapor-doc td { border:1px solid #dbe3ef; padding:7px 8px; vertical-align:top; }
    .rapor-doc th { background:#f8fafc; color:#334155; }
    .rapor-doc .num { text-align:center; font-weight:700; }
    .rapor-doc .empty { text-align:center; color:#94a3b8; padding:16px; }
    .rapor-doc .note { color:#475569; line-height:1.6; font-style:italic; margin:8px 0 0; }
    .signature-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:18px; margin-top:32px; text-align:center; }
    .signature-grid strong { display:block; padding-top:52px; border-bottom:1px solid #94a3b8; min-height:24px; }
    @media (max-width: 720px) {
        .rapor-grid, .rapor-grid.compact, .signature-grid { grid-template-columns:1fr; }
        .rapor-toolbar { align-items:flex-start; flex-direction:column; }
    }
</style>
<div class="rapor-shell">
    <div class="rapor-toolbar">
        <div class="rapor-tabs">
            <a href="{{ route('rapor.show', ['jenis' => 'akademik'] + request()->only('siswa_id')) }}" class="{{ $jenis === 'akademik' ? 'active' : '' }}">Akademik</a>
            <a href="{{ route('rapor.show', ['jenis' => 'english'] + request()->only('siswa_id')) }}" class="{{ $jenis === 'english' ? 'active' : '' }}">English</a>
            <a href="{{ route('rapor.show', ['jenis' => 'quran'] + request()->only('siswa_id')) }}" class="{{ $jenis === 'quran' ? 'active' : '' }}">Quran</a>
        </div>
        <a class="header-btn primary" href="{{ route('rapor.jenis.pdf', ['jenis' => $jenis] + request()->only('siswa_id')) }}"><i class="fas fa-file-pdf"></i> PDF</a>
    </div>
    @include('partials.rapor-content')
</div>
@endsection
