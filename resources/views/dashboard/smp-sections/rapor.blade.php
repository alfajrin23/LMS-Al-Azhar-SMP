@php
    $gradeColor = function ($v) {
        if ($v >= 90) {
            return 'grade-A';
        }
        if ($v >= 85) {
            return 'grade-B';
        }
        return 'grade-C';
    };
    $gradeLetter = function ($v) {
        if ($v >= 90) {
            return 'A';
        }
        if ($v >= 85) {
            return 'A-';
        }
        if ($v >= 80) {
            return 'B+';
        }
        if ($v >= 75) {
            return 'B';
        }
        if ($v >= 70) {
            return 'B-';
        }
        return 'C';
    };
    $getStatus = function ($v, $kkm = 75) {
        if ($v >= 90) {
            return ['label' => 'Bagus Banget', 'class' => 'status-bagus-banget'];
        }
        if ($v >= 80) {
            return ['label' => 'Bagus', 'class' => 'status-bagus'];
        }
        if ($v >= $kkm) {
            return ['label' => 'Perlu Belajar Lagi', 'class' => 'status-perlu-belajar'];
        }
        return ['label' => 'Perlu Diulang', 'class' => 'status-perlu-diulang'];
    };
    $rataSekolah = round($nilaiSekolah->avg('nilai') ?? 0, 1);
    $rataUnggulan = round($nilaiUnggulan->avg('nilai') ?? 0, 1);
    $totalHadir = $kehadiran->where('status', 'hadir')->count();
    $totalSakit = $kehadiran->where('status', 'sakit')->count();
    $totalIzin = $kehadiran->where('status', 'izin')->count();
    $totalAlpha = $kehadiran->where('status', 'alpha')->count();
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
<div x-data="{ activeRaporTab: 'sekolah', subRapor: 'akademik' }">
    <div class="content-header">
        <div>
            <h1>Rapor Semester</h1>
            <p style="font-size:14px;color:var(--gray-400);margin-top:2px">Laporan hasil belajar siswa —
                {{ $catatanWali?->semester ?? setting('semester_aktif') }}</p>
        </div>
        <div class="header-right">
            <a :href="'{{ route('rapor.pdf') }}?type=' + (activeRaporTab === 'sekolah' ? 'biasa' : 'unggulan')"
                target="_blank" class="header-btn primary"><i class="fas fa-file-pdf"></i> PDF</a>
            <a @click="window.print();return false" class="header-btn primary" style="cursor:pointer"><i
                    class="fas fa-print"></i> Cetak</a>
            <div class="avatar blue">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
        </div>
    </div>
    <div
        style="display: flex; gap: 8px; margin-bottom: 20px; border-bottom: 2px solid var(--border-light); padding-bottom: 8px;">
        <button @click="activeRaporTab = 'sekolah'"
            :class="activeRaporTab === 'sekolah' ? 'btn-tab-active' : 'btn-tab-inactive'"
            style="padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
            <i class="fas fa-school" style="margin-right: 6px;"></i> Rapor Sekolah
        </button>
        <button @click="activeRaporTab = 'unggulan'"
            :class="activeRaporTab === 'unggulan' ? 'btn-tab-active' : 'btn-tab-inactive'"
            style="padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
            <i class="fas fa-star" style="margin-right: 6px;"></i> Rapor Program Unggulan
        </button>
    </div>
    <style>
        .btn-tab-active {
            background-color: var(--teal);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-tab-inactive {
            background-color: var(--bg-light, #f3f4f6);
            color: var(--gray-500);
        }
        .btn-tab-inactive:hover {
            background-color: var(--border-light);
            color: var(--gray-800);
        }
    </style>
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h3><i class="fas fa-user-graduate" style="color:var(--teal)"></i> Identitas Siswa</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:4px 0">
            <div><span style="font-size:12px;color:var(--gray-400)">Nama</span>
                <div style="font-weight:600">{{ $siswa->nama }}</div>
            </div>
            <div><span style="font-size:12px;color:var(--gray-400)">NIS</span>
                <div style="font-weight:600">{{ $siswa->nis }}</div>
            </div>
            <div><span style="font-size:12px;color:var(--gray-400)">Kelas</span>
                <div style="font-weight:600">{{ $kelas?->nama_kelas }}</div>
            </div>
            <div><span style="font-size:12px;color:var(--gray-400)">Semester</span>
                <div style="font-weight:600">{{ $catatanWali?->semester ?? setting('semester_aktif') }}</div>
            </div>
        </div>
    </div>
    <div style="margin-bottom: 20px;">
        <select x-model="subRapor" class="form-select" style="width: 100%; max-width: 300px; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); font-weight: 600; color: var(--gray-700); background-color: var(--white); cursor: pointer;">
            <option value="quran">Rapot Quran</option>
            <option value="akademik">Rapot Akademik</option>
            <option value="english">Rapot English</option>
        </select>
    </div>
    <div x-show="activeRaporTab === 'sekolah'">
        <div x-show="subRapor === 'quran'" x-cloak>
            <div class="card" style="margin-bottom:20px; padding: 20px; text-align: center; color: var(--gray-500);">
                <i class="fas fa-quran" style="font-size: 30px; margin-bottom: 12px; color: var(--teal);"></i><br>
                Konten Rapot Quran Sekolah (Belum Tersedia)
            </div>
        </div>
        <div x-show="subRapor === 'akademik'">
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-file-invoice" style="color:var(--blue)"></i> Nilai Akademik Sekolah</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>KKM</th>
                            <th>Nilai</th>
                            <th>Grade</th>
                            <th>Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilaiSekolah as $n)
                            @php $status = $getStatus($n->nilai, setting('kkm_smp')); @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $n->mapel->nama_mapel }}</td>
                                <td>{{ setting('kkm_smp') }}</td>
                                <td style="font-weight:700">{{ $n->nilai }}</td>
                                <td><span class="{{ $gradeColor($n->nilai) }}">{{ $gradeLetter($n->nilai) }}</span>
                                </td>
                                <td><span class="status-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--gray-400)">Belum ada nilai
                                    akademik sekolah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border-light);display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Rata-rata</span>
                    <div style="font-size:20px;font-weight:700;color:var(--teal)">{{ $rataSekolah }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Peringkat Kelas</span>
                    <div style="font-size:20px;font-weight:700;color:var(--blue)">{{ $peringkat['rank'] }} /
                        {{ $peringkat['total'] }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Status</span>
                    <div style="font-size:20px;font-weight:700;color:var(--green)">
                        {{ $rataSekolah >= setting('kkm_smp') ? 'LULUS' : 'TIDAK LULUS' }}</div>
                </div>
            </div>
            </div>
        </div>
        <div x-show="subRapor === 'english'" x-cloak>
            <div class="card" style="margin-bottom:20px; padding: 20px; text-align: center; color: var(--gray-500);">
                <i class="fas fa-language" style="font-size: 30px; margin-bottom: 12px; color: var(--purple);"></i><br>
                Konten Rapot English Sekolah (Belum Tersedia)
            </div>
        </div>
    </div>
    <div x-show="activeRaporTab === 'unggulan'" x-cloak>
        <div x-show="subRapor === 'quran'" x-cloak>
            <div class="card" style="margin-bottom:20px; padding: 20px; text-align: center; color: var(--gray-500);">
                <i class="fas fa-quran" style="font-size: 30px; margin-bottom: 12px; color: var(--teal);"></i><br>
                Konten Rapot Quran Unggulan (Belum Tersedia)
            </div>
        </div>
        <div x-show="subRapor === 'akademik'">
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <h3><i class="fas fa-star" style="color:var(--orange)"></i> Nilai Program Unggulan</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Program Unggulan</th>
                            <th>KKM</th>
                            <th>Nilai</th>
                            <th>Grade</th>
                            <th>Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilaiUnggulan as $n)
                            @php $status = $getStatus($n->nilai, setting('kkm_smp')); @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $n->mapel->nama_mapel }}</td>
                                <td>{{ setting('kkm_smp') }}</td>
                                <td style="font-weight:700">{{ $n->nilai }}</td>
                                <td><span class="{{ $gradeColor($n->nilai) }}">{{ $gradeLetter($n->nilai) }}</span>
                                </td>
                                <td><span class="status-badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--gray-400)">Belum ada nilai
                                    program unggulan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border-light);display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div><span style="font-size:12px;color:var(--gray-400)">Rata-rata</span>
                    <div style="font-size:20px;font-weight:700;color:var(--teal)">{{ $rataUnggulan }}</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Status</span>
                    <div style="font-size:20px;font-weight:700;color:var(--green)">
                        {{ $rataUnggulan >= setting('kkm_smp') ? 'LULUS' : 'TIDAK LULUS' }}</div>
                </div>
            </div>
            </div>
        </div>
        <div x-show="subRapor === 'english'" x-cloak>
            <div class="card" style="margin-bottom:20px; padding: 20px; text-align: center; color: var(--gray-500);">
                <i class="fas fa-language" style="font-size: 30px; margin-bottom: 12px; color: var(--purple);"></i><br>
                Konten Rapot English Unggulan (Belum Tersedia)
            </div>
        </div>
    </div>
    <div style="display:flex;gap:16px;flex-wrap:wrap">
        <div class="card" style="flex:1;min-width:200px">
            <div class="card-header">
                <h3><i class="fas fa-star" style="color:var(--orange)"></i> Catatan Wali Kelas</h3>
            </div>
            @if ($catatanWali)
                <p style="font-size:14px;color:var(--gray-500);line-height:1.7;font-style:italic">
                    &quot;{{ $catatanWali->catatan }}&quot;</p>
                <p style="font-size:12px;color:var(--gray-400);margin-top:8px">— {{ $catatanWali->guru->nama }}, Wali
                    Kelas {{ $kelas?->nama_kelas }}</p>
            @else
                <p style="font-size:14px;color:var(--gray-400);font-style:italic">Belum ada catatan wali kelas.</p>
            @endif
        </div>
        <div class="card" style="flex:1;min-width:200px">
            <div class="card-header">
                <h3><i class="fas fa-calendar-check" style="color:var(--teal)"></i> Ringkasan Kehadiran</h3>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div><span style="font-size:12px;color:var(--gray-400)">Hadir</span>
                    <div style="font-weight:700;color:var(--green)">{{ $totalHadir }} Hari</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Sakit</span>
                    <div style="font-weight:700;color:var(--orange)">{{ $totalSakit }} Hari</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Izin</span>
                    <div style="font-weight:700;color:var(--blue)">{{ $totalIzin }} Hari</div>
                </div>
                <div><span style="font-size:12px;color:var(--gray-400)">Alpha</span>
                    <div style="font-weight:700;color:var(--red)">{{ $totalAlpha }} Hari</div>
                </div>
            </div>
        </div>
    </div>
</div>
