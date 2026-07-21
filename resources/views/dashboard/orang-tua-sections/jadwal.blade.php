<div class="content-header">
    <h1>Jadwal Pelajaran</h1>
    <div class="header-right">
        <select x-model="childId" class="child-select">
            @foreach($anak as $a)
            <option value="{{ $a->id }}">{{ $a->nama }} &mdash; {{ $a->kelas->nama_kelas ?? 'N/A' }}</option>
            @endforeach
        </select>
        <div class="avatar orange">{{ strtoupper(substr($ortu?->nama ?? $user->name, 0, 2)) }}</div>
    </div>
</div>
@php
    $hariIndo = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
@endphp
@foreach($anak as $a)
@php
    $semuaJadwal = \App\Models\Jadwal::where('kelas_id', $a->kelas_id)
        ->with('mapel', 'guru')
        ->orderBy('hari')
        ->orderBy('jam_mulai')
        ->get()
        ->groupBy('hari');
@endphp
<div x-show="childId == {{ $a->id }}">
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-calendar-alt" style="color:var(--teal)"></i> Jadwal Pelajaran — {{ $a->kelas->nama_kelas ?? 'Kelas ' . $a->kelas_id }}</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Jam</th>
                    @foreach($hariIndo as $hari)<th>{{ $hari }}</th>@endforeach
                </tr></thead>
                <tbody>
                    @php
                        $jamList = [];
                        foreach ($hariIndo as $hari) {
                            if (isset($semuaJadwal[$hari])) {
                                foreach ($semuaJadwal[$hari] as $j) {
                                    $jamList[$j->jam_mulai . '-' . $j->jam_selesai] = true;
                                }
                            }
                        }
                        ksort($jamList);
                    @endphp
                    @forelse(array_keys($jamList) as $jam)
                    <tr>
                        <td><strong>{{ $jam }}</strong></td>
                        @foreach($hariIndo as $hari)
                            @php
                                $mapelHari = isset($semuaJadwal[$hari]) ? $semuaJadwal[$hari]->first(fn($j) => ($j->jam_mulai . '-' . $j->jam_selesai) === $jam) : null;
                            @endphp
                            <td>{{ $mapelHari ? $mapelHari->mapel->nama_mapel : '&mdash;' }}</td>
                        @endforeach
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--gray-400)">Belum ada jadwal</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
