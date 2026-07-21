@php
    $exams = \App\Models\CbtExam::whereIn('status', ['pending', 'approved', 'rejected'])
        ->with('mapel', 'kelas', 'guru.user', 'approvedBy')
        ->latest()
        ->get();
@endphp
<div class="content-header">
    <div>
        <h1>Persetujuan Ujian CBT</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Setujui atau tolak ujian yang diajukan guru</p>
    </div>
    <div class="header-right">
        <div class="avatar teal">AD</div>
    </div>
</div>
@if($exams && $exams->count() > 0)
<div class="card">
    <div class="card-header"><h3><i class="fas fa-laptop" style="color:var(--blue)"></i> Daftar Pengajuan Ujian</h3></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Guru</th>
                    <th>Soal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td><strong>{{ $exam->judul }}</strong></td>
                    <td><span class="badge {{ $exam->tipe === 'uts' ? 'orange' : ($exam->tipe === 'uas' ? 'purple' : 'teal') }} light" style="font-size:10px">{{ strtoupper($exam->tipe) }}</span></td>
                    <td>{{ $exam->mapel->kode ?? '-' }}</td>
                    <td>{{ $exam->kelas->nama_kelas ?? 'Semua' }}</td>
                    <td>{{ $exam->guru->user->name ?? $exam->guru->nama ?? '-' }}</td>
                    <td>{{ $exam->jumlah_soal }}</td>
                    <td>
                        @if($exam->status === 'pending')
                            <span class="badge light orange">Pending</span>
                        @elseif($exam->status === 'approved')
                            <span class="badge light green">Disetujui</span>
                        @elseif($exam->status === 'rejected')
                            <span class="badge light red">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        @if($exam->status === 'pending')
                        <div style="display:flex;gap:4px;flex-wrap:wrap">
                            <form method="POST" action="{{ route('admin.cbt.approve', $exam->id) }}" style="display:inline" onsubmit="return confirm('Setujui ujian ini?')">
                                @csrf
                                <button type="submit" class="btn-small teal" style="cursor:pointer;border:none;font-family:var(--font)"><i class="fas fa-check"></i> Setujui</button>
                            </form>
                            <button @click="selectedExam = '{{ $exam->id }}'" class="btn-small outline" style="cursor:pointer;border:none;font-family:var(--font);color:var(--red);border-color:var(--red);background:none"><i class="fas fa-times"></i> Tolak</button>
                        </div>
                        <div x-show="selectedExam === '{{ $exam->id }}'" x-transition style="margin-top:8px">
                            <form method="POST" action="{{ route('admin.cbt.reject', $exam->id) }}" style="display:flex;gap:6px;flex-direction:column">
                                @csrf
                                <textarea name="catatan_reject" rows="2" placeholder="Alasan penolakan..." required style="width:100%;padding:6px 10px;border:1px solid var(--red);border-radius:var(--radius-sm);font-size:12px;font-family:var(--font);resize:vertical"></textarea>
                                <div style="display:flex;gap:4px">
                                    <button type="submit" class="btn-small" style="cursor:pointer;border:none;font-family:var(--font);background:var(--red);color:#fff"><i class="fas fa-times"></i> Tolak</button>
                                    <button type="button" @click="selectedExam = null" class="btn-small outline" style="cursor:pointer;border:none;font-family:var(--font)">Batal</button>
                                </div>
                            </form>
                        </div>
                        @elseif($exam->status === 'rejected' && $exam->catatan_reject)
                            <span style="font-size:11px;color:var(--red)" title="{{ $exam->catatan_reject }}"><i class="fas fa-comment"></i> {{ Str::limit($exam->catatan_reject, 30) }}</span>
                        @elseif($exam->status === 'approved')
                            <span style="font-size:11px;color:var(--gray-400)">oleh {{ $exam->approvedBy->name ?? 'Admin' }} ({{ $exam->approved_at ? \Carbon\Carbon::parse($exam->approved_at)->format('d M Y') : '-' }})</span>
                        @endif
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
        <i class="fas fa-inbox" style="font-size:48px;margin-bottom:16px;opacity:0.3"></i>
        <p>Belum ada pengajuan ujian</p>
    </div>
</div>
@endif
@if(session('success'))
    <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
