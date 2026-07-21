<style>
    .btn-rating {
        background: var(--white);
        color: var(--gray-600);
        border: 1px solid var(--border) !important;
        transition: all 0.2s;
        font-weight: 600;
    }
    .btn-rating.active {
        color: var(--white) !important;
    }
    .btn-rating.active:nth-of-type(1) { background: var(--red) !important; border-color: var(--red) !important; }
    .btn-rating.active:nth-of-type(2) { background: var(--orange) !important; border-color: var(--orange) !important; }
    .btn-rating.active:nth-of-type(3) { background: var(--blue) !important; border-color: var(--blue) !important; }
    .btn-rating.active:nth-of-type(4) { background: var(--green) !important; border-color: var(--green) !important; }
</style>

@php
    $statusBadge = fn($s) => $s === 'baru'
        ? '<span class="badge green light">Baru</span>'
        : '<span class="badge blue">Murojaah</span>';
    $siswaList = \App\Models\Siswa::whereNotNull('kelas_quran_id')->with('kelasQuran')->get();
    $allSetoran = \App\Models\TahfidzSetoran::where('guru_id', $guru->id)
        ->with('siswa', 'siswa.kelas')
        ->orderBy('tanggal', 'desc')
        ->get();
@endphp
<div class="content-header">
    <h1>Setoran Tahfidz</h1>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:20px">
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-plus" style="color:var(--green)"></i> Input Setoran Baru</h3></div>
        <form method="POST" action="/guru/tahfidz" style="padding:4px 0" x-data="{ 
            ayatMulai: 1, 
            ayatSelesai: 1, 
            get ayatList() {
                let list = [];
                let start = parseInt(this.ayatMulai);
                let end = parseInt(this.ayatSelesai);
                if (!isNaN(start) && !isNaN(end) && end >= start) {
                    for (let i = start; i <= end; i++) {
                        list.push(i);
                    }
                }
                return list;
            }
        }">
            @csrf
            <div class="form-group" style="margin-bottom:14px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Siswa</label>
                <select name="siswa_id" required class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                    <option value="">Pilih Siswa</option>
                    @foreach($siswaList as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kelasQuran->nama_kelas ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Surah</label>
                <input type="text" name="surah" required placeholder="Contoh: An-Naba'" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
            </div>
            <div style="display:flex;gap:10px;margin-bottom:14px">
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Ayat Mulai</label>
                    <input type="number" name="ayat_mulai" required min="1" x-model.number="ayatMulai" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
                </div>
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Ayat Selesai</label>
                    <input type="number" name="ayat_selesai" required min="1" x-model.number="ayatSelesai" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:14px">
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Jumlah Ayat</label>
                    <input type="number" name="jumlah_ayat" required min="1" :value="isNaN(ayatSelesai - ayatMulai + 1) || (ayatSelesai - ayatMulai + 1) < 1 ? 1 : (ayatSelesai - ayatMulai + 1)" readonly style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--gray-100);color:var(--gray-500)">
                </div>
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Nilai (Otomatis)</label>
                    <input type="text" placeholder="Dihitung otomatis" disabled style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--gray-100);color:var(--gray-500)">
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:14px">
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Status</label>
                    <select name="status" class="form-select" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);background:var(--white)">
                        <option value="baru">Setoran Baru</option>
                        <option value="murojaah">Murojaah</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ now()->format('Y-m-d') }}" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
                </div>
                <div class="form-group" style="flex:1">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Jadwal Berikutnya (Opsional)</label>
                    <input type="date" name="tanggal_berikutnya" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font)">
                </div>
            </div>

            <!-- Detail Per Ayat Nilai -->
            <div style="border-top: 1px solid var(--border-light); margin-top: 16px; padding-top: 16px;" x-show="ayatList.length > 0">
                <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 12px; color: var(--gray-700)">Aspek Penilaian per Ayat</h4>
                <div style="display:flex; flex-direction:column; gap:12px">
                    <template x-for="(ayat, idx) in ayatList" :key="ayat">
                        <div style="background:var(--gray-50); border:1px solid var(--border-light); padding:10px; border-radius:var(--radius-sm)">
                            <div style="font-weight:700; font-size:13px; color:var(--teal); margin-bottom:8px">Ayat <span x-text="ayat"></span></div>
                            <input type="hidden" :name="'ayat_nilai['+idx+'][nomor_ayat]'" :value="ayat">
                            
                            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px">
                                <!-- Makhroj -->
                                <div>
                                    <label style="display:block; font-size:11px; font-weight:600; color:var(--gray-500); margin-bottom:4px">Makhroj</label>
                                    <div style="display:flex; gap:4px" x-data="{ val: 3 }">
                                        <input type="hidden" :name="'ayat_nilai['+idx+'][makhroj]'" :value="val">
                                        <template x-for="i in [1, 2, 3, 4]" :key="i">
                                            <button type="button" @click="val = i" :class="val == i ? 'btn-rating active' : 'btn-rating'" style="flex:1; border: 1px solid var(--border); border-radius:4px; padding:4px; font-size:11px; cursor:pointer" x-text="i"></button>
                                        </template>
                                    </div>
                                </div>
                                <!-- Tajwid -->
                                <div>
                                    <label style="display:block; font-size:11px; font-weight:600; color:var(--gray-500); margin-bottom:4px">Tajwid</label>
                                    <div style="display:flex; gap:4px" x-data="{ val: 3 }">
                                        <input type="hidden" :name="'ayat_nilai['+idx+'][tajwid]'" :value="val">
                                        <template x-for="i in [1, 2, 3, 4]" :key="i">
                                            <button type="button" @click="val = i" :class="val == i ? 'btn-rating active' : 'btn-rating'" style="flex:1; border: 1px solid var(--border); border-radius:4px; padding:4px; font-size:11px; cursor:pointer" x-text="i"></button>
                                        </template>
                                    </div>
                                </div>
                                <!-- Kelancaran -->
                                <div>
                                    <label style="display:block; font-size:11px; font-weight:600; color:var(--gray-500); margin-bottom:4px">Kelancaran</label>
                                    <div style="display:flex; gap:4px" x-data="{ val: 3 }">
                                        <input type="hidden" :name="'ayat_nilai['+idx+'][kelancaran]'" :value="val">
                                        <template x-for="i in [1, 2, 3, 4]" :key="i">
                                            <button type="button" @click="val = i" :class="val == i ? 'btn-rating active' : 'btn-rating'" style="flex:1; border: 1px solid var(--border); border-radius:4px; padding:4px; font-size:11px; cursor:pointer" x-text="i"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:16px; margin-top: 16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Catatan (opsional)</label>
                <textarea name="catatan_guru" rows="2" placeholder="Catatan untuk setoran ini..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:var(--font);resize:vertical"></textarea>
            </div>
            <button type="submit" class="btn-login" style="cursor:pointer;border:none"><i class="fas fa-save"></i> Simpan Setoran</button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Statistik Setoran Saya</h3></div>
        <div style="padding:4px 0">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Total Setoran</span><div style="font-size:22px;font-weight:700;color:var(--teal)">{{ $allSetoran->count() }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Total Ayat</span><div style="font-size:22px;font-weight:700;color:var(--blue)">{{ $allSetoran->sum('jumlah_ayat') }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Rata-rata Nilai</span><div style="font-size:22px;font-weight:700;color:var(--orange)">{{ $allSetoran->avg('nilai') ? number_format($allSetoran->avg('nilai'), 0) : '-' }}</div></div>
                <div><span style="font-size:12px;color:var(--gray-400)">Siswa Dibimbing</span><div style="font-size:22px;font-weight:700;color:var(--purple)">{{ $allSetoran->pluck('siswa_id')->unique()->count() }}</div></div>
            </div>
        </div>
    </div>
</div>

<div x-data="{ subtab: 'riwayat' }">
    <style>
        .tab-btn {
            border-bottom: 2px solid transparent !important;
            transition: all 0.2s;
            background: none;
            border: none;
            font-family: var(--font);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            padding: 8px 16px;
            color: var(--gray-400);
        }
        .tab-btn.active {
            color: var(--teal) !important;
            border-bottom: 2px solid var(--teal) !important;
        }
    </style>
    
    <div style="display:flex; gap:10px; margin-bottom:14px; border-bottom:1px solid var(--border-light); padding-bottom:4px">
        <button type="button" @click="subtab = 'riwayat'" :class="subtab === 'riwayat' ? 'active' : ''" class="tab-btn">
            <i class="fas fa-history"></i> Riwayat Setoran Saya
        </button>
        <button type="button" @click="subtab = 'pembanding'" :class="subtab === 'pembanding' ? 'active' : ''" class="tab-btn">
            <i class="fas fa-balance-scale"></i> Butuh Penilai Kedua
            @php
                $needGradingCount = \App\Models\TahfidzSetoran::where('guru_id', '!=', $guru->id)
                    ->whereDoesntHave('ayatNilai', fn($q) => $q->where('guru_id', $guru->id))
                    ->count();
            @endphp
            @if($needGradingCount > 0)
                <span class="badge red light" style="margin-left:4px; font-size:11px; padding: 2px 6px">{{ $needGradingCount }}</span>
            @endif
        </button>
    </div>
    
    <!-- Tab 1: Riwayat Setoran Saya -->
    <div x-show="subtab === 'riwayat'">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-list" style="color:var(--blue)"></i> Riwayat Setoran Saya</h3></div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Surah</th>
                            <th>Ayat</th>
                            <th>Penilai</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allSetoran as $t)
                        @php
                            $penilaiIds = $t->ayatNilai->pluck('guru_id')->unique();
                            $penilaiGurus = \App\Models\Guru::whereIn('id', $penilaiIds)->get();
                        @endphp
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                                @if($t->tanggal_berikutnya)
                                    <br><span style="font-size:10px;color:var(--teal);font-weight:600;display:inline-block;margin-top:2px"><i class="fas fa-calendar-alt"></i> Next: {{ \Carbon\Carbon::parse($t->tanggal_berikutnya)->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td><strong>{{ $t->siswa->nama ?? '-' }}</strong></td>
                            <td>{{ $t->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $t->surah }}</td>
                            <td>{{ $t->ayat_mulai }}:{{ $t->ayat_selesai }} ({{ $t->jumlah_ayat }} ayat)</td>
                            <td>
                                <span style="font-weight:600">{{ $t->guru->nama ?? '-' }}</span>
                                @foreach($penilaiGurus as $pg)
                                    @if($pg->id !== $t->guru_id)
                                        <br><span style="font-size:11px; color:var(--blue); font-weight:500">+ {{ $pg->nama }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @if($penilaiGurus->count() < 2 && $t->ayatNilai->count() > 0)
                                    <span class="badge light orange">Menunggu Guru 2</span>
                                @else
                                    {!! $statusBadge($t->status) !!}
                                @endif
                            </td>
                            <td style="font-weight:700">{{ $t->nilai ?? '-' }}</td>
                            <td style="color:var(--gray-400);font-size:13px">{{ $t->catatan_guru ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada setoran tahfidz</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Tab 2: Butuh Penilai Kedua -->
    <div x-show="subtab === 'pembanding'">
        @php
            $setoranPembanding = \App\Models\TahfidzSetoran::where('guru_id', '!=', $guru->id)
                ->whereDoesntHave('ayatNilai', fn($q) => $q->where('guru_id', $guru->id))
                ->with('siswa', 'siswa.kelas', 'guru')
                ->orderBy('tanggal', 'desc')
                ->get();
        @endphp
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-balance-scale" style="color:var(--orange)"></i> Setoran Menunggu Penilai Kedua</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Surah</th>
                            <th>Ayat</th>
                            <th>Guru Pertama</th>
                            <th>Nilai Sementara</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($setoranPembanding as $sp)
                        <tr x-data="{ showForm: false }">
                            <td>{{ \Carbon\Carbon::parse($sp->tanggal)->format('d M Y') }}</td>
                            <td><strong>{{ $sp->siswa->nama ?? '-' }}</strong></td>
                            <td>{{ $sp->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $sp->surah }}</td>
                            <td>{{ $sp->ayat_mulai }}:{{ $sp->ayat_selesai }} ({{ $sp->jumlah_ayat }} ayat)</td>
                            <td>{{ $sp->guru->nama ?? '-' }}</td>
                            <td><span class="badge light blue">{{ $sp->nilai ?? '-' }}</span></td>
                            <td>
                                <button type="button" @click="showForm = true" class="btn-small outline" style="cursor:pointer; display:inline-flex; align-items:center; gap:4px">
                                    <i class="fas fa-edit"></i> Nilai
                                </button>
                                
                                <!-- Modal Overlay Penilaian Kedua -->
                                <div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:9999; padding:20px" x-show="showForm" x-transition x-cloak>
                                    <div class="card" style="width:100%; max-width:600px; max-height:90vh; overflow-y:auto; background:var(--white); box-shadow:0 10px 25px rgba(0,0,0,0.2); padding:20px; border-radius:var(--radius); text-align:left">
                                        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-light); padding-bottom:10px; margin-bottom:15px">
                                            <h3 style="margin:0"><i class="fas fa-balance-scale" style="color:var(--orange)"></i> Nilai Pembanding Tahfidz</h3>
                                            <button type="button" @click="showForm = false" style="background:none; border:none; font-size:18px; cursor:pointer"><i class="fas fa-times"></i></button>
                                        </div>
                                        <p style="font-size:13px; color:var(--gray-500); margin-bottom:15px">
                                            Menilai setoran <strong>{{ $sp->siswa->nama }}</strong> untuk <strong>{{ $sp->surah }} Ayat {{ $sp->ayat_mulai }}-{{ $sp->ayat_selesai }}</strong>.
                                        </p>
                                        
                                        <form method="POST" action="/guru/tahfidz/{{ $sp->id }}/nilai-pembanding">
                                            @csrf
                                            <div style="display:flex; flex-direction:column; gap:12px">
                                                @for($ayat = $sp->ayat_mulai; $ayat <= $sp->ayat_selesai; $ayat++)
                                                    @php $idx = $ayat - $sp->ayat_mulai; @endphp
                                                    <div style="background:var(--gray-50); border:1px solid var(--border-light); padding:10px; border-radius:var(--radius-sm)">
                                                        <div style="font-weight:700; font-size:13px; color:var(--orange); margin-bottom:8px">Ayat {{ $ayat }}</div>
                                                        <input type="hidden" name="ayat_nilai[{{ $idx }}][nomor_ayat]" value="{{ $ayat }}">
                                                        
                                                        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px">
                                                            <!-- Makhroj -->
                                                            <div>
                                                                <label style="display:block; font-size:11px; font-weight:600; color:var(--gray-500); margin-bottom:4px">Makhroj</label>
                                                                <div style="display:flex; gap:4px" x-data="{ val: 3 }">
                                                                    <input type="hidden" name="ayat_nilai[{{ $idx }}][makhroj]" :value="val">
                                                                    <template x-for="n in [1, 2, 3, 4]" :key="n">
                                                                        <button type="button" @click="val = n" :class="val == n ? 'btn-rating active' : 'btn-rating'" style="flex:1; border: 1px solid var(--border); border-radius:4px; padding:4px; font-size:11px; cursor:pointer" x-text="n"></button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <!-- Tajwid -->
                                                            <div>
                                                                <label style="display:block; font-size:11px; font-weight:600; color:var(--gray-500); margin-bottom:4px">Tajwid</label>
                                                                <div style="display:flex; gap:4px" x-data="{ val: 3 }">
                                                                    <input type="hidden" name="ayat_nilai[{{ $idx }}][tajwid]" :value="val">
                                                                    <template x-for="n in [1, 2, 3, 4]" :key="n">
                                                                        <button type="button" @click="val = n" :class="val == n ? 'btn-rating active' : 'btn-rating'" style="flex:1; border: 1px solid var(--border); border-radius:4px; padding:4px; font-size:11px; cursor:pointer" x-text="n"></button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <!-- Kelancaran -->
                                                            <div>
                                                                <label style="display:block; font-size:11px; font-weight:600; color:var(--gray-500); margin-bottom:4px">Kelancaran</label>
                                                                <div style="display:flex; gap:4px" x-data="{ val: 3 }">
                                                                    <input type="hidden" name="ayat_nilai[{{ $idx }}][kelancaran]" :value="val">
                                                                    <template x-for="n in [1, 2, 3, 4]" :key="n">
                                                                        <button type="button" @click="val = n" :class="val == n ? 'btn-rating active' : 'btn-rating'" style="flex:1; border: 1px solid var(--border); border-radius:4px; padding:4px; font-size:11px; cursor:pointer" x-text="n"></button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                            <div style="display:flex; gap:10px; margin-top:20px; justify-content:flex-end">
                                                <button type="button" @click="showForm = false" class="btn-small outline" style="cursor:pointer">Batal</button>
                                                <button type="submit" class="btn-small" style="background:var(--orange); color:var(--white); border:none; border-radius:var(--radius-sm); padding:6px 14px; font-weight:600; cursor:pointer"><i class="fas fa-save"></i> Simpan Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;color:var(--gray-400);padding:20px">Tidak ada setoran yang menunggu nilai kedua.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
