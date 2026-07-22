@php
    $summary = $kehadiranSummary ?? [
        'total_hari_efektif' => 0,
        'hadir' => 0,
        'sakit' => 0,
        'izin' => 0,
        'alpha' => 0,
        'total_tidak_hadir' => 0,
        'persentase_hadir' => 0,
        'tidak_hadir_records' => collect(),
    ];
    $raporAkademik = $rapors?->get('akademik');
    $raporEnglish = $rapors?->get('english');
    $raporQuran = $rapors?->get('quran');
    $cards = [
        'akademik' => [
            'label' => 'Rapor Akademik',
            'icon' => 'fa-file-invoice',
            'color' => 'var(--blue)',
            'status' => $raporAkademik?->status ?? 'draft data',
            'count' => $raporAkademik?->items?->where('kategori', 'akademik')->count() ?? $nilaiSekolah->count(),
            'meta' => 'Mata pelajaran',
        ],
        'english' => [
            'label' => 'Rapor English',
            'icon' => 'fa-language',
            'color' => 'var(--orange)',
            'status' => $raporEnglish?->status ?? 'draft data',
            'count' => $raporEnglish?->items?->where('kategori', 'english')->count() ?? 0,
            'meta' => 'Komponen',
        ],
        'quran' => [
            'label' => 'Rapor Quran',
            'icon' => 'fa-quran',
            'color' => 'var(--teal)',
            'status' => $raporQuran?->status ?? 'draft data',
            'count' => $raporQuran?->items?->whereIn('kategori', ['quran_reading', 'quran_surah'])->count() ?? 0,
            'meta' => 'Penilaian',
        ],
    ];
@endphp

<div class="sd-content sd-content-rapor">
    <div class="content-header">
        <div>
            <h1>Rapor Semester</h1>
            <p style="font-size:14px;color:var(--gray-400);margin-top:2px">{{ $siswa->nama }} - {{ $kelas?->nama_kelas ?? '-' }}</p>
        </div>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
        </div>
    </div>

    <div class="grid-3" style="margin-bottom:20px">
        @foreach($cards as $jenis => $card)
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas {{ $card['icon'] }}" style="color:{{ $card['color'] }}"></i> {{ $card['label'] }}</h3>
                    <span class="badge light {{ ($card['status'] ?? '') === 'published' ? 'green' : 'orange' }}">{{ ucfirst($card['status']) }}</span>
                </div>
                <div style="font-size:28px;font-weight:800;color:{{ $card['color'] }}">{{ $card['count'] }}</div>
                <p style="font-size:12px;color:var(--gray-400);margin:4px 0 14px">{{ $card['meta'] }} tersedia dari database.</p>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="{{ route('rapor.show', $jenis) }}" class="btn-small outline" style="text-decoration:none"><i class="fas fa-eye"></i> Lihat</a>
                    <a href="{{ route('rapor.jenis.pdf', $jenis) }}" class="btn-small outline" style="text-decoration:none"><i class="fas fa-file-pdf"></i> PDF</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-calendar-check" style="color:var(--teal)"></i> Rekap Kehadiran</h3></div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
                <div><span style="font-size:12px;color:var(--gray-400)">Hari Efektif</span><div style="font-weight:800">{{ $summary['total_hari_efektif'] }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Hadir</span><div style="font-weight:800;color:var(--teal)">{{ $summary['hadir'] }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Tidak Hadir</span><div style="font-weight:800;color:var(--red)">{{ $summary['total_tidak_hadir'] }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Sakit</span><div style="font-weight:700">{{ $summary['sakit'] }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Izin</span><div style="font-weight:700">{{ $summary['izin'] }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Alpha</span><div style="font-weight:700">{{ $summary['alpha'] }}</div></div>
            </div>
            <div class="progress-wrap" style="margin-top:14px">
                <div class="progress-label"><span>Persentase Hadir</span><span class="percentage">{{ number_format($summary['persentase_hadir'], 2, ',', '.') }}%</span></div>
                <div class="progress-bar"><div class="fill" style="width:{{ min(100, $summary['persentase_hadir']) }}%"></div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3><i class="fas fa-star" style="color:var(--orange)"></i> Catatan Wali Kelas</h3></div>
            @if($catatanWali)
                <p style="font-size:14px;color:var(--gray-500);line-height:1.7;font-style:italic">"{{ $catatanWali->catatan }}"</p>
                <p style="font-size:12px;color:var(--gray-400);margin-top:8px">{{ $catatanWali->guru->nama ?? 'Wali Kelas' }}</p>
            @else
                <p style="font-size:14px;color:var(--gray-400);font-style:italic">Belum ada catatan wali kelas.</p>
            @endif
        </div>
    </div>
</div>
