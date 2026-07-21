@php
    $statusClass = function($deadline) {
        $d = \Carbon\Carbon::parse($deadline);
        if ($d->isPast()) return 'status-selesai';
        if ($d->diffInDays(now()) <= 3) return 'status-mendekati';
        return 'red';
    };
    $statusLabel = function($deadline) {
        $d = \Carbon\Carbon::parse($deadline);
        if ($d->isPast()) return 'Selesai';
        if ($d->diffInDays(now()) <= 3) return 'Mendekati';
        return 'Akan Datang';
    };
@endphp
<div class="sd-content sd-content-ulangan">
  <div class="content-header"><h1>Ulangan <span>SDIT {{ setting('school_name') }}</span></h1><div class="header-right"><div class="avatar red">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div></div></div>
  <div class="card"><div class="card-header"><h3><i class="fas fa-pencil-alt" style="color:var(--orange)"></i> Jadwal Ulangan</h3></div>
    <div class="table-wrap"><table><thead><tr><th>Nama Ulangan</th><th>Mapel</th><th>Tanggal</th><th>Status</th></tr></thead>
    <tbody>
      @forelse($ulangan as $u)
      <tr><td><strong>{{ $u->judul }}</strong></td><td>{{ $u->mapel->kode }}</td><td>{{ \Carbon\Carbon::parse($u->tanggal_deadline)->format('d M Y') }}</td><td><span class="badge {{ $statusClass($u->tanggal_deadline) }}">{{ $statusLabel($u->tanggal_deadline) }}</span></td></tr>
      @empty
      <tr><td colspan="4" style="text-align:center;color:var(--gray-400)">Tidak ada ulangan</td></tr>
      @endforelse
    </tbody></table></div>
  </div>
</div>
