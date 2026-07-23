@php
    $categories = \App\Support\SmpLearningDocumentInventory::categoryLabels();
    $statusMeta = [
        'pending' => ['label' => 'Menunggu Review', 'class' => 'orange'],
        'approved' => ['label' => 'Disetujui', 'class' => 'green'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'red'],
        'revision_requested' => ['label' => 'Perlu Revisi', 'class' => 'orange'],
        'draft' => ['label' => 'Draft', 'class' => 'gray'],
    ];
@endphp

<div x-data="{ guru: '', kelas: '', mapel: '', kategori: '', tahun: '', status: '' }">
    <div class="content-header">
        <div>
            <h1>Approval Bahan Ajar</h1>
            <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Review pengajuan Kompetensi Inti, Kompetensi Dasar, dan Alur Tahapan Pembelajaran</p>
        </div>
        <div class="header-right">
            <span class="badge light orange">{{ ($materiApprovalList ?? collect())->where('status', 'pending')->count() }} pending</span>
            <div class="avatar teal">KS</div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px">
        <div class="card-header"><h3><i class="fas fa-filter" style="color:var(--blue)"></i> Filter</h3></div>
        <div class="grid-3" style="gap:10px">
            <select x-model="guru" class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                <option value="">Semua Guru</option>
                @foreach($materiApprovalGurus ?? [] as $g)
                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                @endforeach
            </select>
            <select x-model="kelas" class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                <option value="">Semua Kelas</option>
                @foreach($materiApprovalKelas ?? [] as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
            <select x-model="mapel" class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                <option value="">Semua Mapel</option>
                @foreach($materiApprovalMapels ?? [] as $m)
                    <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                @endforeach
            </select>
            <select x-model="kategori" class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <input x-model="tahun" type="text" placeholder="Tahun ajaran" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm)">
            <select x-model="status" class="form-select" style="padding:8px;border:1px solid var(--border);border-radius:var(--radius-sm);background:white">
                <option value="">Semua Status</option>
                @foreach($statusMeta as $key => $meta)
                    <option value="{{ $key }}">{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3><i class="fas fa-clipboard-check" style="color:var(--teal)"></i> Daftar Pengajuan</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Guru</th>
                        <th>Kategori</th>
                        <th>Kelas / Mapel</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th style="width:260px">Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materiApprovalList ?? [] as $m)
                        @php $meta = $statusMeta[$m->status] ?? ['label' => ucfirst($m->status), 'class' => 'gray']; @endphp
                        <tr
                            data-guru="{{ $m->guru_id }}"
                            data-kelas="{{ $m->kelas_id }}"
                            data-mapel="{{ $m->mapel_id }}"
                            data-kategori="{{ $m->kategori }}"
                            data-tahun="{{ $m->tahun_ajaran }}"
                            data-status="{{ $m->status }}"
                            x-show="(!guru || $el.dataset.guru === guru) && (!kelas || $el.dataset.kelas === kelas) && (!mapel || $el.dataset.mapel === mapel) && (!kategori || $el.dataset.kategori === kategori) && (!tahun || $el.dataset.tahun.includes(tahun)) && (!status || $el.dataset.status === status)"
                        >
                            <td>
                                <strong>{{ $m->kode ? $m->kode.' - ' : '' }}{{ $m->judul }}</strong>
                                <div style="font-size:12px;color:var(--gray-400);margin-top:4px">{{ \Illuminate\Support\Str::limit($m->deskripsi, 90) }}</div>
                            </td>
                            <td>{{ $m->guru->nama ?? '-' }}</td>
                            <td>{{ $categories[$m->kategori] ?? $m->kategori }}</td>
                            <td>{{ $m->kelas->nama_kelas ?? '-' }}<br><small style="color:var(--gray-400)">{{ $m->mapel->nama_mapel ?? '-' }}</small></td>
                            <td>{{ $m->tahun_ajaran }}<br><small style="color:var(--gray-400)">{{ $m->semester }}</small></td>
                            <td>
                                <span class="badge light {{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                @if($m->reviewer)
                                    <div style="font-size:11px;color:var(--gray-400);margin-top:4px">Reviewer: {{ $m->reviewer->name }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px">
                                    @if($m->file_path)
                                        <a href="{{ route('materi.download', $m) }}" class="btn-small outline" style="text-decoration:none"><i class="fas fa-download"></i> File</a>
                                    @endif
                                </div>
                                @if($m->status === 'pending')
                                    <form method="POST" action="{{ route('kepala.materi.approve', $m) }}" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px">
                                        @csrf
                                        <input type="text" name="catatan_reviewer" placeholder="Catatan opsional" style="flex:1;min-width:120px;padding:6px;border:1px solid var(--border);border-radius:4px">
                                        <button class="btn-small teal" style="border:none;cursor:pointer"><i class="fas fa-check"></i> Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('kepala.materi.request-revision', $m) }}" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px">
                                        @csrf
                                        <input type="text" name="catatan_reviewer" required placeholder="Catatan revisi" style="flex:1;min-width:120px;padding:6px;border:1px solid var(--border);border-radius:4px">
                                        <button class="btn-small outline" style="cursor:pointer"><i class="fas fa-rotate-left"></i> Revisi</button>
                                    </form>
                                    <form method="POST" action="{{ route('kepala.materi.reject', $m) }}" style="display:flex;gap:6px;flex-wrap:wrap">
                                        @csrf
                                        <input type="text" name="catatan_reviewer" required placeholder="Alasan penolakan" style="flex:1;min-width:120px;padding:6px;border:1px solid var(--border);border-radius:4px">
                                        <button class="btn-small outline" style="border-color:var(--red);color:var(--red);cursor:pointer"><i class="fas fa-times"></i> Tolak</button>
                                    </form>
                                @else
                                    <div style="font-size:12px;color:var(--gray-500);line-height:1.5">{{ $m->catatan_reviewer ?: 'Tidak ada catatan review.' }}</div>
                                @endif
                                <details style="margin-top:8px;font-size:11px;color:var(--gray-500)">
                                    <summary>Riwayat keputusan</summary>
                                    @forelse($m->approvalHistories as $history)
                                        <div>{{ $history->created_at->format('d M Y H:i') }} - {{ $history->actor->name ?? 'Sistem' }}: {{ $history->action }} ke {{ $history->status_to }}</div>
                                    @empty
                                        <div>Belum ada riwayat.</div>
                                    @endforelse
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada pengajuan Bahan Ajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
