@extends('layouts.app')
@section('title', 'Hasil Workbook - LMS Al Azhar Jaya Indonesia')
@section('sidebar')
    <li>
        <a href="{{ route('siswa.workbook.index') }}"><i class="fas fa-arrow-left"></i> Kembali</a>
    </li>
@endsection
@section('content')
<div class="content-header">
    <div>
        <h1>Hasil: {{ $workbook->judul }}</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">{{ $workbook->mapel->nama_mapel ?? '' }}</p>
    </div>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="grid-3" style="margin-bottom:20px">
    <div class="stat-card centered">
        <div class="stat-icon-wrap teal"><i class="fas fa-check-circle"></i></div>
        <div class="stat-number">{{ $correctPG }}/{{ $totalPG }}</div>
        <div class="stat-label">PG Benar</div>
    </div>
    <div class="stat-card centered">
        <div class="stat-icon-wrap blue"><i class="fas fa-question-circle"></i></div>
        <div class="stat-number">{{ $soals->where('tipe', 'pg')->count() - $correctPG }}/{{ $totalPG }}</div>
        <div class="stat-label">PG Salah</div>
    </div>
    <div class="stat-card centered">
        <div class="stat-icon-wrap orange"><i class="fas fa-clock"></i></div>
        <div class="stat-number">{{ $soals->where('tipe', 'essay')->count() }}</div>
        <div class="stat-label">Essay (Menunggu Nilai)</div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list" style="color:var(--teal)"></i> Detail Jawaban</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pertanyaan</th>
                    <th>Jawaban Kamu</th>
                    <th>Jawaban Benar</th>
                    <th>Nilai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($soals as $index => $soal)
                @php $j = $jawaban->get($soal->id); @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="max-width:200px">{{ Str::limit($soal->soal, 60) }}</td>
                    <td>
                        @if($j)
                            @if($soal->tipe === 'pg')
                                {{ strtoupper($j->jawaban) }}
                            @else
                                {{ Str::limit($j->jawaban, 40) }}
                            @endif
                        @else
                            <span style="color:var(--gray-400)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($soal->tipe === 'pg')
                            <strong>{{ strtoupper($soal->jawaban_benar) }}</strong>
                        @else
                            <span style="color:var(--gray-400)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($j && $j->nilai !== null)
                            <strong>{{ $j->nilai }}</strong>
                        @else
                            <span style="color:var(--gray-400)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($soal->tipe === 'pg')
                            @if($j && $j->nilai === 100)
                                <span class="badge light green">Benar</span>
                            @elseif($j && $j->nilai === 0)
                                <span class="badge light red">Salah</span>
                            @else
                                <span style="color:var(--gray-400)">—</span>
                            @endif
                        @else
                            @if($j && $j->nilai !== null)
                                <span class="badge light green">Dinilai ({{ $j->nilai }})</span>
                            @elseif($j)
                                <span class="badge light orange">Menunggu</span>
                            @else
                                <span style="color:var(--gray-400)">—</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div style="margin-top:20px;text-align:center">
    <a href="{{ route('siswa.workbook.index') }}" class="btn-login" style="text-decoration:none;width:auto;padding:10px 32px;display:inline-block;border:none;cursor:pointer"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>
@endsection
