@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah - LMS Al Azhar Jaya Indonesia')

@section('sidebar')
    <li :class="{ 'active': tab === 'dashboard' }" @click="tab = 'dashboard'">
        <label><i class="fas fa-th-large"></i> Dashboard</label>
    </li>
    <li :class="{ 'active': tab === 'siswa' }" @click="tab = 'siswa'">
        <label><i class="fas fa-user-graduate"></i> Siswa</label>
    </li>
    <li :class="{ 'active': tab === 'guru' }" @click="tab = 'guru'">
        <label><i class="fas fa-chalkboard-teacher"></i> Guru</label>
    </li>
    <li :class="{ 'active': tab === 'ortu' }" @click="tab = 'ortu'">
        <label><i class="fas fa-users"></i> Orang Tua</label>
    </li>
    <li :class="{ 'active': tab === 'kelas' }" @click="tab = 'kelas'">
        <label><i class="fas fa-school"></i> Kelas</label>
    </li>
    <li :class="{ 'active': tab === 'kurikulum' }" @click="tab = 'kurikulum'">
        <label><i class="fas fa-book-open"></i> Kurikulum &amp; KKM</label>
    </li>
    <li :class="{ 'active': tab === 'asesmen' }" @click="tab = 'asesmen'">
        <label><i class="fas fa-cubes"></i> Asesmen &amp; Ujian</label>
    </li>
    <li :class="{ 'active': tab === 'audit_guru' }" @click="tab = 'audit_guru'">
        <label><i class="fas fa-clipboard-check"></i> Audit &amp; Kinerja Guru</label>
    </li>
    <li :class="{ 'active': tab === 'bahan_ajar_approval' }" @click="tab = 'bahan_ajar_approval'">
        <label><i class="fas fa-folder-open"></i> Approval Bahan Ajar</label>
    </li>
    <li :class="{ 'active': tab === 'karya_tahfidz' }" @click="tab = 'karya_tahfidz'">
        <label><i class="fas fa-medal"></i> Karya Tulis &amp; Tahfidz</label>
    </li>
    <li :class="{ 'active': tab === 'cbt' }" @click="tab = 'cbt'">
        <label><i class="fas fa-laptop"></i> CBT Approval</label>
    </li>
    <li :class="{ 'active': tab === 'pengaturan' }" @click="tab = 'pengaturan'">
        <label><i class="fas fa-cog"></i> Pengaturan</label>
    </li>
    <li :class="{ 'active': tab === 'rapor_supervisi' }" @click="tab = 'rapor_supervisi'">
        <label><i class="fas fa-file-contract"></i> Rapor Supervisi</label>
    </li>
@endsection

