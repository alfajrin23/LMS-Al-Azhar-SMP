@php
    $title = [
        'akademik' => 'Rapor Akademik',
        'english' => 'Rapor English',
        'quran' => 'Rapor Quran',
    ][$jenis] ?? 'Rapor';
    $attendance = $attendanceSummary ?? ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0, 'total_hari_efektif' => 0, 'tidak_hadir' => 0, 'persentase' => 0];
@endphp

<section class="rapor-doc">
    <header class="rapor-header">
        <h1>{{ strtoupper($title) }}</h1>
        <p>SMPIT Al Azhar Jaya Indonesia - Tahun Ajaran {{ $tahunAjaran }} - {{ $semester }}</p>
        <p>Jl. Sirih Prada No. 135, Pabuaran, Cimuning, Mustika Jaya, Kota Bekasi</p>
    </header>

    <div class="rapor-section">
        <h2>Identitas Siswa</h2>
        <div class="rapor-grid">
            <div><span>Nama</span><strong>{{ $siswa->nama }}</strong></div>
            <div><span>NIS</span><strong>{{ $siswa->nis }}</strong></div>
            <div><span>Kelas</span><strong>{{ $kelas?->nama_kelas ?? '-' }}</strong></div>
            <div><span>Fase</span><strong>{{ str_starts_with($kelas?->kode_kelas ?? '', '1') || str_starts_with($kelas?->kode_kelas ?? '', '2') ? 'A' : 'B/C' }}</strong></div>
            <div><span>Kelas Quran</span><strong>{{ $siswa->kelasQuran?->nama_kelas ?? '-' }}</strong></div>
            <div><span>Status</span><strong>{{ $rapor?->status ? ucfirst($rapor->status) : 'Draft Data' }}</strong></div>
        </div>
    </div>

    @if($jenis === 'akademik')
        <div class="rapor-section">
            <h2>Nilai Akademik</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mata Pelajaran</th>
                        <th>Nilai Akhir</th>
                        <th>Predikat</th>
                        <th>Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->mapel?->nama_mapel ?? $item->komponen }}</td>
                            <td class="num">{{ $item->nilai ?? '-' }}</td>
                            <td>{{ $item->predikat ?? '-' }}</td>
                            <td>{{ $item->deskripsi ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Belum ada data nilai akademik.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rapor-section">
            <h2>Ekstrakurikuler dan Catatan</h2>
            <table>
                <thead><tr><th>Kegiatan</th><th>Predikat</th><th>Deskripsi</th></tr></thead>
                <tbody>
                    @forelse($extraItems as $item)
                        <tr><td>{{ $item->komponen }}</td><td>{{ $item->predikat ?? '-' }}</td><td>{{ $item->deskripsi ?? '-' }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="empty">Belum ada data ekstrakurikuler.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p class="note">{{ $rapor?->catatan ?? $catatanWali?->catatan ?? 'Belum ada catatan wali kelas.' }}</p>
        </div>
    @elseif($jenis === 'english')
        <div class="rapor-section">
            <h2>English Components</h2>
            <table>
                <thead><tr><th>No</th><th>Component</th><th>Score</th><th>Predicate</th><th>Teacher Comment</th></tr></thead>
                <tbody>
                    @forelse($englishItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->komponen }}</td>
                            <td class="num">{{ $item->nilai ?? '-' }}</td>
                            <td>{{ $item->predikat ?? '-' }}</td>
                            <td>{{ $item->deskripsi ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Belum ada data Rapor English.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p class="note">{{ $rapor?->catatan ?? 'Komponen English dapat dikonfigurasi per item rapor.' }}</p>
        </div>
    @else
        <div class="rapor-section">
            <h2>Deskripsi Penilaian Siswa</h2>
            <div class="rapor-grid">
                <div><span>Tingkat UMMI</span><strong>{{ $tahfidzProgress?->tingkat_ummi ?? '-' }}</strong></div>
                <div><span>Posisi Tilawah</span><strong>{{ $tahfidzProgress?->posisi_tilawah ?? '-' }}</strong></div>
                <div><span>Hafalan Terakhir</span><strong>{{ $tahfidzProgress?->hafalan_terakhir ?? $tahfidzProgress?->surah ?? '-' }}</strong></div>
                <div><span>Predikat</span><strong>{{ $tahfidzProgress?->predikat ?? '-' }}</strong></div>
            </div>
            <p class="note">{{ $rapor?->catatan ?? $tahfidzProgress?->catatan ?? 'Belum ada narasi perkembangan Quran.' }}</p>
        </div>

        <div class="rapor-section">
            <h2>Kemampuan Membaca Al-Quran</h2>
            <table>
                <thead><tr><th>No</th><th>Materi Penilaian</th><th>Nilai</th><th>Catatan</th></tr></thead>
                <tbody>
                    @forelse($quranReadingItems as $item)
                        <tr><td>{{ $loop->iteration }}</td><td>{{ $item->komponen }}</td><td>{{ $item->predikat ?? $item->nilai ?? '-' }}</td><td>{{ $item->deskripsi ?? '-' }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="empty">Belum ada data kemampuan membaca Al-Quran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rapor-section">
            <h2>Hafalan Surat Juz 30</h2>
            <table>
                <thead><tr><th>No</th><th>Surat</th><th>Nilai</th><th>No</th><th>Surat</th><th>Nilai</th></tr></thead>
                <tbody>
                    @foreach($quranSurahItems->chunk(2) as $chunk)
                        <tr>
                            @foreach($chunk as $item)
                                <td>{{ $loop->parent->iteration + (($loop->iteration - 1) * ceil($quranSurahItems->count() / 2)) }}</td>
                                <td>{{ $item->komponen }}</td>
                                <td>{{ $item->predikat ?? $item->nilai ?? '-' }}</td>
                            @endforeach
                            @if($chunk->count() === 1)
                                <td></td><td></td><td></td>
                            @endif
                        </tr>
                    @endforeach
                    @if($quranSurahItems->isEmpty())
                        <tr><td colspan="6" class="empty">Belum ada data hafalan surat Juz 30.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif

    <div class="rapor-section">
        <h2>Kehadiran</h2>
        <div class="rapor-grid compact">
            <div><span>Total Hari Efektif</span><strong>{{ $attendance['total_hari_efektif'] }}</strong></div>
            <div><span>Hadir</span><strong>{{ $attendance['hadir'] }}</strong></div>
            <div><span>Sakit</span><strong>{{ $attendance['sakit'] }}</strong></div>
            <div><span>Izin</span><strong>{{ $attendance['izin'] }}</strong></div>
            <div><span>Tanpa Keterangan</span><strong>{{ $attendance['alpha'] }}</strong></div>
            <div><span>Persentase Hadir</span><strong>{{ number_format($attendance['persentase'], 2, ',', '.') }}%</strong></div>
        </div>
    </div>

    <div class="signature-grid">
        @if($jenis === 'quran')
            <div><span>Koordinator Quran</span><strong>{{ $signatures['koordinator_quran'] ?? '-' }}</strong></div>
            <div><span>Guru Tahfidz</span><strong>{{ $signatures['guru_tahfidz'] ?? '-' }}</strong></div>
        @else
            <div><span>Wali Kelas</span><strong>{{ $signatures['wali_kelas'] ?? $catatanWali?->guru?->nama ?? '-' }}</strong></div>
            <div><span>Orang Tua/Wali</span><strong>&nbsp;</strong></div>
        @endif
        <div><span>Mengetahui, Kepala Sekolah</span><strong>{{ $signatures['kepala_sekolah'] ?? '-' }}</strong></div>
    </div>
</section>
