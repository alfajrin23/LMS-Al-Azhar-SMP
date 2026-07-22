@php
    $statusClass = function($deadline) {
        $d = \Carbon\Carbon::parse($deadline);
        if ($d->isPast()) return 'status-terlambat';
        if ($d->diffInDays(now()) <= 3) return 'status-mendekati';
        return 'red';
    };
    $statusLabel = function($deadline) {
        $d = \Carbon\Carbon::parse($deadline);
        if ($d->isPast()) return 'Terlambat';
        if ($d->diffInDays(now()) <= 3) return 'Mendekati';
        return 'Aktif';
    };
    $pengumpulanSaya = \App\Models\PengumpulanTugas::where('siswa_id', $siswa->id)->get()->keyBy('tugas_id');
@endphp
<div class="sd-content sd-content-tugas" x-data="{ showKumpul: false, kumpulTugasId: null, filterMapel: 'semua' }">
  <div class="content-header"><h1>Tugas <span>SMPIT {{ setting('school_name') }}</span></h1><div class="header-right"><div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div></div></div>
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3><i class="fas fa-tasks" style="color:var(--blue)"></i> Semua Tugas</h3>
        <select x-model="filterMapel" style="padding: 6px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); font-size: 13px; color: var(--gray-700); background: var(--gray-50); outline: none; cursor: pointer;">
            <option value="semua">Semua Mapel</option>
            @foreach($mapels as $m)
                <option value="{{ $m->kode }}">{{ $m->nama_mapel }}</option>
            @endforeach
        </select>
    </div>
    <div class="table-wrap"><table><thead><tr><th>Judul Tugas</th><th>Mapel</th><th>Deadline</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
      @forelse($tugas as $t)
      <tr x-show="filterMapel === 'semua' || filterMapel === '{{ $t->mapel->kode }}'" x-transition>
        <td><strong>{{ $t->judul }}</strong></td>
        <td>{{ $t->mapel->kode }}</td>
        <td>{{ \Carbon\Carbon::parse($t->tanggal_deadline)->format('d M Y') }}</td>
        <td><span class="badge {{ $statusClass($t->tanggal_deadline) }}">{{ $statusLabel($t->tanggal_deadline) }}</span></td>
        <td>
          @if($pengumpulanSaya->has($t->id))
            <span style="font-size:11px;color:var(--blue);font-weight:600"><i class="fas fa-check-circle"></i> Sudah dikumpulkan</span>
          @else
            <button @click="showKumpul = true; kumpulTugasId = '{{ $t->id }}'" class="btn-small" style="cursor:pointer;border:none;font-size:11px;padding:4px 10px;border-radius:var(--radius-sm);background:var(--blue);color:#fff"><i class="fas fa-upload"></i> Kumpulkan</button>
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:var(--gray-400)">Tidak ada tugas</td></tr>
      @endforelse
    </tbody></table></div>
  </div>
  @if($pengumpulanSaya->isNotEmpty())
  <div class="card" style="margin-top:16px">
    <div class="card-header"><h3><i class="fas fa-history" style="color:var(--blue)"></i> Tugas yang Sudah Dikumpulkan</h3></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Judul Tugas</th><th>Mapel</th><th>Nilai</th><th>Catatan Guru</th><th>Dikumpulkan</th></tr></thead>
        <tbody>
          @foreach($pengumpulanSaya as $p)
          <tr>
            <td><strong>{{ $p->tugas->judul }}</strong></td>
            <td>{{ $p->tugas->mapel->kode }}</td>
            <td>
              @if($p->nilai !== null)
                <span style="font-weight:700;color:var(--blue)">{{ $p->nilai }}</span>
              @else
                <span style="color:var(--gray-400);font-size:11px">Belum dinilai</span>
              @endif
            </td>
            <td style="max-width:200px;font-size:12px">
              @if($p->catatan_guru)
                {{ $p->catatan_guru }}
              @else
                <span style="color:var(--gray-400);font-size:11px">—</span>
              @endif
            </td>
            <td>{{ $p->dikumpulkan_at ? $p->dikumpulkan_at->format('d M Y H:i') : '-' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
  <div x-show="showKumpul" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center" x-cloak>
    <div style="background:white;border-radius:12px;padding:24px;width:90%;max-width:500px;box-shadow:0 8px 32px rgba(0,0,0,0.2)">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h3 style="margin:0;font-size:16px"><i class="fas fa-upload" style="color:var(--blue)"></i> Kumpulkan Tugas</h3>
        <button @click="showKumpul = false" style="background:none;border:none;font-size:22px;cursor:pointer;color:var(--gray-400)">&times;</button>
      </div>
      <form method="POST" action="{{ route('siswa.tugas.kumpul') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tugas_id" x-model="kumpulTugasId">
        <div class="form-group" style="margin-bottom:14px">
          <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Upload File (opsional)</label>
          <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
          <p style="font-size:11px;color:var(--gray-400);margin-top:4px">PDF, DOC, JPG, PNG, ZIP. Maks 10MB.</p>
        </div>
        <div class="form-group" style="margin-bottom:16px">
          <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Catatan (opsional)</label>
          <textarea name="catatan_siswa" rows="3" placeholder="Catatan untuk guru..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
        </div>
        <button type="submit" class="btn-login" style="cursor:pointer;border:none;width:100%"><i class="fas fa-upload"></i> Kumpulkan</button>
      </form>
    </div>
  </div>
</div>
