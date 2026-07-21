@php
    $siswa = auth()->user()->siswa;
    $exams = \App\Models\CbtExam::where('status', 'approved')
        ->where(function($q) use ($siswa) {
            $q->where('kelas_id', $siswa->kelas_id)->orWhereNull('kelas_id');
        })
        ->with('mapel')
        ->latest()
        ->get();
    foreach ($exams as $exam) {
        $exam->sudah_dikerjakan = \App\Models\CbtJawaban::whereIn('cbt_soal_id', $exam->soals->pluck('id'))
            ->where('siswa_id', $siswa->id)
            ->exists();
    }
@endphp
<div class="content-header">
    <div>
        <h1>Ujian CBT</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Computer Based Test &mdash; Ujian berbasis komputer</p>
    </div>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
    </div>
</div>
@forelse($exams as $exam)
<div class="card" style="margin-bottom:14px">
    <div class="card-header">
        <h3><i class="fas fa-laptop" style="color:var(--blue)"></i> {{ $exam->judul }}</h3>
        <div style="display:flex;gap:8px;align-items:center">
                    <span class="badge {{ $exam->tipe === 'uts' ? 'orange' : ($exam->tipe === 'uas' ? 'purple' : 'teal') }} light" style="font-size:10px">{{ strtoupper($exam->tipe) }}</span>
                    <span class="badge blue">{{ $exam->mapel->kode ?? '-' }}</span>
                    <span class="badge teal">{{ $exam->jumlah_soal }} soal</span>
                    <span class="badge orange">{{ $exam->durasi }} menit</span>
                </div>
    </div>
    <div style="padding:4px 0;font-size:13px;color:var(--gray-500)">
        {{ $exam->deskripsi ?: '' }}
    </div>
    <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between">
        <span style="font-size:13px;color:var(--gray-400)">
            @if($exam->sudah_dikerjakan)
            <i class="fas fa-check-circle" style="color:var(--green)"></i> Sudah dikerjakan
            @else
            <i class="fas fa-clock" style="color:var(--orange)"></i> Belum dikerjakan
            @endif
        </span>
        @if($exam->sudah_dikerjakan)
            <a href="{{ route('siswa.cbt.hasil', $exam->id) }}" class="btn-small teal" style="text-decoration:none"><i class="fas fa-eye"></i> Lihat Hasil</a>
        @else
            <a href="{{ route('siswa.cbt.kerjakan', $exam->id) }}" class="btn-small teal" style="text-decoration:none"><i class="fas fa-pencil-alt"></i> Kerjakan</a>
        @endif
    </div>
</div>
@empty
<div class="card">
    <div style="padding:40px;text-align:center;color:var(--gray-400)">
        <i class="fas fa-laptop" style="font-size:48px;margin-bottom:16px;opacity:0.3"></i>
        <p>Belum ada ujian tersedia</p>
    </div>
</div>
@endforelse
