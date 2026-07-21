@php
    $currentBab = $nilaiKti->current_bab ?? 'Bab 1';
    $isPending = $ktiBimbingans->where('bab', $currentBab)->where('status', 'pending')->first();
    $bimbinganStatus = $ktiBimbingans->where('bab', $currentBab)->first()?->status ?? 'none';
    $steps = ['Bab 1', 'Bab 2', 'Bab 3', 'Bab 4', 'Bab 5', 'Draft Akhir', 'Siap Sidang', 'Selesai'];
    $currentStepIndex = array_search($currentBab, $steps);
    if ($currentStepIndex === false) {
        $currentStepIndex = 0;
    }
@endphp
<style>
    .stepper-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 24px 0;
        position: relative;
    }
    .stepper-line {
        position: absolute;
        top: 20px;
        left: 0;
        width: 100%;
        height: 4px;
        background-color: #e2e8f0;
        z-index: 1;
    }
    .stepper-line-fill {
        height: 100%;
        background-color: #20c997;
        transition: width 0.4s ease;
    }
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 2;
        position: relative;
    }
    .step-node {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: white;
        border: 4px solid #cbd5e1;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .step-node.done {
        background-color: #20c997;
        border-color: #20c997;
        color: white;
        box-shadow: 0 0 10px rgba(32, 201, 151, 0.3);
    }
    .step-node.active {
        background-color: #ff922b;
        border-color: #ff922b;
        color: white;
        animation: pulse-node 2s infinite;
        box-shadow: 0 0 12px rgba(255, 146, 43, 0.6);
    }
    .step-label {
        margin-top: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-align: center;
    }
    .step-label.active {
        color: #ff922b;
    }
    .step-label.done {
        color: #20c997;
    }
    @keyframes pulse-node {
        0% { transform: scale(1); }
        50% { transform: scale(1.08); box-shadow: 0 0 16px rgba(255, 146, 43, 0.8); }
        100% { transform: scale(1); }
    }
    .status-alert {
        padding: 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>
<div x-data="{
    fileType: 'link',
    alasan: '',
    initCountdown(targetDate) {
        return {
            target: new Date(targetDate).getTime(),
            days: 0, hours: 0, minutes: 0, finished: false,
            update() {
                const now = new Date().getTime();
                const dist = this.target - now;
                if (dist < 0) {
                    this.finished = true;
                    return;
                }
                this.days = Math.floor(dist / (1000 * 60 * 60 * 24));
                this.hours = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                this.minutes = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
            },
            init() {
                this.update();
                setInterval(() => this.update(), 60000);
            }
        }
    }
}">
    <div class="content-header">
        <h1>Bimbingan Karya Tulis Ilmiah (KTI) <span>Kelas 9 SMP</span></h1>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
        </div>
    </div>
    @if($nilaiKti && $nilaiKti->jadwal_sidang)
        @php
            $targetTime = \Carbon\Carbon::parse($nilaiKti->jadwal_sidang);
            $isPast = $targetTime->isPast();
        @endphp
        @if(!$isPast && $nilaiKti->current_bab !== 'Selesai')
            <div x-data="initCountdown('{{ $nilaiKti->jadwal_sidang }}')" x-init="init()" style="background: linear-gradient(135deg, #4f46e5, #3b82f6); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.25);">
                <div style="font-size: 13px; opacity: 0.9; text-transform: uppercase; font-weight: 700; letter-spacing: 0.8px;"><i class="fas fa-bullhorn" style="margin-right:6px"></i> Pengumuman Ujian Sidang KTI</div>
                <div style="font-size: 20px; font-weight: 800; margin: 8px 0;">{{ $targetTime->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB</div>
                <div style="display:flex; gap:16px; margin-top:12px; align-items:center" x-show="!finished">
                    <span style="font-size:13px; opacity:0.8; font-weight:600">Hitung Mundur Sidang:</span>
                    <div style="display:flex; gap:10px;">
                        <span style="background:rgba(255,255,255,0.2); padding:4px 8px; border-radius:6px; font-weight:800"><span x-text="days">0</span> Hari</span>
                        <span style="background:rgba(255,255,255,0.2); padding:4px 8px; border-radius:6px; font-weight:800"><span x-text="hours">0</span> Jam</span>
                        <span style="background:rgba(255,255,255,0.2); padding:4px 8px; border-radius:6px; font-weight:800"><span x-text="minutes">0</span> Menit</span>
                    </div>
                </div>
                <div x-show="finished" style="font-weight: 700; margin-top: 10px;">Sidang KTI sedang berlangsung atau telah selesai.</div>
            </div>
        @endif
    @endif
    <div class="card" style="margin-bottom:20px; padding: 24px;">
        <div class="card-header" style="border-bottom:none; padding-bottom:0"><h3><i class="fas fa-project-diagram" style="color:var(--teal)"></i> Tahapan Progress Bimbingan KTI</h3></div>
        <div class="stepper-container">
            <div class="stepper-line">
                <div class="stepper-line-fill" style="width: {{ ($currentStepIndex / (count($steps) - 1)) * 100 }}%"></div>
            </div>
            @foreach($steps as $idx => $step)
                @php
                    $isDone = $idx < $currentStepIndex || $currentBab === 'Selesai';
                    $isActive = $idx === $currentStepIndex && $currentBab !== 'Selesai';
                    $class = $isDone ? 'done' : ($isActive ? 'active' : '');
                @endphp
                <div class="step-item">
                    <div class="step-node {{ $class }}">
                        @if($isDone)
                            <i class="fas fa-check"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <div class="step-label {{ $class }}">{{ $step }}</div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="grid-2">
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><h3><i class="fas fa-cloud-upload-alt" style="color:var(--blue)"></i> Unggah Draf Tulisan</h3></div>
            @if($currentBab === 'Selesai')
                <div class="status-alert" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#16a34a">
                    <i class="fas fa-check-circle" style="font-size:18px"></i>
                    <span>Karya Tulis Ilmiah (KTI) Anda telah rampung dan dinilai oleh tim guru!</span>
                </div>
            @elseif($currentBab === 'Siap Sidang')
                <div class="status-alert" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8">
                    <i class="fas fa-info-circle" style="font-size:18px"></i>
                    <span>Draf KTI Anda telah disetujui sepenuhnya (ACC Draft Akhir). Silakan tunggu jadwal ujian sidang.</span>
                </div>
            @elseif($isPending)
                <div class="status-alert" style="background:#fffbeb; border:1px solid #fef3c7; color:#d97706">
                    <i class="fas fa-clock" style="font-size:18px"></i>
                    <span>Draf bimbingan untuk <strong>{{ $currentBab }}</strong> sedang dalam proses review guru. Form pengunggahan dikunci.</span>
                </div>
            @else
                <form action="{{ route('siswa.kti.bimbingan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="bab" value="{{ $currentBab }}">
                    <div class="form-group" style="margin-bottom:14px">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Bab Bimbingan</label>
                        <input type="text" class="form-select" value="{{ $currentBab }} (Sedang Berjalan)" readonly style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;background:#f8fafc;font-weight:600">
                    </div>
                    <div class="form-group" style="margin-bottom:14px">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:6px">Metode Pengumpulan Draf</label>
                        <div style="display:flex; gap:16px;">
                            <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; font-weight:600; color:var(--gray-600)">
                                <input type="radio" x-model="fileType" value="link" style="width:16px;height:16px"> Tautan Google Docs / Drive
                            </label>
                            <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; font-weight:600; color:var(--gray-600)">
                                <input type="radio" x-model="fileType" value="upload" style="width:16px;height:16px"> Unggah File (PDF/Word)
                            </label>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:14px">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Tautan / Dokumen Draf</label>
                        <template x-if="fileType === 'link'">
                            <input type="url" name="file_draft" placeholder="https://docs.google.com/document/d/..." required style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px">
                        </template>
                        <template x-if="fileType === 'upload'">
                            <input type="file" name="file_draft" required accept=".pdf,.doc,.docx" style="width:100%;padding:6px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px">
                        </template>
                    </div>
                    <div class="form-group" style="margin-bottom:18px">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:4px">Catatan Tambahan untuk Pembimbing</label>
                        <textarea name="catatan_siswa" placeholder="Catatan atau masukan yang ingin disampaikan ke guru..." style="width:100%;height:80px;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;resize:none;font-family:inherit"></textarea>
                    </div>
                    <button type="submit" style="background:var(--blue); color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:700; display:inline-flex; align-items:center; gap:6px; width:100%; justify-content:center">
                        <i class="fas fa-paper-plane"></i> Kirim Draf Bimbingan
                    </button>
                </form>
            @endif
        </div>
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><h3><i class="fas fa-star" style="color:var(--orange)"></i> Penilaian Karya Tulis Ilmiah</h3></div>
            @if($nilaiKti && $nilaiKti->current_bab === 'Selesai')
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom:16px">
                    <div style="background:#f8fafc; padding:12px; border-radius:8px; text-align:center">
                        <span style="font-size:11px; color:var(--gray-400); font-weight:600">Nilai Bimbingan (30%)</span>
                        <div style="font-size:24px; font-weight:800; color:var(--blue); margin-top:4px">{{ $nilaiKti->nilai_proses }}</div>
                    </div>
                    <div style="background:#f8fafc; padding:12px; border-radius:8px; text-align:center">
                        <span style="font-size:11px; color:var(--gray-400); font-weight:600">Nilai Tulisan (40%)</span>
                        <div style="font-size:24px; font-weight:800; color:var(--teal); margin-top:4px">{{ $nilaiKti->nilai_tulisan }}</div>
                    </div>
                    <div style="background:#f8fafc; padding:12px; border-radius:8px; text-align:center">
                        <span style="font-size:11px; color:var(--gray-400); font-weight:600">Nilai Sidang (30%)</span>
                        <div style="font-size:24px; font-weight:800; color:var(--purple); margin-top:4px">{{ $nilaiKti->nilai_sidang }}</div>
                    </div>
                    <div style="background:#e6fcf5; border:1px solid #c3fae8; padding:12px; border-radius:8px; text-align:center">
                        <span style="font-size:11px; color:#0ca678; font-weight:700">NILAI AKHIR KTI</span>
                        <div style="font-size:24px; font-weight:900; color:#0ea5e9; margin-top:4px">{{ $nilaiKti->nilai_akhir }}</div>
                    </div>
                </div>
                @if($nilaiKti->catatan)
                    <div style="background:#f8fafc; border-left:4px solid var(--orange); padding:12px; border-radius:4px; font-size:13px; font-style:italic; color:var(--gray-500)">
                        "{{ $nilaiKti->catatan }}"
                    </div>
                @endif
            @else
                <p style="text-align:center; color:var(--gray-400); font-size:13px; padding:40px 10px">Penilaian akan dirilis secara resmi oleh tim guru setelah ujian sidang KTI dilaksanakan.</p>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-history" style="color:var(--purple)"></i> Riwayat Pengumpulan Draf KTI</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Kirim</th>
                        <th>Bab KTI</th>
                        <th>File Draf</th>
                        <th>Catatan Siswa</th>
                        <th>Status Review</th>
                        <th>Umpan Balik Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ktiBimbingans as $b)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($b->created_at)->isoFormat('D MMMM YYYY - HH:mm') }}</td>
                            <td><strong style="color:var(--text-dark)">{{ $b->bab }}</strong></td>
                            <td>
                                @if(filter_var($b->file_draft, FILTER_VALIDATE_URL))
                                    <a href="{{ $b->file_draft }}" target="_blank" style="color:var(--blue); font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px"><i class="fas fa-external-link-alt"></i> Buka Link</a>
                                @else
                                    <a href="{{ $b->file_draft }}" download style="color:var(--blue); font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px"><i class="fas fa-file-download"></i> Unduh File</a>
                                @endif
                            </td>
                            <td style="max-width:180px; font-size:13px; color:var(--gray-500)">{{ $b->catatan_siswa ?? '-' }}</td>
                            <td>
                                @if($b->status === 'pending')
                                    <span class="badge light orange">⏳ Review</span>
                                @elseif($b->status === 'disetujui')
                                    <span class="badge light green">✅ ACC</span>
                                @else
                                    <span class="badge light red">❌ Revisi</span>
                                @endif
                            </td>
                            <td style="max-width:200px; font-size:13px; color:var(--gray-600)">{{ $b->catatan_guru ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:var(--gray-400); padding:20px">Belum ada riwayat pengunggahan draf bimbingan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
