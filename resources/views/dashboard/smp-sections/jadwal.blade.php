@php
    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    // Ambil semua rentang jam unik dari jadwal siswa ini
    $jamHeaders = $semuaJadwal->map(function($j) {
        return substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5);
    })->unique()->sort()->values();
@endphp
<div class="content-header">
    <h1>Jadwal Pelajaran</h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h3><i class="fas fa-calendar-alt" style="color:var(--teal)"></i> Jadwal Mingguan</h3></div>
    <div class="table-wrap" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr>
                    <th style="padding: 12px 15px; background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; text-align: center; font-weight: 600;">Hari</th>
                    @foreach($jamHeaders as $jh)
                        <th style="padding: 12px 10px; background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; text-align: center; font-size: 0.85rem; white-space: nowrap;">
                            <i class="far fa-clock" style="color: var(--teal, #008080); margin-right: 4px;"></i> {{ $jh }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                @php $dayJadwal = $semuaJadwal->where('hari', $day); @endphp
                <tr>
                    <td style="padding: 12px 15px; border: 1px solid #dee2e6; text-align: center; background: #fff; font-weight: bold; color: #333;">
                        {{ $day }}
                    </td>
                    @foreach($jamHeaders as $jh)
                        @php
                            $match = $dayJadwal->first(function($j) use ($jh) {
                                return (substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5)) === $jh;
                            });
                        @endphp
                        <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; vertical-align: middle; background: {{ $match ? '#f0f8ff' : '#fafafa' }};">
                            @if($match)
                                <span style="background: var(--teal, #008080); color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; display: inline-block; white-space: nowrap; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                    {{ $match->mapel->nama_mapel }}
                                </span>
                            @else
                                <span style="color: #ddd;">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
