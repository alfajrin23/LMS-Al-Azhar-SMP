@php
    $gradeLetter = fn($v) => $v >= 90 ? 'A' : ($v >= 85 ? 'A-' : ($v >= 80 ? 'B+' : ($v >= 75 ? 'B' : ($v >= 70 ? 'B-' : 'C'))));
    $gradeColor = fn($v) => $v >= 90 ? 'grade-A' : ($v >= 80 ? 'grade-B' : 'grade-C');
@endphp
<div class="content-header">
    <h1>Nilai Anak</h1>
    <div class="header-right">
        <select x-model="childId" class="child-select">
            @foreach($anak as $a)
            <option value="{{ $a->id }}">{{ $a->nama }} &mdash; {{ $a->kelas->nama_kelas ?? 'N/A' }}</option>
            @endforeach
        </select>
        <div class="avatar orange">{{ strtoupper(substr($ortu?->nama ?? $user->name, 0, 2)) }}</div>
    </div>
</div>
@foreach($anak as $a)
@php
    $nilaiAnak = \App\Models\Nilai::where('siswa_id', $a->id)->with('mapel')->get();
    $rataNilai = $nilaiAnak->count() > 0 ? round($nilaiAnak->avg('nilai'), 1) : 0;
@endphp
<div x-show="childId == {{ $a->id }}">
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-file-invoice" style="color:var(--blue)"></i> Rapor {{ $a->nama }} — {{ $a->kelas->nama_kelas ?? '' }}</h3>
            <label style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer" @click="window.print()"><i class="fas fa-print"></i> Cetak</label>
        </div>
        <div class="table-wrap">
            <table class="rapor-table">
                <thead><tr><th>Mata Pelajaran</th><th>Nilai</th><th>Grade</th></tr></thead>
                <tbody>
                    @forelse($nilaiAnak as $n)
                    <tr><td>{{ $n->mapel->nama_mapel }}</td><td style="font-weight:700">{{ $n->nilai }}</td><td><span class="{{ $gradeColor($n->nilai) }}">{{ $gradeLetter($n->nilai) }}</span></td></tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--gray-400)">Belum ada nilai</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border-light);display:flex;gap:20px">
            <div><span style="font-size:12px;color:var(--gray-400)">Rata-rata</span><div style="font-size:20px;font-weight:700;color:var(--teal)">{{ $rataNilai }}</div></div>
            <div><span style="font-size:12px;color:var(--gray-400)">Tertinggi</span><div style="font-size:20px;font-weight:700;color:var(--green)">{{ $nilaiAnak->max('nilai') ?? '-' }}</div></div>
            <div><span style="font-size:12px;color:var(--gray-400)">Terendah</span><div style="font-size:20px;font-weight:700;color:var(--orange)">{{ $nilaiAnak->min('nilai') ?? '-' }}</div></div>
        </div>
    </div>
</div>
@endforeach
