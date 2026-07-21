<div x-data="{ activeSiswaId: null, showGradeModal: false, selectedSiswaKti: null, ktiJudul: '', nilaiProses: '', nilaiTulisan: '', nilaiSidang: '', catatan: '' }">
    <div class="content-header">
        <h1>Karya Tulis Ilmiah (KTI) <span>Siswa Kelas 9</span></h1>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
        </div>
    </div>
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h3><i class="fas fa-clipboard-check" style="color:var(--teal)"></i> Menunggu Persetujuan (Review Draf)</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Bab KTI</th>
                        <th>File Draf</th>
                        <th>Catatan Siswa</th>
                        <th>Aksi Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ktiBimbinganReviews as $r)
                        <tr>
                            <td><strong>{{ $r->siswa->nama }}</strong></td>
                            <td>{{ $r->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td><span class="badge light blue">{{ $r->bab }}</span></td>
                            <td>
                                @if(filter_var($r->file_draft, FILTER_VALIDATE_URL))
                                    <a href="{{ $r->file_draft }}" target="_blank" style="color:var(--blue); font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px"><i class="fas fa-external-link-alt"></i> Buka Link</a>
                                @else
                                    <a href="{{ $r->file_draft }}" download style="color:var(--blue); font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px"><i class="fas fa-file-download"></i> Unduh File</a>
                                @endif
                            </td>
                            <td style="max-width:180px; font-size:13px; color:var(--gray-500); font-style:italic">"{{ $r->catatan_siswa ?? '-' }}"</td>
                            <td>
                                <form action="{{ route('guru.kti.proses-bimbingan', $r->id) }}" method="POST" style="display:flex; flex-direction:column; gap:6px; max-width:220px">
                                    @csrf
                                    <input type="text" name="catatan_guru" placeholder="Catatan/Umpan balik..." required style="padding:6px; border:1px solid var(--border); border-radius:4px; font-size:12px">
                                    <div style="display:flex; gap:6px">
                                        <button type="submit" name="status" value="disetujui" class="btn-small" style="background:var(--green); color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer; font-weight:700; font-size:11px">
                                            <i class="fas fa-check"></i> ACC / Setujui
                                        </button>
                                        <button type="submit" name="status" value="revisi" class="btn-small" style="background:var(--red); color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer; font-weight:700; font-size:11px">
                                            <i class="fas fa-undo"></i> Minta Revisi
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:var(--gray-400); padding:20px">Tidak ada draf bimbingan yang perlu di-review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users" style="color:var(--blue)"></i> Rekapitulasi Karya Tulis Ilmiah Kelas 9</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Judul KTI</th>
                        <th>Bab Aktif</th>
                        <th>Jadwal Sidang</th>
                        <th>Nilai Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaKelas9 as $s)
                        @php
                            $ktiInfo = $nilaiKtiRekap->where('siswa_id', $s->id)->first();
                            $currentKtiBab = $ktiInfo->current_bab ?? 'Bab 1';
                        @endphp
                        <tr>
                            <td><strong>{{ $s->nama }}</strong></td>
                            <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                            <td style="max-width:200px; font-weight:600">{{ $ktiInfo->judul_kti ?? 'Belum Ditentukan' }}</td>
                            <td>
                                @if($currentKtiBab === 'Selesai')
                                    <span class="badge light green"><i class="fas fa-check-circle"></i> Selesai</span>
                                @elseif($currentKtiBab === 'Siap Sidang')
                                    <span class="badge light blue"><i class="fas fa-graduation-cap"></i> Siap Sidang</span>
                                @else
                                    <span class="badge light orange">{{ $currentKtiBab }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ktiInfo && $ktiInfo->jadwal_sidang)
                                    <div style="font-size:12px; font-weight:700">{{ \Carbon\Carbon::parse($ktiInfo->jadwal_sidang)->format('d M Y - HH:mm') }}</div>
                                @else
                                    <span style="color:var(--gray-400); font-size:11px">Belum Dijadwalkan</span>
                                @endif
                                @if($ktiInfo && $currentKtiBab !== 'Selesai')
                                    <form action="{{ route('guru.kti.jadwal', $ktiInfo->id) }}" method="POST" style="margin-top:6px; display:flex; gap:4px">
                                        @csrf
                                        <input type="datetime-local" name="jadwal_sidang" required style="padding:4px; font-size:10px; border:1px solid var(--border); border-radius:4px">
                                        <button type="submit" class="btn-small outline" style="padding:4px 6px; font-size:10px; cursor:pointer; background:none; border:1px solid var(--border)"><i class="fas fa-calendar-plus"></i></button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                @if($currentKtiBab === 'Selesai')
                                    <strong style="color:var(--teal); font-size:15px">{{ $ktiInfo->nilai_akhir }}</strong>
                                @else
                                    <span style="color:var(--gray-400); font-size:12px">-</span>
                                @endif
                            </td>
                            <td>
                                @if($currentKtiBab !== 'Selesai')
                                    <button type="button" @click="
                                        selectedSiswaKti = {{ $s->id }};
                                        ktiJudul = '{{ $ktiInfo->judul_kti ?? '' }}';
                                        nilaiProses = '{{ $ktiInfo->nilai_proses ?? '' }}';
                                        nilaiTulisan = '{{ $ktiInfo->nilai_tulisan ?? '' }}';
                                        nilaiSidang = '{{ $ktiInfo->nilai_sidang ?? '' }}';
                                        catatan = '{{ $ktiInfo->catatan ?? '' }}';
                                        showGradeModal = true;
                                    " class="btn-small" style="background:#4f46e5; color:white; border:none; cursor:pointer; font-weight:700; border-radius:4px; padding:6px 12px; display:inline-flex; align-items:center; gap:4px">
                                        <i class="fas fa-star"></i> Input Nilai
                                    </button>
                                @else
                                    <button type="button" @click="
                                        selectedSiswaKti = {{ $s->id }};
                                        ktiJudul = '{{ $ktiInfo->judul_kti ?? '' }}';
                                        nilaiProses = '{{ $ktiInfo->nilai_proses ?? '' }}';
                                        nilaiTulisan = '{{ $ktiInfo->nilai_tulisan ?? '' }}';
                                        nilaiSidang = '{{ $ktiInfo->nilai_sidang ?? '' }}';
                                        catatan = '{{ $ktiInfo->catatan ?? '' }}';
                                        showGradeModal = true;
                                    " class="btn-small outline" style="border:1px solid #4f46e5; color:#4f46e5; cursor:pointer; font-weight:700; border-radius:4px; padding:6px 12px; display:inline-flex; align-items:center; gap:4px; background:none">
                                        <i class="fas fa-edit"></i> Edit Nilai
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:var(--gray-400); padding:20px">Belum ada data siswa kelas 9.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div x-show="showGradeModal" class="modal-backdrop" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:9999; backdrop-filter:blur(4px)" x-cloak>
        <div @click.away="showGradeModal = false" style="background:white; border-radius:12px; width:100%; max-width:500px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.1); position:relative">
            <h3 style="margin-top:0; color:#1e293b; display:flex; align-items:center; gap:8px"><i class="fas fa-star" style="color:#ff922b"></i> Penilaian Ujian Sidang KTI</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px">Isi rincian nilai KTI siswa di bawah ini. Nilai Akhir akan dihitung otomatis oleh sistem.</p>
            <form action="/guru/kti" method="POST">
                @csrf
                <input type="hidden" name="siswa_id" :value="selectedSiswaKti">
                <div class="form-group" style="margin-bottom:12px">
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px">Judul Karya Tulis Ilmiah</label>
                    <input type="text" name="judul_kti" x-model="ktiJudul" required placeholder="Masukkan judul KTI siswa..." style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px">
                </div>
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-bottom:12px">
                    <div class="form-group">
                        <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px">Bimbingan (30%)</label>
                        <input type="number" name="nilai_proses" x-model="nilaiProses" required min="0" max="100" placeholder="0-100" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px">
                    </div>
                    <div class="form-group">
                        <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px">Tulisan (40%)</label>
                        <input type="number" name="nilai_tulisan" x-model="nilaiTulisan" required min="0" max="100" placeholder="0-100" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px">
                    </div>
                    <div class="form-group">
                        <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px">Sidang (30%)</label>
                        <input type="number" name="nilai_sidang" x-model="nilaiSidang" required min="0" max="100" placeholder="0-100" style="width:100%; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:16px">
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:4px">Catatan / Umpan Balik Guru</label>
                    <textarea name="catatan" x-model="catatan" placeholder="Tuliskan umpan balik atau apresiasi atas karya tulis..." style="width:100%; height:80px; padding:8px 10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; font-family:inherit; resize:none"></textarea>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px">
                    <button type="button" @click="showGradeModal = false" style="background:#f1f3f5; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-weight:600; color:#475569">Batal</button>
                    <button type="submit" style="background:#ff922b; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-weight:600; color:white"><i class="fas fa-save"></i> Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>
</div>
