@php
    $gradeColor = function($v) {
        if ($v >= 90) return 'grade-A';
        if ($v >= 85) return 'grade-B';
        return 'grade-C';
    };
    $gradeLetter = function($v) {
        if ($v >= 90) return 'A';
        if ($v >= 85) return 'A-';
        if ($v >= 80) return 'B+';
        if ($v >= 75) return 'B';
        if ($v >= 70) return 'B-';
        return 'C';
    };
    $getStatus = function($v, $kkm = 70) {
        if ($v >= 90) return ['label' => 'Bagus Banget', 'class' => 'status-bagus-banget'];
        if ($v >= 80) return ['label' => 'Bagus', 'class' => 'status-bagus'];
        if ($v >= $kkm) return ['label' => 'Perlu Belajar Lagi', 'class' => 'status-perlu-belajar'];
        return ['label' => 'Perlu Diulang', 'class' => 'status-perlu-diulang'];
    };
    $barColors = ['blue', 'red', 'orange', 'orange', 'purple', 'pink', 'cyan'];
    $rata = $nilai->avg('nilai');
@endphp
<style>
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-block;
    }
    .status-bagus-banget {
        background-color: #e6fffa;
        color: #047857;
        border: 1px solid #b2f5ea;
    }
    .status-bagus {
        background-color: #ebf8ff;
        color: #0284c7;
        border: 1px solid #bee3f8;
    }
    .status-perlu-belajar {
        background-color: #fffaf0;
        color: #dd6b20;
        border: 1px solid #feebc8;
    }
    .status-perlu-diulang {
        background-color: #fff5f5;
        color: #e53e3e;
        border: 1px solid #fed7d7;
    }
