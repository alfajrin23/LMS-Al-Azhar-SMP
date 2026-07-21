<style>
    /* Stats & Indicator Styles */
    .indicator-card {
        background: var(--gray-50);
        padding: 16px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-light);
        margin-bottom: 16px;
    }

    .indicator-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .indicator-value-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 10px;
    }

    .indicator-score {
        font-size: 28px;
        font-weight: 800;
        color: var(--teal);
    }

    .indicator-scale {
        font-size: 12px;
        color: var(--gray-400);
    }

    .indicator-bar-track {
        height: 8px;
        background: var(--gray-200);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .indicator-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease-in-out;
    }

    .indicator-desc {
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="content-header">
    <div>
        <h1>Kondisi Kelas (Hasil Survei Siswa)</h1>
        <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Pemantauan iklim belajar berdasarkan
            masukan/survei harian siswa</p>
    </div>
    <div class="header-right">
        <div class="avatar blue">{{ strtoupper(substr($guru->nama, 0, 1)) }}</div>
    </div>
</div>

@if (session('success'))
    <div
        style="background: var(--green); color: white; padding: 14px 20px; border-radius: var(--radius-sm); font-weight: 600; margin-bottom: 20px; box-shadow: var(--shadow)">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div
    style="margin-bottom: 20px; background: var(--teal-bg); border: 1px solid var(--border-light); padding: 14px 18px; border-radius: var(--radius); display: flex; gap: 12px; align-items: center">
    <div style="font-size: 24px; color: var(--teal)"><i class="fas fa-info-circle"></i></div>
    <div style="font-size: 13px; color: var(--gray-600); line-height: 1.5">
        Data berikut dihimpun langsung dari **kuesioner singkat 3 pertanyaan (slider)** yang diisi secara sukarela oleh
        siswa melalui dashboard mereka. Penilaian bersifat rahasia/anonim untuk mendorong kejujuran siswa.
    </div>
</div>

<!-- Grid Atas: Status Saat Ini & Grafik Tren -->
<div id="kondisiKelasPanel" class="grid-2" style="margin-bottom:20px; display: grid; grid-template-columns: 1fr 1.5fr; gap: 20px;"
    x-data="{
        selectedKelasId: '',
        latestData: { hubungan: 0, nyaman: 0, bantuan: 0 },
        get statusKelas() {
            const avg = (this.latestData.hubungan + this.latestData.nyaman + this.latestData.bantuan) / 3;
            if (avg === 0) return 'Belum Ada Data';
            if (avg >= 4) return 'Sangat Baik & Harmonis';
            if (avg >= 3) return 'Kondusif';
            return 'Perlu Perhatian / Pendampingan';
        },
        get statusColor() {
            const avg = (this.latestData.hubungan + this.latestData.nyaman + this.latestData.bantuan) / 3;
            if (avg === 0) return 'var(--gray-400)';
            if (avg >= 4) return 'var(--green)';
            if (avg >= 3) return 'var(--blue)';
            return 'var(--red)';
        }
    }">
    <!-- Kiri: Status Kelas Saat Ini -->
    <div class="card"
        style="background: var(--white); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); display: flex; flex-direction: column;">
        <div class="card-header" style="margin-bottom: 16px">
            <h3><i class="fas fa-heartbeat" style="color:var(--red)"></i> Status Kelas Terkini</h3>
        </div>

        <div
            style="margin-bottom: 20px; background: var(--gray-50); border: 1px solid var(--border-light); padding: 12px 14px; border-radius: var(--radius-sm); display: flex; flex-direction: column; align-items: center">
            <span
                style="font-size:11px; font-weight:700; color:var(--gray-400); text-transform:uppercase; letter-spacing:0.5px">Kesimpulan
                Kondisi</span>
            <span style="font-size:15px; font-weight:800; margin-top:4px; text-align:center"
                :style="'color: ' + statusColor" x-text="statusKelas"></span>
        </div>

        <!-- Metrik 1: Hubungan -->
        <div class="indicator-card">
            <div class="indicator-title">Hubungan Guru-Siswa</div>
            <div class="indicator-value-row">
                <span class="indicator-score" x-text="latestData.hubungan.toFixed(1)">0.0</span>
                <span class="indicator-scale">/ 5.0</span>
            </div>
            <div class="indicator-bar-track">
                <div class="indicator-bar-fill"
                    :style="'background: var(--green); width: ' + (latestData.hubungan * 20) + '%'"></div>
            </div>
            <div class="indicator-desc"
                :style="'color: ' + (latestData.hubungan >= 4 ? 'var(--green)' : (latestData.hubungan >= 3 ? 'var(--blue)' :
                    'var(--red)'))"
                x-text="latestData.hubungan >= 4 ? 'Sangat Komunikatif' : (latestData.hubungan >= 3 ? 'Cukup Kondusif' : (latestData.hubungan > 0 ? 'Renggang / Kurang Terbuka' : 'Tidak Ada Data'))">
            </div>
        </div>

        <!-- Metrik 2: Kenyamanan -->
        <div class="indicator-card">
            <div class="indicator-title">Kenyamanan Siswa</div>
            <div class="indicator-value-row">
                <span class="indicator-score" style="color: var(--purple)"
                    x-text="latestData.nyaman.toFixed(1)">0.0</span>
                <span class="indicator-scale">/ 5.0</span>
            </div>
            <div class="indicator-bar-track">
                <div class="indicator-bar-fill"
                    :style="'background: var(--purple); width: ' + (latestData.nyaman * 20) + '%'"></div>
            </div>
            <div class="indicator-desc"
                :style="'color: ' + (latestData.nyaman >= 4 ? 'var(--green)' : (latestData.nyaman >= 3 ? 'var(--blue)' :
                    'var(--red)'))"
                x-text="latestData.nyaman >= 4 ? 'Aman & Nyaman' : (latestData.nyaman >= 3 ? 'Biasa / Cukup Nyaman' : (latestData.nyaman > 0 ? 'Ada Siswa Kurang Nyaman' : 'Tidak Ada Data'))">
            </div>
        </div>

        <!-- Metrik 3: Bantuan -->
        <div class="indicator-card">
            <div class="indicator-title">Kemudahan Mencari Bantuan</div>
            <div class="indicator-value-row">
                <span class="indicator-score" style="color: var(--orange)"
                    x-text="latestData.bantuan.toFixed(1)">0.0</span>
                <span class="indicator-scale">/ 5.0</span>
            </div>
            <div class="indicator-bar-track">
                <div class="indicator-bar-fill"
                    :style="'background: var(--orange); width: ' + (latestData.bantuan * 20) + '%'"></div>
            </div>
            <div class="indicator-desc"
                :style="'color: ' + (latestData.bantuan >= 4 ? 'var(--green)' : (latestData.bantuan >= 3 ? 'var(--blue)' :
                    'var(--red)'))"
                x-text="latestData.bantuan >= 4 ? 'Sangat Terbuka' : (latestData.bantuan >= 3 ? 'Cukup Terbuka' : (latestData.bantuan > 0 ? 'Siswa Malu / Takut Bertanya' : 'Tidak Ada Data'))">
            </div>
        </div>
    </div>

    <!-- Kanan: Tren & Grafik Kondisi Kelas -->
    <div class="card"
        style="background: var(--white); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); display: flex; flex-direction: column;">
        <div class="card-header"
            style="margin-bottom: 20px; display:flex; justify-content:space-between; align-items:center">
            <h3><i class="fas fa-chart-line" style="color:var(--blue)"></i> Grafik Tren Kondisi Kelas</h3>
            <select id="chartKelasFilter" class="form-select" x-model="selectedKelasId"
                style="padding:6px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:12px;background:var(--white)">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelasYangDiajar as $k)
                    <option value="{{ $k->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex:1; min-height:300px; position:relative">
            <canvas id="kondisiKelasChart"></canvas>
            <div id="noChartData"
                style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:var(--gray-400); font-weight:600">
                Belum ada data survei dari siswa untuk kelas ini
            </div>
        </div>
        <div style="margin-top: 15px; font-size:12px; color:var(--gray-400); line-height: 1.5">
            <i class="fas fa-info-circle"></i> Grafik di atas memetakan perubahan nilai rata-rata dari ketiga aspek
            seiring berjalannya waktu. Nilai berkisar antara 1 s.d 5.
        </div>
    </div>
