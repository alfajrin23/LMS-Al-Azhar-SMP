@php
    $exams = \App\Models\CbtExam::where('guru_id', $guru->id)->with('mapel', 'kelas')->latest()->get();
@endphp
<div class="content-header">
    <h1>Computer Based Test (CBT)</h1>
    <div class="header-right">
        <a href="{{ route('guru.cbt.create') }}" class="header-btn primary" style="text-decoration:none;cursor:pointer"><i class="fas fa-plus"></i> Buat Ujian Baru</a>
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>
@if($exams && $exams->count() > 0)
<div class="card">
    <div class="card-header"><h3><i class="fas fa-laptop" style="color:var(--blue)"></i> Daftar Ujian CBT</h3></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Metode</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Jumlah Soal</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td><strong>{{ $exam->judul }}</strong></td>
                    <td><span class="badge {{ $exam->tipe === 'uts' ? 'orange' : ($exam->tipe === 'uas' ? 'purple' : 'teal') }} light" style="font-size:10px">{{ strtoupper($exam->tipe) }}</span></td>
                    <td>
                        <span class="badge {{ $exam->metode === 'cetak' ? 'blue' : 'teal' }} light" style="font-size:10px;text-transform:capitalize">
                            <i class="fas {{ $exam->metode === 'cetak' ? 'fa-print' : 'fa-laptop' }}"></i> {{ $exam->metode ?? 'online' }}
                        </span>
                    </td>
                    <td>{{ $exam->mapel->kode ?? '-' }}</td>
                    <td>{{ $exam->kelas->nama_kelas ?? 'Semua' }}</td>
                    <td>{{ $exam->jumlah_soal }}</td>
                    <td>{{ $exam->durasi }} menit</td>
                    <td>
                        @if($exam->status === 'draft')
                            <span class="badge light teal">Draft</span>
                        @elseif($exam->status === 'pending')
                            <span class="badge light orange">Pending</span>
                        @elseif($exam->status === 'approved')
                            <span class="badge light green">Disetujui</span>
                        @elseif($exam->status === 'rejected')
                            <span class="badge light red">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap">
                            @if($exam->status === 'draft')
                                <a href="{{ route('guru.cbt.add-soal', $exam->id) }}" class="btn-small teal" style="text-decoration:none"><i class="fas fa-plus"></i> Soal</a>
                                <form method="POST" action="{{ route('guru.cbt.ajukan', $exam->id) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-small outline" style="cursor:pointer;border:none;font-family:var(--font)"><i class="fas fa-paper-plane"></i> Ajukan</button>
                                </form>
                            @elseif($exam->status === 'rejected')
                                <a href="{{ route('guru.cbt.add-soal', $exam->id) }}" class="btn-small teal" style="text-decoration:none"><i class="fas fa-edit"></i> Edit</a>
                                @if($exam->catatan_reject)
                                <span class="badge light red" style="font-size:10px;cursor:pointer" title="{{ $exam->catatan_reject }}">Pesan</span>
                                @endif
                            @else
                                <a href="{{ route('guru.cbt.add-soal', $exam->id) }}" class="btn-small outline" style="text-decoration:none"><i class="fas fa-eye"></i> Lihat</a>
                            @endif
                            <a href="{{ route('guru.cbt.print', $exam->id) }}" target="_blank" class="btn-small outline" style="text-decoration:none;border-color:var(--blue);color:var(--blue)"><i class="fas fa-print"></i> Cetak</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card">
    <div style="padding:40px;text-align:center;color:var(--gray-400)">
        <i class="fas fa-laptop" style="font-size:48px;margin-bottom:16px;opacity:0.3"></i>
        <p>Belum ada ujian CBT. Buat ujian baru untuk memulai.</p>
        <a href="{{ route('guru.cbt.create') }}" class="btn-login" style="text-decoration:none;display:inline-block;width:auto;padding:10px 28px;margin-top:12px;border:none;cursor:pointer"><i class="fas fa-plus"></i> Buat Ujian Baru</a>
    </div>
</div>
@endif
@if(session('success'))
    <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
