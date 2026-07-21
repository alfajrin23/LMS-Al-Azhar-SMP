@php $cardColors = ['green', 'teal', 'blue', 'orange light', 'purple light', 'cyan light', 'pink light']; @endphp
<div class="content-header">
    <h1>Mata Pelajaran <span>SMPIT {{ setting('school_name') }}</span></h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="grid-2">
    @foreach($mapels as $m)
    @php
        $materiSiswa = \App\Models\Materi::where('mapel_id', $m->id)
            ->where('status', 'approved')
            ->where(function($q) use ($siswa) {
                $q->where('kelas_id', $siswa->kelas_id)->orWhereNull('kelas_id');
            })
            ->get();
    @endphp
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-book" style="color:var(--{{ $cardColors[$loop->index % count($cardColors)] }})"></i> {{ $m->nama_mapel }}</h3></div>
        <p style="font-size:13px;color:var(--gray-400);margin-bottom:8px">Kode: {{ $m->kode }}</p>
        @if($materiSiswa->count() > 0)
        <div style="margin-top:12px;border-top:1px solid var(--border-light);padding-top:10px">
            <span style="font-size:11px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:0.5px">Materi Ajar &amp; Modul</span>
            <ul style="list-style:none;padding:0;margin:6px 0 0 0;display:flex;flex-direction:column;gap:8px">
                @foreach($materiSiswa as $mat)
                <li style="display:flex;flex-direction:column;gap:2px">
                        <div style="display:flex;flex-direction:column;gap:2px">
                            <span style="font-size:13px;font-weight:600">
                                <i class="fas fa-file-pdf" style="color:var(--red);margin-right:4px"></i>
                                <a href="{{ route('materi.download', $mat->id) }}" target="_blank" style="text-decoration:none;color:var(--blue)">{{ $mat->judul }}</a>
                            </span>
                            <div style="font-size: 10px; color: var(--gray-400); margin-left: 18px;">
                                <i class="fas fa-info-circle"></i> Klik nama materi untuk mengunduh
                            </div>
                        </div>
                    @if($mat->deskripsi)
                    <p style="font-size:11px;color:var(--gray-500);margin:0 0 0 16px;line-height:1.3">{{ $mat->deskripsi }}</p>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <div style="margin-top:12px;border-top:1px solid var(--border-light);padding-top:8px;font-size:11px;color:var(--gray-400);font-style:italic">
            Belum ada materi ajar yang diunggah.
        </div>
        @endif
    </div>
    @endforeach
</div>