</style>
<div class="sd-content sd-content-nilai" x-data="{ showBandingModal: false, activeNilaiId: null, alasan: '' }">
  <div class="content-header"><h1>Nilai <span>SDIT {{ setting('school_name') }}</span></h1><div class="header-right"><div class="avatar red">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div></div></div>
  <div class="grid-2">
    <div class="card"><div class="card-header"><h3><i class="fas fa-file-invoice" style="color:var(--orange)"></i> Rapor Semester Genap</h3><label @click="tab='rapor'" style="font-size:12px;color:var(--orange);font-weight:600;cursor:pointer;text-decoration:none">Cetak</label></div>
      @foreach($nilai as $n)
      @php $status = $getStatus($n->nilai, setting('kkm_sd')); @endphp
      <div class="rapor-item" style="flex-wrap: wrap; gap: 8px;">
          <div style="display:flex; flex-direction:column;">
              <span class="rapor-mapel">{{ $n->mapel->nama_mapel }}</span>
              @if($n->nilai_bahasa)
              <span style="font-size:11px; color:#4f46e5; font-weight:600">Komponen B. Inggris: {{ $n->nilai_bahasa }}</span>
              @endif
          </div>
          <div style="display:flex; align-items:center; gap:8px">
              <span class="rapor-nilai {{ $gradeColor($n->nilai) }}">{{ $n->nilai }} ({{ $gradeLetter($n->nilai) }})</span>
              <span class="status-badge {{ $status['class'] }}">{{ $status['label'] }}</span>

              @if($n->nilai_bahasa)
                  @if($n->banding)
                      @if($n->banding->status === 'pending')
                          <span class="status-badge" style="background-color: #fffbeb; color: #d97706; border: 1px solid #fef3c7;" title="Alasan: {{ $n->banding->alasan_siswa }}">⏳ Banding</span>
                      @elseif($n->banding->status === 'disetujui')
                          <span class="status-badge" style="background-color: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7;" title="Catatan Guru: {{ $n->banding->catatan_guru ?? 'Disetujui' }}">✅ Disetujui</span>
                      @elseif($n->banding->status === 'ditolak')
                          <span class="status-badge" style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fee2e2;" title="Catatan Guru: {{ $n->banding->catatan_guru ?? 'Ditolak' }}">❌ Ditolak</span>
                      @endif
                  @else
                      <button type="button" @click="activeNilaiId = {{ $n->id }}; alasan = ''; showBandingModal = true" style="background:#4f46e5; color:white; border:none; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px">
                          ⚖️ Banding
                      </button>
                  @endif
              @endif
          </div>
      </div>
      @endforeach
    </div>
        @php
            // Mengambil data nilai per tugas/bab riil dari database (tabel nilai & pengumpulan_tugas)
            $chartDataReal = [];
            foreach ($mapels as $m) {
                $chartDataReal[$m->id] = [];
            }

            // 1. Cek tabel nilai yang terkait dengan tugas (jika ada)
            $tugasNilai = \App\Models\Nilai::with(['tugas', 'mapel'])
                ->where('siswa_id', $siswa->id)
                ->whereNotNull('tugas_id')
                ->get();

            foreach ($tugasNilai as $tn) {
                if ($tn->tugas && $tn->mapel_id) {
                    $label =
                        strlen($tn->tugas->judul) > 15 ? substr($tn->tugas->judul, 0, 15) . '...' : $tn->tugas->judul;
                    $chartDataReal[$tn->mapel_id][] = [
                        'label' => $label,
                        'value' => $tn->nilai,
                        'color' => 'orange',
                    ];
                }
            }

            // 2. Cek tabel pengumpulan_tugas yang sudah dinilai guru
            $pengumpulan = \App\Models\PengumpulanTugas::with('tugas.mapel')
                ->where('siswa_id', $siswa->id)
                ->whereNotNull('nilai')
                ->get();

            foreach ($pengumpulan as $pg) {
                if ($pg->tugas && $pg->tugas->mapel_id) {
                    $mId = $pg->tugas->mapel_id;
                    $label =
                        strlen($pg->tugas->judul) > 15 ? substr($pg->tugas->judul, 0, 15) . '...' : $pg->tugas->judul;
                    $color = $pg->tugas->tipe === 'ulangan' ? 'orange' : 'blue';

                    $exists = collect($chartDataReal[$mId])->contains('label', $label);
                    if (!$exists) {
                        $chartDataReal[$mId][] = [
                            'label' => $label,
                            'value' => $pg->nilai,
                            'color' => $color,
                        ];
                    }
                }
            }
        @endphp
        <div class="card" x-data="{
            selectedMapel: '{{ $mapels->first()->id ?? 1 }}',
            chartData: {{ json_encode($chartDataReal) }}
        }">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <h3 style="margin: 0;"><i class="fas fa-chart-bar" style="color:var(--blue)"></i> Statistik Per Bab</h3>
                <select x-model="selectedMapel"
                    style="padding: 6px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--gray-50); font-weight: 600; color: var(--gray-700); cursor: pointer; outline: none; flex: 1; max-width: 200px;">
                    @foreach ($mapels as $m)
                        <option value='{{ $m->id }}'>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="h-bar-group" style="margin-top: 16px;">
                <template x-if="chartData[selectedMapel] && chartData[selectedMapel].length > 0">
                    <div>
                        <template x-for="item in chartData[selectedMapel]" :key="item.label">
                            <div class="h-bar-row">
                                <div class="h-bar-label" x-text="item.label" style="width: 80px; font-size:11px;"></div>
                                <div class="h-bar-track">
                                    <div class="h-bar-fill" :class="item.color" :style="'width: ' + item.value + '%'"
                                        x-text="item.value"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!chartData[selectedMapel] || chartData[selectedMapel].length === 0">
                    <div style="text-align:center; padding: 20px; color:var(--gray-400); font-size:13px;">
                        <i class="fas fa-folder-open" style="font-size:24px; margin-bottom:10px; opacity:0.5;"></i>
                        <p style="margin:0">Belum ada data nilai tugas/ulangan yang dinilai untuk mapel ini.</p>
                    </div>
                </template>
            </div>
        </div>
  </div>

  <!-- Modal Ajukan Banding Nilai -->
  <div x-show="showBandingModal" class="modal-backdrop" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:9999; backdrop-filter:blur(4px)" x-cloak>
      <div @click.away="showBandingModal = false" style="background:white; border-radius:12px; width:100%; max-width:500px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.1); position:relative">
          <h3 style="margin-top:0; color:#1e293b; display:flex; align-items:center; gap:8px"><i class="fas fa-balance-scale" style="color:#4f46e5"></i> Ajukan Banding Nilai</h3>
          <p style="font-size:13px; color:#64748b; margin-bottom:16px">Tuliskan alasan rasional mengapa nilai pemahaman materi Anda harus ditinjau kembali oleh Guru mata pelajaran ini.</p>
          
          <form action="{{ route('siswa.banding.store') }}" method="POST">
              @csrf
              <input type="hidden" name="nilai_id" :value="activeNilaiId">
              <textarea name="alasan_siswa" x-model="alasan" placeholder="Contoh: Saya sudah mengerjakan dengan rumus yang benar, mohon review kembali porsi nilai bahasa..." required minlength="5" style="width:100%; height:120px; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; font-family:inherit; resize:none; outline:none; transition:border-color 0.2s" onfocus="this.style.borderColor='#4f46e5'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
              
              <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px">
                  <button type="button" @click="showBandingModal = false" style="background:#f1f3f5; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-weight:600; color:#475569">Batal</button>
                  <button type="submit" :disabled="alasan.length < 5" style="background:#4f46e5; border:none; padding:10px 16px; border-radius:8px; cursor:pointer; font-weight:600; color:white; display:inline-flex; align-items:center; gap:6px; transition:opacity 0.2s" :style="alasan.length < 5 ? 'opacity:0.5; cursor:not-allowed;' : ''">Kirim Banding</button>
              </div>
          </form>
      </div>
  </div>
</div>