@section('content')

    <div x-show="tab === 'dashboard'">
        <div>
            <style>
                .score-row-card:hover {
                    transform: translateY(-2px);
                    background-color: var(--gray-50) !important;
                }

                .pulse-dot {
                    position: relative;
                    padding-right: 20px !important;
                }

                .pulse-dot::after {
                    content: '';
                    position: absolute;
                    width: 8px;
                    height: 8px;
                    background: #ef4444;
                    border-radius: 50%;
                    top: 50%;
                    right: 8px;
                    transform: translateY(-50%);
                    animation: pulse-glow 1.5s infinite;
                }

                @keyframes pulse-glow {
                    0% {
                        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
                    }

                    70% {
                        box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
                    }

                    100% {
                        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
                    }
                }
            </style>

            <div class="content-header">
                <div>
                    <h1>Dashboard Kepala Sekolah</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Selamat datang, Kepala Sekolah</p>
                </div>
                <div class="header-right">
                    <span class="badge teal" style="font-size:12px;padding:6px 16px"><i class="fas fa-school"></i> SMPIT</span>
                    <div class="avatar teal">KS</div>
                </div>
            </div>

            <div class="grid-4" style="margin-bottom:20px">
                <div class="admin-stat-card">
                    <div class="asc-icon grad-teal"><i class="fas fa-user-graduate"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $statSiswa ?? 0 }}</div>
                        <div class="asc-label">Total Siswa</div>
                        <div class="asc-compare up">&#x2191; {{ $siswaBaru ?? 0 }} siswa baru bulan ini</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="asc-icon grad-blue"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $statGuru ?? 0 }}</div>
                        <div class="asc-label">Total Guru</div>
                        <div class="asc-compare up">&#x2191; {{ $guruBaru ?? 0 }} guru baru tahun ini</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="asc-icon grad-orange"><i class="fas fa-users"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $statOrtu ?? 0 }}</div>
                        <div class="asc-label">Total Orang Tua</div>
                        <div class="asc-compare up">&#x2191; {{ $ortuBaru ?? 0 }} akun baru bulan ini</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="asc-icon grad-purple"><i class="fas fa-school"></i></div>
                    <div class="asc-body">
                        <div class="asc-number">{{ $statKelas ?? 0 }}</div>
                        <div class="asc-label">Kelas Aktif</div>
                        <div class="asc-compare up">&#x2191; {{ $kelasBaru ?? 0 }} kelas baru tahun ini</div>
                    </div>
                </div>
            </div>

            <div class="grid-2" style="margin-bottom:20px">
                <div class="card" x-data="{
                    scores: [
                        { label: 'Akademik Nasional', val: {{ $akademikNasional ?? 0 }}, target: {{ $targetAkademik ?? 80 }}, trend: 'Evaluasi Otomatis', type: '{{ $typAkademik ?? 'up' }}', status: '{{ $stsAkademik ?? 'Sangat Baik' }}', color: '{{ $clrAkademik ?? 'var(--teal)' }}', desc: 'Rata-rata seluruh nilai akademik reguler (Nasional) dari seluruh siswa aktif.' },
                        { label: 'Bahasa Inggris / Internasional', val: {{ $internasional ?? 0 }}, target: {{ $targetInternasional ?? 75 }}, trend: 'Evaluasi Otomatis', type: '{{ $typInternasional ?? 'up' }}', status: '{{ $stsInternasional ?? 'Pemantauan' }}', color: '{{ $clrInternasional ?? 'var(--orange)' }}', desc: 'Rata-rata penilaian program unggulan/bilingual lintas mata pelajaran.' },
                        { label: 'Hafalan Quran / Tahfidz', val: {{ $tahfidz ?? 0 }}, target: {{ $targetTahfidz ?? 85 }}, trend: 'Evaluasi Otomatis', type: '{{ $typTahfidz ?? 'up' }}', status: '{{ $stsTahfidz ?? 'Sangat Baik' }}', color: '{{ $clrTahfidz ?? 'var(--purple)' }}', desc: 'Kalkulasi konversi dari evaluasi makhroj, tajwid, dan kelancaran per ayat secara keseluruhan.' }
                    ],
                    selectedScore: null
                }">

                    <div class="card-header">
                        <h3><i class="fas fa-chart-line" style="color:var(--teal)"></i> Ringkasan 3 Nilai Besar Sekolah</h3>
                        <span class="badge light green" style="font-size:11px">{{ $semester ?? 'Semester Ganjil' }}</span>
                    </div>
                    <p style="font-size:12px;color:var(--gray-400);margin-bottom:12px">Pilih nilai untuk melihat analisis
                        dan rekomendasi peningkatan.</p>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <template x-for="(sc, i) in scores" :key="i">
                            <div @click="selectedScore = (selectedScore === i ? null : i)" class="score-row-card"
                                style="border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 10px 14px; cursor: pointer; transition: all 0.2s; background: var(--white);"
                                :style="selectedScore === i ? 'border-color: ' + sc.color + '; box-shadow: var(--shadow-sm);' :
                                    ''">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div>
                                        <strong style="font-size: 13.5px; color: var(--text);" x-text="sc.label"></strong>
                                        <span class="badge light" :class="sc.type === 'up' ? 'green' : 'orange'"
                                            style="font-size: 9.5px; margin-left: 6px; padding: 2px 6px;">
                                            <i class="fas"
                                                :class="sc.type === 'up' ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                                            <span x-text="sc.trend"></span>
                                        </span>
                                    </div>
                                    <div style="text-align: right;">
                                        <span style="font-size: 18px; font-weight: 700;"
                                            :style="'color: ' + sc.color + ';'" x-text="sc.val"></span>
                                        <span style="font-size: 11px; color: var(--gray-400);">/100</span>
                                    </div>
                                </div>

                                <div class="progress-wrap"
                                    style="margin: 0 0 4px 0; background: var(--gray-100); height: 6px; border-radius: 3px; overflow: hidden;">
                                    <div class="fill"
                                        :style="'width: ' + sc.val + '%; background: ' + sc.color +
                                            '; height: 100%; border-radius: 3px; transition: width 0.5s;'">
                                    </div>
                                </div>

                                <div
                                    style="display: flex; justify-content: space-between; font-size: 11px; color: var(--gray-400);">
                                    <span>Target Kelulusan: <span x-text="sc.target"></span></span>
                                    <span style="font-weight: 600;" :style="'color: ' + sc.color"
                                        x-text="sc.status"></span>
                                </div>

                                <div x-show="selectedScore === i" x-transition
                                    style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border); font-size: 12px; color: var(--gray-600); line-height: 1.5;">
                                    <i class="fas fa-info-circle" :style="'color: ' + sc.color"></i> <span
                                        x-text="sc.desc"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="card" x-data="{
                    alerts: {{ \Illuminate\Support\Js::from($kelasGakSehat ?? []) }},
                    showToast: false,
                    toastMsg: '',
                    sendAlert(kelas, wali, waliUserId) {
                        if (!waliUserId) {
                            this.toastMsg = 'Wali kelas belum diatur untuk kelas ' + kelas;
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 4000);
                            return;
                        }
                
                        fetch('{{ route('dashboard.pesan-wali') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    wali_user_id: waliUserId,
                                    kelas: kelas
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.toastMsg = 'Pesan peringatan resmi terkirim ke Wali Kelas ' + kelas + ' (' + wali + ')!';
                                    this.showToast = true;
                                    setTimeout(() => this.showToast = false, 4000);
                                }
                            })
                            .catch(error => {
                                this.toastMsg = 'Gagal mengirim pesan.';
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 4000);
                            });
                    }
                }">
                    <div class="card-header">
                        <h3><i class="fas fa-heart-pulse" style="color:var(--red)"></i> Peringatan Kelas &quot;Gak
                            Sehat&quot;</h3>
                        <span class="badge red pulse-dot" style="font-size: 11px;" x-show="alerts.length > 0"><i
                                class="fas fa-exclamation-triangle"></i> <span x-text="alerts.length"></span>
                            Peringatan</span>
                    </div>
                    <p style="font-size:12px;color:var(--gray-400);margin-bottom:12px">Mendeteksi potensi stress /
                        ketidaknyamanan siswa berdasarkan input jurnal guru harian.</p>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div x-show="alerts.length === 0"
                            style="padding: 16px; text-align: center; color: var(--gray-400); font-size: 12px; border: 1px dashed var(--border-light); border-radius: var(--radius-sm);">
                            <i class="fas fa-check-circle"
                                style="color: var(--teal); font-size: 24px; margin-bottom: 8px; display: block;"></i>
                            Tidak ada kelas dengan peringatan. Semua kelas berada di zona aman.
                        </div>
                        <template x-for="(al, i) in alerts" :key="i">
                            <div style="border-radius: var(--radius-sm); padding: 12px; display: flex; flex-direction: column; gap: 6px;"
                                :style="'background: ' + al.bg + '; border: 1px solid ' + al.border">
                                <div style="display: flex; justify-content: space-between; align-items: center; gap:10px;">
                                    <div>
                                        <span class="badge"
                                            :style="'background: ' + al.color +
                                                '; color: white; font-size: 10px; font-weight: 700;'"
                                            x-text="'Kelas ' + al.kelas"></span>
                                        <span style="font-size: 11px; color: var(--gray-500); margin-left: 6px;">Wali:
                                            <strong x-text="al.wali"></strong></span>
                                    </div>
                                    <span class="badge light" :class="al.status === 'Kritis' ? 'red' : 'orange'"
                                        style="font-size: 10px; font-weight: 600;" x-text="al.stressLevel"></span>
                                </div>
                                <p style="font-size: 12px; color: #475569; line-height: 1.5; margin: 2px 0;"
                                    x-text="al.detail"></p>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; padding-top: 6px; border-top: 1px dashed rgba(0,0,0,0.06);">
                                    <span style="font-size: 11.5px; color: var(--gray-500);">
                                        <i class="fas fa-smile" :style="'color: ' + al.color"></i> Rata-rata Mood: <strong
                                            x-text="al.mood"></strong>
                                    </span>
                                    <button @click="sendAlert(al.kelas, al.wali, al.wali_user_id)"
                                        class="btn-small outline"
                                        style="padding: 4px 8px; font-size: 10px; font-weight: 600; background: white; border-radius: 4px; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='#ef4444';this.style.color='white';this.style.borderColor='#ef4444'"
                                        onmouseout="this.style.background='white';this.style.color='var(--text)';this.style.borderColor='var(--border)'">
                                        <i class="fas fa-paper-plane"></i> Hubungi Wali
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform translate-y-2"
                        style="position: fixed; bottom: 24px; right: 24px; background: #ef4444; color: white; padding: 12px 24px; border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 9999; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> <span x-text="toastMsg"></span>
                    </div>
                </div>
            </div>

            <div class="grid-2" style="margin-bottom:20px">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-signal" style="color:var(--teal)"></i> Aktivitas Login (30 Hari)</h3><label
                            @click="tab='pengaturan'"
                            style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Detail</label>
                    </div>
                    <div class="chart-area">
                        <div class="chart-bar"
                            style="height:60%;background:linear-gradient(to top,var(--teal-light),var(--teal))"><span
                                class="bar-label">Sen</span></div>
                        <div class="chart-bar"
                            style="height:80%;background:linear-gradient(to top,var(--teal-light),var(--teal))"><span
                                class="bar-label">Sel</span></div>
                        <div class="chart-bar" style="height:45%"><span class="bar-label">Rab</span></div>
                        <div class="chart-bar" style="height:90%"><span class="bar-label">Kam</span></div>
                        <div class="chart-bar" style="height:70%"><span class="bar-label">Jum</span></div>
                        <div class="chart-bar" style="height:30%"><span class="bar-label">Sab</span></div>
                        <div class="chart-bar" style="height:25%"><span class="bar-label">Min</span></div>
                        <div class="chart-bar" style="height:65%"><span class="bar-label">Sen</span></div>
                        <div class="chart-bar" style="height:85%"><span class="bar-label">Sel</span></div>
                        <div class="chart-bar" style="height:50%"><span class="bar-label">Rab</span></div>
                        <div class="chart-bar" style="height:75%"><span class="bar-label">Kam</span></div>
                        <div class="chart-bar" style="height:95%"><span class="bar-label">Jum</span></div>
                        <div class="chart-bar" style="height:35%"><span class="bar-label">Sab</span></div>
                        <div class="chart-bar" style="height:20%"><span class="bar-label">Min</span></div>
                    </div>
                    <p style="font-size:11px;color:var(--gray-400);text-align:center">Login per hari (14 hari terakhir)</p>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-layer-group" style="color:var(--blue)"></i> Distribusi Siswa per Jenjang</h3>
                        <label @click="tab='siswa'"
                            style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Detail</label>
                    </div>
                    <div class="h-bar-group">
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 1-3 SD</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill teal" style="width:90%">180</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 4-6 SD</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill blue" style="width:72%">145</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 7</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill orange" style="width:58%">80</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 8</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill purple" style="width:50%">70</div>
                            </div>
                        </div>
                        <div class="h-bar-row">
                            <div class="h-bar-label">Kelas 9</div>
                            <div class="h-bar-track">
                                <div class="h-bar-fill pink" style="width:38%">55</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:20px;" x-data="{
                searchQuery: '',
                filterTab: 'semua',
                showToast: false,
                toastMsg: '',
                guruData: {{ \Illuminate\Support\Js::from($guruKinerja ?? []) }},
                sendAssistance(g) {
                    const tipe = g.status.includes('Kritis') ? 'bimbingan' : 'selamat';
                    fetch('/dashboard/pesan-kinerja', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            penerima_id: g.user_id,
                            tipe: tipe
                        })
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            this.toastMsg = tipe === 'selamat' ?
                                'Ucapan selamat kinerja berhasil dikirim ke ' + g.nama + '!' :
                                'Surat panggilan bimbingan otomatis terkirim ke ' + g.nama + '!';
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 4000);
                        }
                    }).catch(err => {
                        this.toastMsg = 'Gagal mengirim pesan!';
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 4000);
                    });
                },
                filteredGuru() {
                    return this.guruData.filter(g => {
                        const matchesSearch = g.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) || g.mapel.toLowerCase().includes(this.searchQuery.toLowerCase());
                        if (this.filterTab === 'semua') return matchesSearch;
                        return matchesSearch && g.status === 'Butuh Bimbingan';
                    });
                }
            }">
                <div class="card-header"
                    style="flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
                    <h3><i class="fas fa-trophy" style="color:var(--orange)"></i> Rapor Indeks Kinerja Guru</h3>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <div class="input-wrap"
                            style="padding: 6px 12px; display: flex; align-items: center; gap: 6px; width: 220px;">
                            <i class="fas fa-search" style="color: var(--gray-400); font-size: 13px;"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari guru..."
                                style="border:none; outline:none; font-size:12px; font-family:var(--font); width:100%;">
                        </div>

                        <div
                            style="display: flex; background: var(--gray-100); border-radius: var(--radius-sm); padding: 2px;">
                            <button @click="filterTab = 'semua'" class="filter-btn"
                                :class="filterTab === 'semua' ? 'active' : ''"
                                style="padding: 6px 12px; font-size: 11.5px; border:none; border-radius:4px; cursor:pointer;"
                                :style="filterTab === 'semua' ?
                                    'background:white; font-weight:600; box-shadow:var(--shadow-sm);' :
                                    'background:transparent; color:var(--gray-500);'">Semua</button>
                            <button @click="filterTab = 'bimbingan'" class="filter-btn"
                                :class="filterTab === 'bimbingan' ? 'active' : ''"
                                style="padding: 6px 12px; font-size: 11.5px; border:none; border-radius:4px; cursor:pointer;"
                                :style="filterTab === 'bimbingan' ?
                                    'background:white; font-weight:600; box-shadow:var(--shadow-sm);' :
                                    'background:transparent; color:var(--gray-500);'">Butuh
                                Bimbingan</button>
                        </div>
                    </div>
                </div>

                <div class="table-wrap" style="margin-top: 10px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Guru</th>
                                <th>Mata Pelajaran</th>
                                <th style="width: 35%;">Skor Indeks Kinerja</th>
                                <th>Skor</th>
                                <th>Rekomendasi Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(g, i) in filteredGuru()" :key="i">
                                <tr style="transition: background-color 0.2s;"
                                    onmouseover="this.style.backgroundColor='var(--gray-50)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td>
                                        <strong style="color:var(--text); font-size:13px;" x-text="g.nama"></strong>
                                        <div style="font-size:11px; color:var(--gray-400); margin-top:2px;"
                                            x-text="g.detail"></div>
                                    </td>
                                    <td style="color:var(--gray-500); font-size:12.5px;" x-text="g.mapel"></td>
                                    <td>
                                        <div class="progress-wrap"
                                            style="margin: 0; background: var(--gray-100); height: 6px; border-radius: 3px; overflow: hidden;">
                                            <div class="fill"
                                                :style="'width: ' + g.skor + '%; background: ' + g.color +
                                                    '; height: 100%; border-radius: 3px;'">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge light"
                                            :class="g.skor >= 90 ? 'green' : (g.skor >= 70 ? 'orange' : (g.skor >= 50 ? 'red' :
                                                'black'))"
                                            style="font-size:10.5px; font-weight:700;"
                                            x-text="g.skor + ' (' + g.status + ')'"></span>
                                    </td>
                                    <td>
                                        <button @click="sendAssistance(g)" class="btn-small outline"
                                            style="padding: 5px 10px; font-size:11px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; transition: all 0.2s;"
                                            :style="g.status.includes('Kritis') ?
                                                'border-color:var(--orange); color:var(--orange);' :
                                                'border-color:var(--border); color:var(--gray-500);'"
                                            onmouseover="this.style.background='var(--teal)'; this.style.color='white'; this.style.borderColor='var(--teal)'"
                                            onmouseout="this.style.background='transparent'; this.style.color=this.getAttribute('data-color')"
                                            :data-color="g.status.includes('Kritis') ? 'var(--orange)' : 'var(--gray-500)'">
                                            <i class="fas"
                                                :class="g.status.includes('Kritis') ? 'fa-handshake' : 'fa-check'"></i>
                                            <span
                                                x-text="g.status.includes('Kritis') ? 'Bimbing Guru' : 'Beri Selamat'"></span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredGuru().length === 0">
                                <td colspan="5" style="text-align: center; color: var(--gray-400); padding: 24px;">
                                    Tidak ada data guru.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    style="position: fixed; bottom: 24px; right: 24px; background: var(--teal); color: white; padding: 12px 24px; border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 9999; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check-circle"></i> <span x-text="toastMsg"></span>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history" style="color:var(--orange)"></i> Aktivitas Terbaru</h3><label
                        style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Semua</label>
                </div>
                <div class="filter-bar"><button class="filter-btn active">Semua</button><button
                        class="filter-btn">SD</button><button class="filter-btn">SMP</button></div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Jenis</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="color:var(--gray-400);font-size:12px">28 Mar 2026, 09:15</td>
                                <td><span class="badge light teal">User</span></td>
                                <td>User baru &mdash; Siti Aisyah (Siswa)</td>
                                <td><span class="badge light green">Selesai</span></td>
                            </tr>
                            <tr>
                                <td style="color:var(--gray-400);font-size:12px">28 Mar 2026, 08:30</td>
                                <td><span class="badge light blue">Kelas</span></td>
                                <td>Kelas 7C ditambahkan</td>
                                <td><span class="badge light green">Selesai</span></td>
                            </tr>
                            <tr>
                                <td style="color:var(--gray-400);font-size:12px">27 Mar 2026, 14:00</td>
                                <td><span class="badge light orange">Pengumuman</span></td>
                                <td>Pengumuman UTS dibuat</td>
                                <td><span class="badge light teal">Aktif</span></td>
                            </tr>
                            <tr>
                                <td style="color:var(--gray-400);font-size:12px">27 Mar 2026, 11:20</td>
                                <td><span class="badge light purple">Tugas</span></td>
                                <td>Tugas "Praktek Sholat" PAI 7A</td>
                                <td><span class="badge light teal">Aktif</span></td>
                            </tr>
                            <tr>
                                <td style="color:var(--gray-400);font-size:12px">26 Mar 2026, 15:45</td>
                                <td><span class="badge light pink">Laporan</span></td>
                                <td>Laporan bulanan Maret 2026</td>
                                <td><span class="badge light green">Selesai</span></td>
                            </tr>
                            <tr>
                                <td style="color:var(--gray-400);font-size:12px">25 Mar 2026, 13:30</td>
                                <td><span class="badge light red">Sistem</span></td>
                                <td>Pembaruan LMS versi 2.4.1</td>
                                <td><span class="badge light green">Selesai</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa'">
        <div>
            <div class="content-header">
                <h1>Manajemen Siswa</h1>
                <div class="header-right">
                    <label @click="tab='siswa-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Siswa</label>
                    <label class="header-btn outline" style="cursor:pointer"><i class="fas fa-upload"></i> Import</label>
                    <a href="{{ route('kepala.export.siswa') }}" class="header-btn outline"
                        style="text-decoration:none;display:inline-flex;align-items:center;gap:6px"><i
                            class="fas fa-file-excel"></i> Export Excel</a>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jenis Kelamin</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listSiswa ?? [] as $siswaData)
                                <tr>
                                    <td>{{ $siswaData->nis ?? '-' }}</td>
                                    <td><strong>{{ $siswaData->nama }}</strong></td>
                                    <td>{{ $siswaData->kelas->nama_kelas ?? '-' }}</td>
                                    <td>{{ $siswaData->jenis_kelamin ?? '-' }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label
                                            @click="tab='siswa-detail'; selectedSiswa = JSON.parse(atob('{{ base64_encode(json_encode($siswaData)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Detail</label>
                                        <label
                                            @click="tab='siswa-edit'; selectedSiswa = JSON.parse(atob('{{ base64_encode(json_encode($siswaData)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Edit</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        siswa</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listSiswa->appends(['tab' => 'siswa'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'guru'">
        <div>
            <div class="content-header">
                <h1>Manajemen Guru</h1>
                <div class="header-right">
                    <label @click="tab='guru-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Guru</label>
                    <a href="{{ route('kepala.export.guru') }}" class="header-btn outline"
                        style="text-decoration:none;display:inline-flex;align-items:center;gap:6px"><i
                            class="fas fa-file-excel"></i> Export Excel</a>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listGuru ?? [] as $guruData)
                                <tr>
                                    <td>{{ $guruData->nip ?? '-' }}</td>
                                    <td><strong>{{ $guruData->nama }}</strong></td>
                                    <td>{{ $guruData->mapels->pluck('nama_mapel')->unique()->implode(', ') ?: '-' }}</td>
                                    <td>{{ $guruData->jadwal->pluck('kelas.nama_kelas')->unique()->implode(', ') ?: '-' }}
                                    </td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label
                                            @click="tab='guru-detail'; selectedGuru = JSON.parse(atob('{{ base64_encode(json_encode($guruData)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Detail</label>
                                        <label
                                            @click="tab='guru-form'; selectedGuru = JSON.parse(atob('{{ base64_encode(json_encode($guruData)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Edit</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        guru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listGuru->appends(['tab' => 'guru'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'ortu'">
        <div>
            <div class="content-header">
                <h1>Manajemen Orang Tua</h1>
                <div class="header-right">
                    <label @click="tab='ortu-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Akun</label>
                    <a href="{{ route('kepala.export.orang-tua') }}" class="header-btn outline"
                        style="text-decoration:none;display:inline-flex;align-items:center;gap:6px"><i
                            class="fas fa-file-excel"></i> Export Excel</a>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Anak</th>
                                <th>Kelas</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listOrtu ?? [] as $ortu)
                                <tr>
                                    <td><strong>{{ $ortu->name }}</strong></td>
                                    <td>{{ $ortu->orangTua?->siswa->pluck('nama')->implode(', ') ?: '-' }}</td>
                                    <td>{{ $ortu->orangTua?->siswa->pluck('kelas.nama_kelas')->unique()->implode(', ') ?: '-' }}
                                    </td>
                                    <td>{{ $ortu->email }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label
                                            @click="tab='ortu-detail'; selectedOrtu = JSON.parse(atob('{{ base64_encode(json_encode($ortu)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Detail</label>
                                        <label
                                            @click="tab='ortu-form'; selectedOrtu = JSON.parse(atob('{{ base64_encode(json_encode($ortu)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Edit</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        orang tua</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listOrtu->appends(['tab' => 'ortu'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'kelas'" x-data="{ jenisKelas: 'umum' }">
        <div>
            <div class="content-header">
                <h1>Manajemen Kelas</h1>
                <div class="header-right" style="display:flex; align-items:center; gap:16px;">
                    <select x-model="jenisKelas" class="form-select"
                        style="min-width:180px; font-weight:600; padding:8px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:14px; background:white;">
                        <option value="umum">Kelas Umum</option>
                        <option value="quran">Kelas Qur'an</option>
                    </select>
                    <label @click="tab='kelas-form'" class="header-btn primary" style="cursor:pointer"><i
                            class="fas fa-plus"></i> Tambah Kelas</label>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card" x-show="jenisKelas === 'umum'">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Jenjang</th>
                                <th>Jumlah Siswa</th>
                                <th>Wali Kelas</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listKelas ?? [] as $kelasData)
                                <tr>
                                    <td><strong>{{ $kelasData->nama_kelas }}</strong></td>
                                    <td>{{ str_contains($kelasData->nama_kelas, '1') || str_contains($kelasData->nama_kelas, '2') || str_contains($kelasData->nama_kelas, '3') || str_contains($kelasData->nama_kelas, '4') || str_contains($kelasData->nama_kelas, '5') || str_contains($kelasData->nama_kelas, '6') ? 'SD' : 'SMP' }}
                                    </td>
                                    <td>{{ $kelasData->siswa_count ?? 0 }}</td>
                                    <td>{{ $kelasData->waliKelas->nama ?? '-' }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                    <td>
                                        <label
                                            @click="tab='kelas-form'; selectedKelas = JSON.parse(atob('{{ base64_encode(json_encode($kelasData)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Edit</label>
                                        <label
                                            @click="tab='kelas-detail'; selectedKelas = JSON.parse(atob('{{ base64_encode(json_encode($kelasData)) }}'))"
                                            class="btn-small outline" style="cursor:pointer">Detail</label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        kelas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listKelas->appends(['tab' => 'kelas'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>

            <div class="card" x-show="jenisKelas === 'quran'">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Jenjang</th>
                                <th>Kategori</th>
                                <th>Tingkat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listKelasQuran ?? [] as $kq)
                                <tr>
                                    <td><strong>{{ $kq->nama_kelas }}</strong></td>
                                    <td>{{ $kq->jenjang }}</td>
                                    <td>{{ $kq->kategori }}</td>
                                    <td>{{ $kq->tingkat }}</td>
                                    <td><span class="badge light green">Aktif</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; color:var(--gray-400);">Tidak ada data
                                        kelas Qur'an</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div style="margin-top:16px;">
                        {{ $listKelasQuran->appends(['tab' => 'kelas'])->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa-form'">
        <div>
            <div class="content-header">
                <h1>Tambah Siswa</h1>
                <div class="header-right">
                    <label @click="tab='siswa'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form action="/admin/siswa" method="POST">
                    @csrf
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>NIS</label>
                            <div class="input-wrap"><input type="text" name="nis" placeholder="NIS" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="nama" placeholder="Nama Lengkap"
                                    required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach (\App\Models\Kelas::all() as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:16px"><label>Email User (Untuk Login)</label>
                        <div class="input-wrap"><input type="email" name="email" placeholder="Email Siswa" required
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='siswa'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'guru-form'">
        <div>
            <div class="content-header">
                <h1><span>Tambah</span> Guru</h1>
                <div class="header-right">
                    <label @click="tab='guru'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="selectedGuru ? ('/admin/guru/' + selectedGuru.id) : '/admin/guru'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!selectedGuru">
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>NIP</label>
                            <div class="input-wrap"><input type="text" name="nip" placeholder="19870101"
                                    :value="selectedGuru?.nip" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">NIP wajib diisi</div>
                        </div>
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="nama"
                                    placeholder="Ustadz Ahmad Fauzi" :value="selectedGuru?.nama" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Nama wajib diisi</div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Telepon</label>
                            <div class="input-wrap"><input type="text" name="no_telp" placeholder="08..."
                                    :value="selectedGuru?.no_telp"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                        <div class="form-group"><label>Email</label>
                            <div class="input-wrap"><input type="email" name="email"
                                    placeholder="guru@alazharjayaindonesia.sch.id" :value="selectedGuru?.user?.email"
                                    required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Email wajib diisi</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='guru'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'ortu-form'">
        <div>
            <div class="content-header">
                <h1><span>Tambah</span> Orang Tua</h1>
                <div class="header-right">
                    <label @click="tab='ortu'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="selectedOrtu ? ('/admin/ortu/' + selectedOrtu.id) : '/admin/ortu'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!selectedOrtu">
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="name" placeholder="Bpk. Andi Pratama"
                                    :value="selectedOrtu?.name" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Nama wajib diisi</div>
                        </div>
                        <div class="form-group"><label>Telepon</label>
                            <div class="input-wrap"><input type="text" name="no_telp" placeholder="08..."
                                    :value="selectedOrtu?.orang_tua?.no_telp"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Email</label>
                            <div class="input-wrap"><input type="email" name="email" placeholder="ortu@email.com"
                                    :value="selectedOrtu?.email" required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                            <div class="form-error">Email wajib diisi</div>
                        </div>
                        <div class="form-group"><label>Alamat</label>
                            <div class="input-wrap"><input type="text" name="alamat" placeholder="Jl..."
                                    :value="selectedOrtu?.orang_tua?.alamat"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='ortu'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'kelas-form'">
        <div>
            <div class="content-header">
                <h1><span>Tambah</span> Kelas</h1>
                <div class="header-right">
                    <label @click="tab='kelas'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="selectedKelas ? ('/admin/kelas/' + selectedKelas.id) : '/admin/kelas'" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="!selectedKelas">
                    <div class="form-group" style="margin-bottom:16px"><label>Nama Kelas</label>
                        <div class="input-wrap"><input type="text" name="nama_kelas" placeholder="1A"
                                :value="selectedKelas?.nama_kelas" required
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                        <div class="form-error">Nama Kelas wajib diisi</div>
                    </div>
                    <div class="form-group" style="margin-bottom:16px"><label>Wali Kelas</label>
                        <select name="guru_id" class="form-select" :value="selectedKelas?.guru_id">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach ($allGurus ?? [] as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                        <div class="form-error">Wali Kelas wajib diisi</div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='kelas'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Siswa</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap siswa</p>
                </div>
                <div class="header-right">
                    <label @click="tab='siswa'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">AR</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-user-graduate" style="color:var(--teal)"></i> Identitas Siswa</h3><label
                        @click="tab='siswa-edit'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">NIS</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.nis || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Lengkap</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.nama || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jenis Kelamin</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.jenis_kelamin || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Kelas</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.kelas?.nama_kelas || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Tempat, Tgl Lahir</span>
                        <div style="font-weight:600"
                            x-text="(selectedSiswa?.tempat_lahir || '') + ', ' + (selectedSiswa?.tanggal_lahir || '')">
                        </div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Alamat</span>
                        <div style="font-weight:600" x-text="selectedSiswa?.alamat || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Orang Tua</span>
                        <div style="font-weight:600"
                            x-text="(selectedSiswa?.nama_ayah || selectedSiswa?.nama_ibu) || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">No. Telepon</span>
                        <div style="font-weight:600">-</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Email</span>
                        <div style="font-weight:600">-</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line" style="color:var(--blue)"></i> Ringkasan Akademik</h3>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;padding:8px 0">
                    <div style="text-align:center;padding:16px;background:var(--teal-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--teal)">85.3</div>
                        <div style="font-size:11px;color:var(--gray-400)">Rata-rata</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--blue-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--blue)">5</div>
                        <div style="font-size:11px;color:var(--gray-400)">Peringkat</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--green-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--green)">112</div>
                        <div style="font-size:11px;color:var(--gray-400)">Hadir</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--orange-bg);border-radius:var(--radius-sm)">
                        <div style="font-size:24px;font-weight:700;color:var(--orange)">3</div>
                        <div style="font-size:11px;color:var(--gray-400)">Tugas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'guru-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Guru</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap guru</p>
                </div>
                <div class="header-right">
                    <label @click="tab='guru'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar blue">DS</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard-teacher" style="color:var(--blue)"></i> Identitas Guru</h3><label
                        @click="tab='guru-form'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">NIP</span>
                        <div style="font-weight:600" x-text="selectedGuru?.nip || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Lengkap</span>
                        <div style="font-weight:600" x-text="selectedGuru?.nama || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jenis Kelamin</span>
                        <div style="font-weight:600">-</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Mata Pelajaran</span>
                        <div style="font-weight:600"
                            x-text="selectedGuru?.mapels?.map(m => m.nama_mapel).join(', ') || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">No. Telepon</span>
                        <div style="font-weight:600" x-text="selectedGuru?.no_telp || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Email</span>
                        <div style="font-weight:600">-</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Alamat</span>
                        <div style="font-weight:600" x-text="selectedGuru?.alamat || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Status</span>
                        <div><span class="badge teal" x-text="selectedGuru?.status || 'Aktif'"></span></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard" style="color:var(--teal)"></i> Kelas yang Diajar</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Wali Kelas</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="jadwal in (selectedGuru?.jadwal || [])" :key="jadwal.id">
                                <tr>
                                    <td><strong x-text="jadwal.kelas?.nama_kelas || '-'"></strong></td>
                                    <td x-text="jadwal.kelas?.siswa_count || '0'"></td>
                                    <td x-text="jadwal.kelas?.wali_kelas?.nama || '-'"></td>
                                    <td><label @click="tab='kelas-detail'; selectedKelas = jadwal.kelas"
                                            class="btn-small teal" style="cursor:pointer">Detail</label></td>
                                </tr>
                            </template>
                            <tr x-show="!(selectedGuru?.jadwal?.length)">
                                <td colspan="4" style="text-align:center; color:var(--gray-400)">Tidak ada data kelas
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'ortu-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Orang Tua</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap orang tua/wali</p>
                </div>
                <div class="header-right">
                    <label @click="tab='ortu'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar orange">SR</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-users" style="color:var(--orange)"></i> Identitas Orang Tua</h3><label
                        @click="tab='ortu-form'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Orang Tua</span>
                        <div style="font-weight:600" x-text="selectedOrtu?.name || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">No. Telepon</span>
                        <div style="font-weight:600"
                            x-text="(selectedOrtu?.orang_tua || selectedOrtu?.orangTua)?.no_telp || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Email</span>
                        <div style="font-weight:600" x-text="selectedOrtu?.email || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Alamat</span>
                        <div style="font-weight:600"
                            x-text="(selectedOrtu?.orang_tua || selectedOrtu?.orangTua)?.alamat || '-'"></div>
                    </div>
                    <div style="grid-column: span 2"><span style="font-size:12px;color:var(--gray-400)">Anak</span>
                        <div style="font-weight:600"
                            x-text="(selectedOrtu?.orang_tua || selectedOrtu?.orangTua)?.siswa?.map(s => s.nama + ' (' + (s.kelas?.nama_kelas || '-') + ')').join(', ') || '-'">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'kelas-detail'">
        <div>
            <div class="content-header">
                <div>
                    <h1>Detail Kelas</h1>
                    <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Informasi lengkap kelas</p>
                </div>
                <div class="header-right">
                    <label @click="tab='kelas'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">7A</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-school" style="color:var(--teal)"></i> Informasi Kelas</h3><label
                        @click="tab='kelas-form'" class="btn-small teal" style="cursor:pointer"><i
                            class="fas fa-edit"></i> Edit</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding:8px 0">
                    <div><span style="font-size:12px;color:var(--gray-400)">Nama Kelas</span>
                        <div style="font-weight:600" x-text="selectedKelas?.nama_kelas || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jenjang</span>
                        <div style="font-weight:600"
                            x-text="['1','2','3','4','5','6'].some(c => (selectedKelas?.nama_kelas || '').includes(c)) ? 'SD' : 'SMP'">
                        </div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Wali Kelas</span>
                        <div style="font-weight:600" x-text="selectedKelas?.wali_kelas?.nama || '-'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Jumlah Siswa</span>
                        <div style="font-weight:600" x-text="selectedKelas?.siswa_count || '0'"></div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Tahun Ajaran</span>
                        <div style="font-weight:600">2025/2026</div>
                    </div>
                    <div><span style="font-size:12px;color:var(--gray-400)">Status</span>
                        <div><span class="badge teal">Aktif</span></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-graduate" style="color:var(--blue)"></i> Daftar Siswa</h3><label
                        style="font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;text-decoration:none">Lihat
                        Semua</label>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>L/P</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(siswa, index) in (selectedKelas?.siswa || [])" :key="siswa.id">
                                <tr>
                                    <td x-text="index + 1"></td>
                                    <td x-text="siswa.nis || '-'"></td>
                                    <td><strong x-text="siswa.nama || '-'"></strong></td>
                                    <td x-text="(siswa.jenis_kelamin || '-').substring(0, 1)"></td>
                                    <td><label @click="tab='siswa-detail'; selectedSiswa = siswa"
                                            class="btn-small outline" style="cursor:pointer">Detail</label></td>
                                </tr>
                            </template>
                            <tr x-show="!(selectedKelas?.siswa?.length)">
                                <td colspan="5" style="text-align:center; color:var(--gray-400)">Tidak ada data siswa
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'siswa-edit'">
        <div>
            <div class="content-header">
                <h1>Edit Siswa</h1>
                <div class="header-right">
                    <label @click="tab='siswa'" class="header-btn outline" style="cursor:pointer"><i
                            class="fas fa-arrow-left"></i> Kembali</label>
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="card" style="max-width:600px">
                <form :action="'/admin/siswa/' + selectedSiswa?.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>NIS</label>
                            <div class="input-wrap"><input type="text" name="nis" :value="selectedSiswa?.nis"
                                    required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                        <div class="form-group"><label>Nama Lengkap</label>
                            <div class="input-wrap"><input type="text" name="nama" :value="selectedSiswa?.nama"
                                    required
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                            </div>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="form-group"><label>Kelas</label>
                            <div class="input-wrap"><input type="text" readonly disabled
                                    :value="selectedSiswa?.kelas?.nama_kelas || '-'"
                                    style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%;background:transparent;">
                            </div>
                        </div>
                        <div class="form-group"><label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" :value="selectedSiswa?.jenis_kelamin"
                                required>
                                <option value="L">L</option>
                                <option value="P">P</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:16px"><label>Email</label>
                        <div class="input-wrap"><input type="email" name="email" :value="selectedSiswa?.user?.email"
                                required
                                style="border:none;outline:none;padding:10px 0;font-size:14px;font-family:var(--font);width:100%">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:8px">
                        <button type="submit" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;border:none;font-size:14px;font-family:var(--font);font-weight:700;"><i
                                class="fas fa-save"></i> Simpan</button>
                        <label @click="tab='siswa'" class="btn-login"
                            style="text-align:center;flex:1;cursor:pointer;background:var(--gray-300);color:var(--text)"><i
                                class="fas fa-times"></i> Batal</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="tab === 'kurikulum'" x-data="{
        showToast: false,
        toastMsg: '',
        newPair1: '',
        newPair2: '',
        kkmList: window.kkmListData,
        mapelPairs: [
            { mapel1: 'Matematika', mapel2: 'Mathematics' },
            { mapel1: 'IPA', mapel2: 'Science' }
        ],
        saveKKM() {
            this.toastMsg = 'Pengaturan KKM berhasil disimpan!';
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        },
        addPair() {
            if (!this.newPair1 || !this.newPair2) return;
            this.mapelPairs.push({ mapel1: this.newPair1, mapel2: this.newPair2 });
            this.newPair1 = '';
            this.newPair2 = '';
            this.toastMsg = 'Pasangan Mata Pelajaran ditambahkan!';
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        },
        removePair(index) {
            this.mapelPairs.splice(index, 1);
            this.toastMsg = 'Pasangan Mata Pelajaran dihapus!';
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        }
    }">
        <div class="content-header">
            <div>
                <h1>Kurikulum &amp; KKM</h1>
                <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Kelola KKM Mata Pelajaran &amp; Pasangan
                    Mapel Bilingual</p>
            </div>
            <div class="header-right">
                <div class="avatar teal">KS</div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-sliders-h" style="color:var(--teal)"></i> Pengaturan Nilai Minimum (KKM)</h3>
                </div>
                <div class="table-wrap" style="margin-bottom: 20px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>KKM Biasa</th>
                                <th>KKM Unggulan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(kkm, index) in kkmList" :key="index">
                                <tr>
                                    <td style="font-weight: 600;" x-text="kkm.mapel"></td>
                                    <td>
                                        <div class="input-wrap"
                                            style="border: 1px solid var(--border-light); border-radius: 4px; padding: 2px 6px; width: 80px; display: inline-block;">
                                            <input type="number" x-model.number="kkm.biasa"
                                                style="border:none; outline:none; width: 100%; font-size: 13px; text-align: center;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-wrap"
                                            style="border: 1px solid var(--border-light); border-radius: 4px; padding: 2px 6px; width: 80px; display: inline-block;">
                                            <input type="number" x-model.number="kkm.unggulan"
                                                style="border:none; outline:none; width: 100%; font-size: 13px; text-align: center;">
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <button @click="saveKKM()" class="btn-primary btn-small"
                    style="display: block; width: 100%; text-align: center; border-radius: 8px;">
                    <i class="fas fa-save"></i> Simpan Nilai KKM
                </button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-link" style="color:var(--blue)"></i> Pasangan Mata Pelajaran (Bilingual)</h3>
                </div>

                <div
                    style="background: var(--blue-bg); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); margin-bottom: 16px;">
                    <p style="font-size: 12px; color: var(--gray-600); line-height: 1.4;">
                        Tentukan pasangan mapel nasional dengan pasangan mapel internasionalnya untuk pelaporan rapor
                        terintegrasi.
                    </p>
                </div>

                <div class="table-wrap" style="margin-bottom: 20px; max-height: 250px; overflow-y: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Mapel Utama</th>
                                <th></th>
                                <th>Mapel Pasangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(pair, index) in mapelPairs" :key="index">
                                <tr>
                                    <td style="font-weight:600;" x-text="pair.mapel1"></td>
                                    <td style="color: var(--gray-400); text-align: center;"><i
                                            class="fas fa-arrows-alt-h"></i></td>
                                    <td style="font-weight:600; color: var(--blue);" x-text="pair.mapel2"></td>
                                    <td>
                                        <button @click="removePair(index)" class="btn-small outline"
                                            style="border-color: var(--red); color: var(--red); padding: 2px 8px; font-size: 11px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div style="border-top: 1px solid var(--border-light); padding-top: 16px;">
                    <h4 style="font-size: 12px; font-weight: 700; margin-bottom: 8px;">Tambah Hubungan Baru</h4>
                    <div style="display: flex; gap: 8px;">
                        <div class="input-wrap"
                            style="flex:1; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 6px 12px;">
                            <input type="text" placeholder="Contoh: IPS" x-model="newPair1"
                                style="border:none; outline:none; width: 100%; font-size:12px;">
                        </div>
                        <div style="align-self: center; color: var(--gray-400);"><i class="fas fa-link"></i></div>
                        <div class="input-wrap"
                            style="flex:1; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 6px 12px;">
                            <input type="text" placeholder="Contoh: Social Studies" x-model="newPair2"
                                style="border:none; outline:none; width: 100%; font-size:12px;">
                        </div>
                        <button @click="addPair()" class="btn-primary btn-small"
                            style="padding: 8px 12px; border-radius: var(--radius-sm);">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            style="position: fixed; bottom: 24px; right: 24px; background: var(--teal); color: white; padding: 12px 24px; border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 9999; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> <span x-text="toastMsg"></span>
        </div>
    </div>

    <div x-show="tab === 'asesmen'" x-data="{
        showToast: false,
        toastMsg: '',
        isGenerating: false,
        examAssembled: false,
        selectedMapel: 'Matematika',
        selectedKelas: '7A',
        soalCount: 40,
        compEasy: 30,
        compMedium: 40,
        compHard: 20,
        compOlim: 10,
        soalList: [],
        anomalies: window.anomaliesData,
        generateExam() {
            this.isGenerating = true;
            this.examAssembled = false;
            setTimeout(() => {
                this.isGenerating = false;
                this.examAssembled = true;
                this.soalList = window.soalListData;
                this.toastMsg = 'Ujian Otomatis Berhasil Dirakit!';
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            }, 1500);
        },
        swapScores(index) {
            let a = this.anomalies[index];
            let temp = a.biasa;
            a.biasa = a.unggulan;
            a.unggulan = temp;
            a.status = 'Fixed';
            this.toastMsg = 'Nilai berhasil ditukar kembali untuk ' + a.nama;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        }
    }">
        <div class="content-header">
            <div>
                <h1>Asesmen &amp; Bank Soal</h1>
                <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Rakit Ujian Otomatis &amp; Cek Nilai
                    Tertukar (Audit Nilai)</p>
            </div>
            <div class="header-right">
                <div class="avatar teal">KS</div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-cog" style="color:var(--teal)"></i> Perakitan Ujian Otomatis</h3>
                </div>

                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div class="grid-2">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size: 11px; font-weight:700;">Mata Pelajaran</label>
                            <select x-model="selectedMapel" class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:6px; font-family:var(--font); outline:none;">
                                <option>Matematika</option>
                                <option>IPA</option>
                                <option>Bahasa Indonesia</option>
                                <option>Bahasa Inggris</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size: 11px; font-weight:700;">Kelas Target</label>
                            <select x-model="selectedKelas" class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:6px; font-family:var(--font); outline:none;">
                                <option>7A</option>
                                <option>7B</option>
                                <option>8A</option>
                                <option>9A</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size: 11px; font-weight:700;">Jumlah Soal</label>
                        <div class="input-wrap"
                            style="border: 1px solid var(--border-light); border-radius: 4px; padding: 6px 12px;">
                            <input type="number" x-model.number="soalCount"
                                style="border:none; outline:none; width:100%; font-size:13px; font-family:var(--font);">
                        </div>
                    </div>

                    <div
                        style="background:var(--gray-50); padding:12px; border-radius:var(--radius-sm); border: 1px solid var(--border-light); display:flex; flex-direction:column; gap:8px;">
                        <span style="font-size:11px; font-weight:700; color:var(--gray-600);">Komposisi Soal (%)</span>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div>
                                <span style="font-size:11px; color:var(--gray-500);">Gampang</span>
                                <input type="number" x-model.number="compEasy" class="form-select"
                                    style="width:100%; border:1px solid var(--border-light); padding:4px; font-size:12px; font-family:var(--font); outline:none; border-radius:4px;">
                            </div>
                            <div>
                                <span style="font-size:11px; color:var(--gray-500);">Sedang</span>
                                <input type="number" x-model.number="compMedium" class="form-select"
                                    style="width:100%; border:1px solid var(--border-light); padding:4px; font-size:12px; font-family:var(--font); outline:none; border-radius:4px;">
                            </div>
                            <div>
                                <span style="font-size:11px; color:var(--gray-500);">Susah</span>
                                <input type="number" x-model.number="compHard" class="form-select"
                                    style="width:100%; border:1px solid var(--border-light); padding:4px; font-size:12px; font-family:var(--font); outline:none; border-radius:4px;">
                            </div>
                            <div>
                                <span style="font-size:11px; color:var(--gray-500);">Olimpiade</span>
                                <input type="number" x-model.number="compOlim" class="form-select"
                                    style="width:100%; border:1px solid var(--border-light); padding:4px; font-size:12px; font-family:var(--font); outline:none; border-radius:4px;">
                            </div>
                        </div>
                    </div>

                    <button @click="generateExam()" class="btn-primary btn-small"
                        style="padding: 10px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; border-radius:8px;"
                        :disabled="isGenerating">
                        <i class="fas fa-spinner fa-spin" x-show="isGenerating" style="display:none;"></i>
                        <i class="fas fa-magic" x-show="!isGenerating"></i>
                        <span x-text="isGenerating ? 'Merakit Soal...' : 'Rakit Ujian Otomatis'"></span>
                    </button>
                </div>

                <div x-show="examAssembled"
                    style="margin-top:20px; border-top:1.5px dashed var(--border); padding-top:16px;" x-transition>
                    <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--teal);"><i
                            class="fas fa-file-alt"></i> Preview Lembar Ujian Terakit</h4>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <template x-for="soal in soalList" :key="soal.no">
                            <div
                                style="background:var(--white); border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding:8px 12px; display:flex; gap:10px;">
                                <span style="font-weight:700; color:var(--teal);" x-text="soal.no"></span>
                                <div style="flex:1;">
                                    <p style="font-size:12px; font-weight:500;" x-text="soal.teks"></p>
                                    <span class="badge"
                                        :class="{ 'teal': soal.tipe==='Gampang', 'blue': soal.tipe==='Sedang', 'orange': soal.tipe==='Susah', 'red': soal.tipe==='Olimpiade' }"
                                        style="font-size:9px; padding:2px 6px;" x-text="soal.tipe"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle" style="color:var(--orange)"></i> Cek Nilai Tertukar
                        (Audit
                        Nilai)</h3>
                </div>

                <div
                    style="background: var(--orange-bg); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-light); margin-bottom: 16px;">
                    <p style="font-size: 12px; color: var(--gray-600); line-height: 1.4;">
                        Algoritma mendeteksi anomali entri di mana nilai harian biasa dan nilai unggulan terbalik pada kolom
                        nilai guru.
                    </p>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Mapel</th>
                                <th>Biasa</th>
                                <th>Unggulan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(anomali, index) in anomalies" :key="index">
                                <tr>
                                    <td>
                                        <div style="font-weight:600;" x-text="anomali.nama"></div>
                                        <small style="color:var(--gray-400);"
                                            x-text="'Kelas: ' + anomali.kelas"></small>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;" x-text="anomali.mapel"></div>
                                        <small style="color:var(--gray-400);" x-text="'Guru: ' + anomali.guru"></small>
                                    </td>
                                    <td>
                                        <span x-text="anomali.biasa"
                                            :style="anomali.status === 'Flagged' ? 'color: var(--red); font-weight:700;' :
                                                'color: var(--text);'"></span>
                                    </td>
                                    <td>
                                        <span x-text="anomali.unggulan"
                                            :style="anomali.status === 'Flagged' ? 'color: var(--teal); font-weight:700;' :
                                                'color: var(--text);'"></span>
                                    </td>
                                    <td>
                                        <span class="badge" :class="anomali.status === 'Flagged' ? 'red' : 'green'"
                                            style="font-size:10px;" x-text="anomali.status"></span>
                                    </td>
                                    <td>
                                        <template x-if="anomali.status === 'Flagged'">
                                            <button @click="swapScores(index)" class="btn-small teal"
                                                style="padding: 4px 8px; font-size:11px;">
                                                <i class="fas fa-sync-alt"></i> Tukar Kembali
                                            </button>
                                        </template>
                                        <template x-if="anomali.status === 'Fixed'">
                                            <span style="color: var(--green); font-size: 11px; font-weight: 600;"><i
                                                    class="fas fa-check"></i> Selesai</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            style="position: fixed; bottom: 24px; right: 24px; background: var(--teal); color: white; padding: 12px 24px; border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 9999; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> <span x-text="toastMsg"></span>
        </div>
    </div>

    @php
        $guruReportsData = $guruReportsData ?? [];

        if (empty($guruReportsData)) {
            $teachersList = \App\Models\Guru::with('mapels')->get();

            foreach ($teachersList as $g) {
                $today = now()->format('Y-m-d');
                $startOfWeek = now()->startOfWeek()->format('Y-m-d');
                $endOfWeek = now()->endOfWeek()->format('Y-m-d');
                $startOfMonth = now()->startOfMonth()->format('Y-m-d');
                $endOfMonth = now()->endOfMonth()->format('Y-m-d');

                $hasHarian = \App\Models\LaporanMengajar::where('guru_id', $g->id)
                    ->where('tipe', 'harian')
                    ->where('tanggal', $today)
                    ->exists();

                $hasMingguan = \App\Models\LaporanMengajar::where('guru_id', $g->id)
                    ->where('tipe', 'mingguan')
                    ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                    ->exists();

                $hasBulanan = \App\Models\LaporanMengajar::where('guru_id', $g->id)
                    ->where('tipe', 'bulanan')
                    ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->exists();

                $allReports = \App\Models\LaporanMengajar::where('guru_id', $g->id)
                    ->orderBy('tanggal', 'desc')
                    ->get()
                    ->map(function ($l) {
                        $isiTeks = '';
                        if (is_array($l->isi)) {
                            if ($l->tipe === 'harian') {
                                $kList = [];
                                if (!empty($l->isi['checklists'])) {
                                    $tidakList = [];
                                    foreach ($l->isi['checklists'] as $key => $chk) {
                                        if (($chk['status'] ?? '') === 'tidak') {
                                            $tidakList[] = $key;
                                        }
                                    }
                                    if (!empty($tidakList)) {
                                        $kList[] = "• Indikator 'Tidak': " . implode(', ', $tidakList);
                                    }
                                }
                                if (!empty($l->isi['kendala'])) {
                                    foreach ($l->isi['kendala'] as $k) {
                                        $kList[] = "• [Kendala {$k['bidang']}] {$k['deskripsi']}";
                                    }
                                }
                                if (!empty($l->isi['pemetaan_masalah_siswa'])) {
                                    foreach ($l->isi['pemetaan_masalah_siswa'] as $s) {
                                        $kList[] = "• [Siswa {$s['nama_siswa']}] {$s['permasalahan']}";
                                    }
                                }
                                if (!empty($l->isi['refleksi']['kendala_terbesar'])) {
                                    $kList[] = "• [Refleksi] Kendala: {$l->isi['refleksi']['kendala_terbesar']}";
                                }

                                if (empty($kList)) {
                                    $kList[] = 'KBM Terlaksana Baik (Tidak ada kendala)';
                                }

                                if (!empty($l->isi['catatan_umum'])) {
                                    $kList[] = 'Catatan: ' . $l->isi['catatan_umum'];
                                }
                                $isiTeks = implode("\n", $kList);
                            } elseif ($l->tipe === 'mingguan') {
                                $mList = [];
                                if (!empty($l->isi['rekap_pembelajaran'])) {
                                    foreach ($l->isi['rekap_pembelajaran'] as $r) {
                                        if (!empty($r['materi'])) {
                                            $mList[] = "• {$r['hari']}: {$r['materi']} (HOTS: {$r['hots']})";
                                        }
                                    }
                                }
                                if (!empty($l->isi['tindak_lanjut'])) {
                                    foreach ($l->isi['tindak_lanjut'] as $t) {
                                        $mList[] = "• [Tindak Lanjut] {$t['program']} untuk {$t['sasaran']}";
                                    }
                                }
                                if (!empty($l->isi['catatan_umum'])) {
                                    $mList[] = 'Catatan: ' . $l->isi['catatan_umum'];
                                }
                                $isiTeks = implode("\n", $mList);
                            } else {
                                $bList = [];
                                if (!empty($l->isi['capaian_belajar_bulanan'])) {
                                    foreach ($l->isi['capaian_belajar_bulanan'] as $c) {
                                        if (!empty($c['keterangan']) || !empty($c['capaian'])) {
                                            $bList[] = "• CP {$c['elemen_cp']}: Capaian {$c['capaian']} (Target: {$c['target']})";
                                        }
                                    }
                                }
                                if (!empty($l->isi['catatan_umum'])) {
                                    $bList[] = 'Catatan: ' . $l->isi['catatan_umum'];
                                }
                                $isiTeks = implode("\n", $bList);
                            }
                        } else {
                            $isiTeks = $l->isi;
                        }

                        return [
                            'id' => $l->id,
                            'tipe' => ucfirst($l->tipe),
                            'tanggal' => $l->tanggal->format('d M Y'),
                            'isi' => $isiTeks,
                        ];
                    });

                $classNames =
                    \App\Models\Jadwal::where('guru_id', $g->id)
                        ->with('kelas')
                        ->get()
                        ->pluck('kelas.nama_kelas')
                        ->unique()
                        ->implode(', ') ?:
                    '—';

                $guruReportsData[] = [
                    'id' => $g->id,
                    'nama' => $g->nama,
                    'mapel' => $g->mapel->nama_mapel ?? '—',
                    'kelas' => $classNames,
                    'harian' => $hasHarian ? 'Lengkap' : 'Belum Isi',
                    'mingguan' => $hasMingguan ? 'Lengkap' : 'Belum Isi',
                    'bulanan' => $hasBulanan ? 'Lengkap' : 'Belum Isi',
                    'reports' => $allReports,
                ];
            }
        }

        $materiListAdmin = \App\Models\Materi::with('guru', 'mapel', 'kelas')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'guru' => $m->guru->nama ?? '—',
                    'mapel' =>
                        ($m->mapel->nama_mapel ?? '—') . ($m->kelas ? ' ' . $m->kelas->nama_kelas : ' (Semua Kelas)'),
                    'judul' => $m->judul,
                    'status' => ucfirst(
                        $m->status === 'approved' ? 'Approved' : ($m->status === 'rejected' ? 'Rejected' : 'Pending'),
                    ),
                    'file_path' => $m->file_path,
                ];
            });
    @endphp
    <script>
        window.guruReportsData = @json($guruReportsData ?? []);
        window.materiAjarData = @json($materiAjarData ?? []);
    </script>
    <div x-show="tab === 'audit_guru'" x-data="{
        showAuditModal: false,
        selectedMateriId: null,
        showToast: false,
        toastMsg: '',
        guruReports: window.guruReportsData,
        selectedGuru: null,
        materiAjar: window.materiAjarData,
        sendReminder(nama) {
            this.toastMsg = 'Peringatan terkirim ke ' + nama + '!';
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        },
        approveMateri(id) {
            this.selectedMateriId = id;
            this.$refs.auditModal.showModal();
        },
        rejectMateri(id) {
            let form = document.getElementById('reject-materi-form');
            form.action = '/admin/materi/' + id + '/reject';
            form.submit();
        },
        calculatePerformance(guruName) {
            let rep = this.guruReports.find(x => x.nama === guruName);
            let mat = this.materiAjar.filter(x => x.guru === guruName);
    
            let score = 70; // Base score
            if (rep) {
                if (rep.harian === 'Lengkap') score += 10;
                if (rep.mingguan === 'Lengkap') score += 10;
                if (rep.bulanan === 'Lengkap') score += 10;
                if (rep.harian === 'Terlambat') score += 5;
            }
            let approvedCount = mat.filter(x => x.status === 'Approved').length;
            score += approvedCount * 5;
            return Math.min(score, 100);
        }
    }">
        <div class="content-header">
            <div>
                <h1>Audit &amp; Kinerja Guru</h1>
                <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Cek Kelengkapan Jurnal Mengajar, Approval
                    Modul, dan Rapor Kinerja</p>
            </div>
            <div class="header-right">
                <div class="avatar teal">KS</div>
            </div>
        </div>

        <div class="grid-2" style="margin-bottom: 24px;">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list" style="color:var(--teal)"></i> Kelengkapan Laporan Guru</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Guru</th>
                                <th>Harian</th>
                                <th>Mingguan</th>
                                <th>Bulanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(rep, index) in guruReports" :key="index">
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;" x-text="rep.nama"></div>
                                        <small style="color:var(--gray-400);"
                                            x-text="rep.mapel + ' - ' + rep.kelas"></small>
                                    </td>
                                    <td>
                                        <span class="badge"
                                            :class="{ 'green': rep.harian==='Lengkap', 'orange': rep.harian==='Terlambat', 'red': rep.harian==='Belum Isi' }"
                                            x-text="rep.harian"></span>
                                    </td>
                                    <td>
                                        <span class="badge"
                                            :class="{ 'green': rep.mingguan==='Lengkap', 'orange': rep.mingguan==='Terlambat', 'red': rep.mingguan==='Belum Isi' }"
                                            x-text="rep.mingguan"></span>
                                    </td>
                                    <td>
                                        <span class="badge"
                                            :class="{ 'green': rep.bulanan==='Lengkap', 'orange': rep.bulanan==='Terlambat', 'red': rep.bulanan==='Belum Isi' }"
                                            x-text="rep.bulanan"></span>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:6px;align-items:center">
                                            <button @click="selectedGuru = rep" class="btn-small outline"
                                                style="border-color:var(--blue); color:var(--blue); padding:4px 8px; font-size:11px; cursor:pointer">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <template
                                                x-if="rep.harian === 'Belum Isi' || rep.mingguan === 'Belum Isi' || rep.bulanan === 'Belum Isi' || rep.harian === 'Terlambat'">
                                                <button @click="sendReminder(rep.nama)" class="btn-small outline"
                                                    style="border-color:var(--red); color:var(--red); padding:4px 8px; font-size:11px; cursor:pointer">
                                                    <i class="fas fa-bell"></i> Hubungi
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-check-double" style="color:var(--blue)"></i> Approve Materi Ajar Guru</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Materi / Modul</th>
                                <th>Guru</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(mat, index) in materiAjar" :key="index">
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">
                                            <a :href="'/materi/' + mat.id + '/download'" target="_blank"
                                                x-text="mat.judul"
                                                style="text-decoration:none;color:var(--text);font-weight:600"></a>
                                        </div>
                                        <div style="font-size: 10px; color: var(--gray-400); margin-top: 2px;">
                                            <i class="fas fa-info-circle"></i> Klik nama materi untuk mengunduh
                                        </div>
                                        <small style="color: var(--blue);" x-text="mat.mapel"></small>
                                    </td>
                                    <td style="font-weight:500;" x-text="mat.guru"></td>
                                    <td>
                                        <span class="badge"
                                            :class="{ 'green': mat.status==='Approved', 'red': mat.status==='Rejected', 'orange': mat.status==='Pending' }"
                                            x-text="mat.status"></span>
                                    </td>
                                    <td>
                                        <template x-if="mat.status === 'Pending'">
                                            <div style="display:flex; gap:4px;">
                                                <button @click="approveMateri(mat.id)" class="btn-small teal"
                                                    style="padding: 2px 6px; font-size:11px;"><i
                                                        class="fas fa-check"></i></button>
                                                <button @click="rejectMateri(mat.id)" class="btn-small outline"
                                                    style="border-color: var(--red); color: var(--red); padding: 2px 6px; font-size:11px;"><i
                                                        class="fas fa-times"></i></button>
                                            </div>
                                        </template>
                                        <template x-if="mat.status !== 'Pending'">
                                            <span
                                                style="font-size: 11px; color: var(--gray-400); font-weight: 500;">Selesai
                                                di-review</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <style>
            dialog::backdrop {
                background: rgba(0, 0, 0, 0.5);
            }
        </style>
        <dialog x-ref="auditModal"
            style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; border:none; border-radius:8px; padding:20px; width:400px; box-shadow:0 4px 12px rgba(0,0,0,0.3); z-index: 999999;">
            <h3 style="margin-top:0; font-size:18px">Compliance Audit Modul Ajar</h3>
            <p style="font-size:12px; color:var(--gray-500); margin-bottom:15px;">Berikan skor 1 sampai 5 untuk setiap
                indikator mutu sesuai Kurikulum AFUSCHO.</p>

            <form :action="'/admin/materi/' + selectedMateriId + '/approve'" method="POST">
                @csrf
                <div style="margin-bottom: 10px;">
                    <label style="font-size:12px; font-weight:600">Integrasi Ayat Kauniyah (1-5)</label>
                    <input type="number" name="skor_kauniyah" min="1" max="5" required
                        style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="font-size:12px; font-weight:600">Glosarium Bilingual (1-5)</label>
                    <input type="number" name="skor_bilingual" min="1" max="5" required
                        style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size:12px; font-weight:600">Kompatibilitas AI (1-5)</label>
                    <input type="number" name="skor_ai" min="1" max="5" required
                        style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px">
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" @click="$refs.auditModal.close()" class="btn-small outline">Batal</button>
                    <button type="submit" class="btn-small teal">Simpan & ACC</button>
                </div>
            </form>
        </dialog>


        <div x-show="selectedGuru" class="card" style="margin-bottom: 24px; border-left: 4px solid var(--blue)"
            x-transition>
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
                <h3><i class="fas fa-file-alt" style="color:var(--blue)"></i> Detail Catatan Laporan: <span
                        x-text="selectedGuru ? selectedGuru.nama : ''"></span></h3>
                <button @click="selectedGuru = null" class="btn-small outline"
                    style="border-color:var(--gray-400); color:var(--gray-500); cursor:pointer"><i
                        class="fas fa-times"></i> Tutup</button>
            </div>
            <div class="table-wrap" style="margin-top:10px">
                <table style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:15%">Tanggal</th>
                            <th style="width:15%">Tipe Laporan</th>
                            <th style="width:70%">Isi Catatan Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="selectedGuru && selectedGuru.reports.length === 0">
                            <tr>
                                <td colspan="3" style="text-align:center;color:var(--gray-400);padding:20px">Guru ini
                                    belum pernah mengirim laporan mengajar.</td>
                            </tr>
                        </template>
                        <template x-for="(rep, rIndex) in (selectedGuru ? selectedGuru.reports : [])"
                            :key="rIndex">
                            <tr>
                                <td><strong x-text="rep.tanggal"></strong></td>
                                <td>
                                    <span class="badge light"
                                        :class="{ 'blue': rep.tipe==='Harian', 'green': rep.tipe==='Mingguan', 'purple': rep.tipe==='Bulanan' }"
                                        x-text="rep.tipe"></span>
                                </td>
                                <td style="font-size:13px;line-height:1.5;white-space:pre-line" x-text="rep.isi"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-trophy" style="color:var(--orange)"></i> Rapor Skor Kinerja Guru</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Guru</th>
                            <th>Mata Pelajaran</th>
                            <th style="width: 50%;">Skor Indeks Kinerja</th>
                            <th>Nilai</th>
                            <th>Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(rep, index) in guruReports" :key="index">
                            <tr>
                                <td style="font-weight:700;" x-text="rep.nama"></td>
                                <td style="color:var(--gray-500);" x-text="rep.mapel"></td>
                                <td>
                                    <div class="progress-wrap" style="margin:0;">
                                        <div class="progress-bar" style="height:10px;">
                                            <div class="fill"
                                                :style="'width: ' + calculatePerformance(rep.nama) + '%;'"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color:var(--teal); font-size:15px;"
                                        x-text="calculatePerformance(rep.nama)"></strong>
                                </td>
                                <td>
                                    <span class="badge"
                                        :class="{
                                            'green': calculatePerformance(rep.nama) >= 90,
                                            'blue': calculatePerformance(
                                                    rep.nama) >= 80 && calculatePerformance(rep.nama) <
                                                90,
                                            'orange': calculatePerformance(rep.nama) < 80
                                        }"
                                        style="font-size: 10px;"
                                        x-text="calculatePerformance(rep.nama) >= 90 ? 'Sangat Baik' : (calculatePerformance(rep.nama) >= 80 ? 'Baik' : 'Cukup')"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            style="position: fixed; bottom: 24px; right: 24px; background: var(--teal); color: white; padding: 12px 24px; border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 9999; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> <span x-text="toastMsg"></span>
        </div>

        <form id="approve-materi-form" method="POST" style="display:none">
            @csrf
        </form>
        <form id="reject-materi-form" method="POST" style="display:none">
            @csrf
        </form>
    </div>

    <div x-show="tab === 'karya_tahfidz'" x-data="{
        showToast: false,
        toastMsg: '',
        searchSiswa: '',
        sidangList: window.sidangListData || [],
        progressTargetMode: 'siswa',
        newSiswa: '',
        newKelas: '9A',
        newJudul: '',
        newTanggal: '',
        newWaktu: '',
        newPenguji: 'Pak Budi Santoso',
        showForm: false,
        addSidang() {
            if (!this.newSiswa || !this.newJudul || !this.newTanggal || !this.newWaktu) return;
            this.sidangList.push({
                siswa: this.newSiswa,
                kelas: this.newKelas,
                judul: this.newJudul,
                tanggal: this.newTanggal,
                waktu: this.newWaktu,
                penguji: this.newPenguji,
                status: 'Terjadwal'
            });
            this.newSiswa = '';
            this.newJudul = '';
            this.newTanggal = '';
            this.newWaktu = '';
            this.showForm = false;
            this.toastMsg = 'Jadwal sidang berhasil ditambahkan!';
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        }
    }">
        <div class="content-header">
            <div>
                <h1>Karya Tulis &amp; Tahfidz</h1>
                <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Jadwalkan Sidang Karya Tulis Ilmiah Kelas 9
                    &amp; Rekap Hafalan Quran Siswa</p>
            </div>
            <div class="header-right">
                <div class="avatar teal">KS</div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-file-signature" style="color:var(--teal)"></i> Jadwal Sidang Karya Tulis (Kelas
                        9)</h3>
                    <button @click="showForm = !showForm" class="btn-small teal"
                        style="padding:4px 8px; font-size:11px;">
                        <i class="fas" :class="showForm ? 'fa-times' : 'fa-plus'"></i> <span
                            x-text="showForm ? 'Batal' : 'Tambah'"></span>
                    </button>
                </div>

                <div x-show="showForm"
                    style="background:var(--gray-50); border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding:16px; margin-bottom: 16px;"
                    x-transition>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <div class="grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:11px; font-weight:700;">Nama Siswa</label>
                                <div class="input-wrap"
                                    style="border: 1px solid var(--border-light); border-radius: 4px; padding: 4px 8px;">
                                    <input type="text" x-model="newSiswa" placeholder="Ahmad Rizky"
                                        style="border:none; outline:none; width:100%; font-size:12px; font-family:var(--font);">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:11px; font-weight:700;">Kelas</label>
                                <select x-model="newKelas" class="form-select"
                                    style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:4px; font-family:var(--font); outline:none;">
                                    <option>9A</option>
                                    <option>9B</option>
                                    <option>9C</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Judul Karya Tulis</label>
                            <div class="input-wrap"
                                style="border: 1px solid var(--border-light); border-radius: 4px; padding: 4px 8px;">
                                <input type="text" x-model="newJudul" placeholder="Analisis..."
                                    style="border:none; outline:none; width:100%; font-size:12px; font-family:var(--font);">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:11px; font-weight:700;">Tanggal</label>
                                <div class="input-wrap"
                                    style="border: 1px solid var(--border-light); border-radius: 4px; padding: 4px 8px;">
                                    <input type="date" x-model="newTanggal"
                                        style="border:none; outline:none; width:100%; font-size:12px; font-family:var(--font);">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:11px; font-weight:700;">Waktu</label>
                                <div class="input-wrap"
                                    style="border: 1px solid var(--border-light); border-radius: 4px; padding: 4px 8px;">
                                    <input type="time" x-model="newWaktu"
                                        style="border:none; outline:none; width:100%; font-size:12px; font-family:var(--font);">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Dosen Penguji / Guru</label>
                            <select x-model="newPenguji" class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:4px; font-family:var(--font); outline:none;">
                                <option>Pak Budi Santoso</option>
                                <option>Ustadz Ahmad Fauzi</option>
                                <option>Bu Dewi Sartika</option>
                                <option>Ibu Siti Rahmawati</option>
                            </select>
                        </div>
                        <button @click="addSidang()" class="btn-primary btn-small"
                            style="align-self: flex-start; padding: 8px 16px; border-radius:6px;">
                            <i class="fas fa-save"></i> Jadwalkan
                        </button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Judul Karya Tulis</th>
                                <th>Tanggal &amp; Jam</th>
                                <th>Penguji</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(sidang, index) in sidangList" :key="index">
                                <tr>
                                    <td>
                                        <div style="font-weight:600;" x-text="sidang.siswa"></div>
                                        <small style="color:var(--gray-400);" x-text="'Kelas: ' + sidang.kelas"></small>
                                    </td>
                                    <td>
                                        <div style="font-size:12px; font-weight:500; line-height:1.3;"
                                            x-text="sidang.judul"></div>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;" x-text="sidang.tanggal"></div>
                                        <small style="color:var(--teal);" x-text="sidang.waktu + ' WIB'"></small>
                                    </td>
                                    <td style="font-weight:500; font-size:12px;" x-text="sidang.penguji"></td>
                                    <td>
                                        <span class="badge"
                                            :class="{ 'green': sidang.status==='Terjadwal', 'orange': sidang.status==='Draft' }"
                                            x-text="sidang.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-quran" style="color:var(--blue)"></i> Progress Tahfidz Siswa</h3>
                    <div class="input-wrap"
                        style="border: 1px solid var(--border-light); border-radius: 20px; padding: 2px 10px; width: 140px; display: flex; align-items: center;">
                        <input type="text" placeholder="Cari Siswa..." x-model="searchSiswa"
                            style="border:none; outline:none; width: 100%; font-size: 11px; font-family:var(--font);">
                    </div>
                </div>

                <form method="POST" action="{{ route('kepala.tahfidz-progress.store') }}"
                    style="background:var(--gray-50); border:1px solid var(--border-light); border-radius:var(--radius-sm); padding:14px; margin-bottom:16px;">
                    @csrf
                    <div class="grid-2" style="gap:10px; margin-bottom:10px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Target Update</label>
                            <select name="target_mode" x-model="progressTargetMode" class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                                <option value="siswa">Per Siswa</option>
                                <option value="kelas">Per Kelas Umum</option>
                                <option value="kelas_quran">Per Kelas Tahfidz/Quran</option>
                                <option value="semua">Semua Siswa</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;" x-show="progressTargetMode === 'siswa'">
                            <label style="font-size:11px; font-weight:700;">Siswa</label>
                            <select name="siswa_id" class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                                <option value="">Pilih siswa</option>
                                @foreach($tahfidzSiswaOptions ?? [] as $opsiSiswa)
                                    <option value="{{ $opsiSiswa->id }}">{{ $opsiSiswa->nama }} ({{ $opsiSiswa->kelas->nama_kelas ?? '-' }} / {{ $opsiSiswa->kelasQuran->nama_kelas ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;" x-show="progressTargetMode === 'kelas'">
                            <label style="font-size:11px; font-weight:700;">Kelas Umum</label>
                            <select name="kelas_id" class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                                <option value="">Pilih kelas umum</option>
                                @foreach($tahfidzKelasOptions ?? [] as $opsiKelas)
                                    <option value="{{ $opsiKelas->id }}">{{ $opsiKelas->nama_kelas }} ({{ $opsiKelas->siswa_count ?? 0 }} siswa)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;" x-show="progressTargetMode === 'kelas_quran'">
                            <label style="font-size:11px; font-weight:700;">Kelas Tahfidz/Quran</label>
                            <select name="kelas_quran_id" class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                                <option value="">Pilih kelas Quran</option>
                                @foreach($tahfidzKelasQuranOptions ?? [] as $opsiKelasQuran)
                                    <option value="{{ $opsiKelasQuran->id }}">{{ $opsiKelasQuran->nama_kelas }} - {{ $opsiKelasQuran->kategori }} {{ $opsiKelasQuran->tingkat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;" x-show="progressTargetMode === 'semua'">
                            <label style="font-size:11px; font-weight:700;">Cakupan</label>
                            <div style="border:1px solid var(--border-light); border-radius:4px; padding:8px; font-size:12px; color:var(--gray-500); background:white;">
                                Semua siswa aktif akan menerima progress yang sama.
                            </div>
                        </div>
                    </div>

                    <div class="grid-2" style="gap:10px; margin-bottom:10px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Surah Terakhir</label>
                            <input type="text" name="surah" placeholder="Contoh: Al-Baqarah"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Ayat</label>
                            <div style="display:flex; gap:6px;">
                                <input type="number" name="ayat_mulai" min="1" placeholder="Dari"
                                    style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                                <input type="number" name="ayat_selesai" min="1" placeholder="Sampai"
                                    style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                            </div>
                        </div>
                    </div>

                    <div class="grid-2" style="gap:10px; margin-bottom:10px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Juz Dihafal</label>
                            <input type="number" name="juz_dihafal" min="0" max="30" placeholder="0-30"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Progress (%)</label>
                            <input type="number" name="progress_percent" min="0" max="100" step="0.01" required
                                placeholder="0-100"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                        </div>
                    </div>

                    <div class="grid-2" style="gap:10px; margin-bottom:10px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Target</label>
                            <input type="text" name="target_deskripsi" placeholder="Contoh: Juz 30 selesai"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700;">Status</label>
                            <select name="status" required class="form-select"
                                style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font);">
                                <option value="berproses">Berproses</option>
                                <option value="lancar">Lancar</option>
                                <option value="perlu_murojaah">Perlu Murojaah</option>
                                <option value="belum_mulai">Belum Mulai</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:10px;">
                        <label style="font-size:11px; font-weight:700;">Catatan Kepala Sekolah</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan singkat..."
                            style="width:100%; border:1px solid var(--border-light); border-radius:4px; padding:7px; font-size:12px; font-family:var(--font); resize:vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn-small teal" style="border:none; padding:8px 14px; cursor:pointer;">
                        <i class="fas fa-save"></i> Simpan Progress
                    </button>
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Hafalan Terakhir</th>
                                <th>Target</th>
                                <th>Progress Juz</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahfidzProgressRows ?? [] as $progressSiswa)
                                @php
                                    $progress = $progressSiswa->tahfidzProgress;
                                    $progressPercent = $progress ? (float) $progress->progress_percent : 0;
                                    $statusLabel = [
                                        'belum_mulai' => 'Belum Mulai',
                                        'berproses' => 'Berproses',
                                        'lancar' => 'Lancar',
                                        'perlu_murojaah' => 'Perlu Murojaah',
                                    ][$progress->status ?? 'belum_mulai'];
                                    $searchName = mb_strtolower($progressSiswa->nama);
                                @endphp
                                <tr data-search="{{ $searchName }}" x-show="searchSiswa === '' || $el.dataset.search.includes(searchSiswa.toLowerCase())">
                                    <td>
                                        <div style="font-weight: 600;">{{ $progressSiswa->nama }}</div>
                                        <small style="color:var(--gray-400);">Kelas: {{ $progressSiswa->kelas->nama_kelas ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div style="font-weight:600; color:var(--teal);">Q.S. {{ $progress->surah ?? '-' }}</div>
                                        <small style="color:var(--gray-500);">Ayat: {{ $progress?->ayat_mulai && $progress?->ayat_selesai ? $progress->ayat_mulai . '-' . $progress->ayat_selesai : '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge light blue">{{ $progress->target_deskripsi ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="progress-wrap" style="margin:0;">
                                            <div class="progress-label" style="font-size:10px; margin-bottom:2px;">
                                                <span class="percentage">{{ number_format($progressPercent, 1) }}%</span>
                                                <span>{{ (int) ($progress->juz_dihafal ?? 0) }}/30 Juz</span>
                                            </div>
                                            <div class="progress-bar" style="height:6px; width:100px;">
                                                <div class="fill" style="width: {{ min(100, max(0, $progressPercent)) }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ ($progress->status ?? '') === 'lancar' ? 'green' : (($progress->status ?? '') === 'perlu_murojaah' ? 'orange' : 'light blue') }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada data siswa</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="showToast" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            style="position: fixed; bottom: 24px; right: 24px; background: var(--teal); color: white; padding: 12px 24px; border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 9999; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> <span x-text="toastMsg"></span>
        </div>
    </div>

    <div x-show="tab === 'cbt'" x-data="{ selectedExam: null }">
        @include('dashboard.admin-sections.cbt-approval')
    </div>

    <div x-show="tab === 'pengaturan'">
        <div>
            <div class="content-header">
                <h1>Pengaturan</h1>
                <div class="header-right">
                    <div class="avatar teal">KS</div>
                </div>
            </div>
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-cog" style="color:var(--teal)"></i> Pengaturan Umum</h3>
                    </div>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px">Nama Sekolah: SMPIT Al
                        Azhar Jaya Indonesia</p>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px">Tahun Ajaran: 2025/2026</p>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px">Semester: Genap</p>
                    <p style="font-size:13px;color:var(--gray-500)">Alamat: Jl. Sirih Prada No. 135, Pabuaran, Kec.
                        Mustika
                        Jaya, Kota Bekasi</p>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-palette" style="color:var(--purple)"></i> Tampilan</h3>
                    </div>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px">Tema: Default (Hijau &amp; Biru)
                    </p>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px">Bahasa: Indonesia</p>
                    <p style="font-size:13px;color:var(--gray-500)">Zona Waktu: WIB (UTC+7)</p>
                </div>
            </div>
        </div>
    </div>

    <div x-show="tab === 'rapor_supervisi'">
        @include('dashboard.kepala-sekolah-sections.rapor_supervisi')
    </div>

    <div x-show="tab === 'bahan_ajar_approval'">
        @include('dashboard.kepala-sekolah-sections.bahan-ajar-approval')
    </div>

@endsection