</div>

<!-- Bagian Bawah: Riwayat Pengisian -->
<div class="card"
    style="background: var(--white); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow)">
    <div class="card-header" style="margin-bottom: 15px">
        <h3><i class="fas fa-history" style="color:var(--teal)"></i> Riwayat Penilaian Agregat Kelas</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kelas</th>
                    <th>Rata-rata Hubungan</th>
                    <th>Rata-rata Kenyamanan</th>
                    <th>Rata-rata Kemudahan Bantuan</th>
                    <th>Status Kelas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kondisiKelasHistory as $h)
                    @php
                        $avgScore = ($h->avg_hubungan + $h->avg_nyaman + $h->avg_bantuan) / 3;
                        $statusText =
                            $avgScore >= 4 ? 'Sangat Baik' : ($avgScore >= 3 ? 'Kondusif' : 'Perlu Perhatian');
                        $statusClass = $avgScore >= 4 ? 'green light' : ($avgScore >= 3 ? 'blue light' : 'red light');
                    @endphp
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($h->tanggal)->format('d M Y') }}</strong></td>
                        <td>{{ $h->kelas->nama_kelas ?? '-' }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px">
                                <span class="badge light green"
                                    style="font-weight:700">{{ number_format($h->avg_hubungan, 1) }}</span>
                                <span style="font-size:12px; color:var(--gray-500)">
                                    {{ $h->avg_hubungan >= 4.5 ? 'Sangat Harmonis' : ($h->avg_hubungan >= 3.5 ? 'Baik' : ($h->avg_hubungan >= 2.5 ? 'Cukup' : 'Renggang')) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px">
                                <span class="badge light purple"
                                    style="font-weight:700">{{ number_format($h->avg_nyaman, 1) }}</span>
                                <span style="font-size:12px; color:var(--gray-500)">
                                    {{ $h->avg_nyaman >= 4.5 ? 'Sangat Nyaman' : ($h->avg_nyaman >= 3.5 ? 'Nyaman' : ($h->avg_nyaman >= 2.5 ? 'Cukup' : 'Kurang')) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px">
                                <span class="badge light orange"
                                    style="font-weight:700">{{ number_format($h->avg_bantuan, 1) }}</span>
                                <span style="font-size:12px; color:var(--gray-500)">
                                    {{ $h->avg_bantuan >= 4.5 ? 'Sangat Mudah' : ($h->avg_bantuan >= 3.5 ? 'Mudah' : ($h->avg_bantuan >= 2.5 ? 'Cukup' : 'Sulit')) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--gray-400);padding:30px">
                            Belum ada riwayat pengisian kondisi kelas dari siswa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const historyData = @json($kondisiKelasHistory).map(item => ({
            ...item,
            kelas_id: String(item.kelas_id),
            avg_hubungan: Number(item.avg_hubungan || 0),
            avg_nyaman: Number(item.avg_nyaman || 0),
            avg_bantuan: Number(item.avg_bantuan || 0),
        }));
        const kelasSelect = document.getElementById('chartKelasFilter');
        const noDataEl = document.getElementById('noChartData');
        const canvas = document.getElementById('kondisiKelasChart');
        let chartInstance = null;

        // Get this component's Alpine scope to update status cards.
        const gridEl = document.getElementById('kondisiKelasPanel');

        function getAlpineScope() {
            if (!gridEl) return null;
            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                return window.Alpine.$data(gridEl);
            }
            return gridEl._x_dataStack ? gridEl._x_dataStack[0] : null;
        }

        function updateAlpineData(kelasId) {
            const alpineScope = getAlpineScope();
            if (!alpineScope) return;

            // Cari data teranyar untuk kelas ini
            const classHistory = historyData.filter(item => item.kelas_id === String(kelasId))
                .sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal)); // Tanggal terbaru dulu

            if (classHistory.length > 0) {
                alpineScope.latestData = {
                    hubungan: parseFloat(classHistory[0].avg_hubungan),
                    nyaman: parseFloat(classHistory[0].avg_nyaman),
                    bantuan: parseFloat(classHistory[0].avg_bantuan)
                };
            } else {
                alpineScope.latestData = {
                    hubungan: 0,
                    nyaman: 0,
                    bantuan: 0
                };
            }
        }

        function renderChart(kelasId) {
            // Update data panel kiri
            updateAlpineData(kelasId);

            if (!canvas || typeof Chart === 'undefined') return;

            // Filter data berdasarkan kelas_id
            const filtered = historyData.filter(item => item.kelas_id === String(kelasId))
                .sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal)); // Urutkan tertua ke terbaru

            if (filtered.length === 0) {
                noDataEl.style.display = 'block';
                canvas.style.display = 'none';
                if (chartInstance) {
                    chartInstance.destroy();
                    chartInstance = null;
                }
                return;
            }

            noDataEl.style.display = 'none';
            canvas.style.display = 'block';

            // Persiapkan data grafik
            const labels = filtered.map(item => {
                const date = new Date(item.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                });
            });
            const dataHubungan = filtered.map(item => item.avg_hubungan);
            const dataNyaman = filtered.map(item => item.avg_nyaman);
            const dataBantuan = filtered.map(item => item.avg_bantuan);

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Hubungan Guru-Siswa',
                            data: dataHubungan,
                            borderColor: '#4CAF7D', // var(--green)
                            backgroundColor: 'rgba(76, 175, 125, 0.1)',
                            borderWidth: 3,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Kenyamanan Siswa',
                            data: dataNyaman,
                            borderColor: '#7C3AED', // var(--purple)
                            backgroundColor: 'rgba(124, 58, 237, 0.1)',
                            borderWidth: 3,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Kemudahan Bantuan',
                            data: dataBantuan,
                            borderColor: '#F59E0B', // var(--orange)
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 3,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    weight: '600',
                                    size: 11
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            min: 1,
                            max: 5,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    weight: '600'
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    weight: '600'
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        if (kelasSelect) {
            // Sinkronisasi Alpine scope dengan inisiasi dropdown
            const alpineScope = getAlpineScope();
            if (alpineScope) {
                alpineScope.selectedKelasId = kelasSelect.value;
            }

            kelasSelect.addEventListener('change', function() {
                renderChart(this.value);
            });

            // Render awal
            if (kelasSelect.value) {
                renderChart(kelasSelect.value);
            }
        }

        const sidebarMenu = document.querySelector('.sidebar-menu');
        if (sidebarMenu) {
            sidebarMenu.addEventListener('click', function() {
                setTimeout(() => {
                    if (kelasSelect && kelasSelect.value && gridEl && gridEl.offsetParent !== null) {
                        renderChart(kelasSelect.value);
                    }
                }, 100);
            });
        }
    });
</script>
