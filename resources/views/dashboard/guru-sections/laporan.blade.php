@php
    $guruActive = \App\Models\Guru::where('user_id', auth()->id())->first();
    $laporans = $guruActive
        ? \App\Models\LaporanMengajar::where('guru_id', $guruActive->id)->orderBy('tanggal', 'desc')->get()
        : collect();
@endphp
<div x-data="{
    tipe: 'harian',
    tanggal: '{{ date('Y-m-d') }}',
    catatan_umum: '',
    checklists: {
        modul_rpp: { status: 'ya', catatan: '' },
        media_siap: { status: 'ya', catatan: '' },
        apersepsi: { status: 'ya', catatan: '' },
        tujuan_tampil: { status: 'ya', catatan: '' },
        siswa_tanya: { status: 'ya', catatan: '' },
        siswa_diskusi: { status: 'ya', catatan: '' },
        siswa_tugas: { status: 'ya', catatan: '' },
        metode_aktif: { status: 'ya', catatan: '' },
        hots_tanya: { status: 'ya', catatan: '' },
        literasi_int: { status: 'ya', catatan: '' },
        numerasi_int: { status: 'ya', catatan: '' },
        media_efektif: { status: 'ya', catatan: '' },
        kelas_kondusif: { status: 'ya', catatan: '' },
        guru_asesmen: { status: 'ya', catatan: '' },
        refleksi_kbm: { status: 'ya', catatan: '' }
    },
    kendalas: [],
    pemetaan_masalah_siswa: [],
    refleksi: {
        materi_tersampaikan: '',
        target_tercapai: '',
        kendala_terbesar: '',
        strategi_perbaikan: '',
        rencana_pertemuan: ''
    },
    rekap_pembelajaran: [
        { hari: 'Senin', materi: '', kehadiran: '', ketuntasan: '', hots: '', catatan: '' },
        { hari: 'Selasa', materi: '', kehadiran: '', ketuntasan: '', hots: '', catatan: '' },
        { hari: 'Rabu', materi: '', kehadiran: '', ketuntasan: '', hots: '', catatan: '' },
        { hari: 'Kamis', materi: '', kehadiran: '', ketuntasan: '', hots: '', catatan: '' },
        { hari: 'Jumat', materi: '', kehadiran: '', ketuntasan: '', hots: '', catatan: '' }
    ],
    evaluasi_akademik: {
        ketuntasan_materi: { target: '>= 75%', capaian: '', analisis: '' },
        keaktifan_siswa: { target: '>= 80%', capaian: '', analisis: '' },
        hots: { target: 'Berkembang', capaian: '', analisis: '' },
        literasi: { target: 'Berkembang', capaian: '', analisis: '' },
        numerasi: { target: 'Berkembang', capaian: '', analisis: '' }
    },
    analisis_kendala_mingguan: [],
    pemetaan_siswa_mingguan: [],
    tindak_lanjut: [],
    capaian_belajar_bulanan: [
        { elemen_cp: 'Pengetahuan', target: '', capaian: '', persentase: '', keterangan: '' },
        { elemen_cp: 'Keterampilan', target: '', capaian: '', persentase: '', keterangan: '' },
        { elemen_cp: 'HOTS', target: '', capaian: '', persentase: '', keterangan: '' },
        { elemen_cp: 'Literasi', target: '', capaian: '', persentase: '', keterangan: '' },
        { elemen_cp: 'Numerasi', target: '', capaian: '', persentase: '', keterangan: '' }
    ],
    evaluasi_dan_kendala_bulanan: [
        { bidang: 'Akademik', permasalahan: '', analisis_penyebab: '', dampak: '', solusi_dilakukan: '', efektivitas: '' },
        { bidang: 'Disiplin', permasalahan: '', analisis_penyebab: '', dampak: '', solusi_dilakukan: '', efektivitas: '' },
        { bidang: 'Motivasi', permasalahan: '', analisis_penyebab: '', dampak: '', solusi_dilakukan: '', efektivitas: '' },
        { bidang: 'Sarana', permasalahan: '', analisis_penyebab: '', dampak: '', solusi_dilakukan: '', efektivitas: '' },
        { bidang: 'SDM', permasalahan: '', analisis_penyebab: '', dampak: '', solusi_dilakukan: '', efektivitas: '' }
    ],
    analisis_siswa_bulanan: [
        { kategori_siswa: 'Sangat baik', jumlah: '', permasalahan_dominan: '', program_solusi: '' },
        { kategori_siswa: 'Baik', jumlah: '', permasalahan_dominan: '', program_solusi: '' },
        { kategori_siswa: 'Perlu pendampingan', jumlah: '', permasalahan_dominan: '', program_solusi: '' },
        { kategori_siswa: 'Remedial', jumlah: '', permasalahan_dominan: '', program_solusi: '' },
        { kategori_siswa: 'HOTS rendah', jumlah: '', permasalahan_dominan: '', program_solusi: '' }
    ],
    pemetaan_masalah_jangka_pendek: [
        { masalah: '', solusi: '', pic: '', target_waktu: '' }
    ],
    pemetaan_masalah_jangka_menengah: [
        { masalah: '', program_strategis: '', target: '', evaluasi_berkala: '' }
    ],
    loadLaporan(tipe, tanggal, isi) {
        this.tipe = tipe;
        this.tanggal = tanggal;
        this.catatan_umum = '';
        if (typeof isi === 'object' && isi !== null) {
            this.catatan_umum = isi.catatan_umum || '';
            if (tipe === 'harian') {
                if (isi.checklists) {
                    Object.keys(this.checklists).forEach(k => {
                        if (isi.checklists[k]) this.checklists[k] = isi.checklists[k];
                    });
                }
                this.kendalas = isi.kendala || [];
                this.pemetaan_masalah_siswa = isi.pemetaan_masalah_siswa || [];
                if (isi.refleksi) this.refleksi = isi.refleksi;
            } else if (tipe === 'mingguan') {
                this.rekap_pembelajaran = isi.rekap_pembelajaran || this.rekap_pembelajaran;
                if (isi.evaluasi_akademik) this.evaluasi_akademik = isi.evaluasi_akademik;
                this.analisis_kendala_mingguan = isi.analisis_kendala || [];
                this.pemetaan_siswa_mingguan = isi.pemetaan_siswa || [];
                this.tindak_lanjut = isi.tindak_lanjut || [];
            } else if (tipe === 'bulanan') {
                this.capaian_belajar_bulanan = isi.capaian_belajar_bulanan || this.capaian_belajar_bulanan;
                this.evaluasi_dan_kendala_bulanan = isi.evaluasi_dan_kendala || this.evaluasi_dan_kendala_bulanan;
                this.analisis_siswa_bulanan = isi.analisis_siswa || this.analisis_siswa_bulanan;
                this.pemetaan_masalah_jangka_pendek = isi.pemetaan_masalah_jangka_pendek || this.pemetaan_masalah_jangka_pendek;
                this.pemetaan_masalah_jangka_menengah = isi.pemetaan_masalah_jangka_menengah || this.pemetaan_masalah_jangka_menengah;
            }
        } else if (typeof isi === 'string') {
            this.catatan_umum = isi;
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}">
    <div class="content-header">
        <h1>Laporan Mengajar <span>Guru</span></h1>
        <div class="header-right">
            <div class="avatar blue">{{ strtoupper(substr($guruActive->nama ?? 'G', 0, 1)) }}</div>
        </div>
    </div>
    <div class="card" style="margin-bottom:20px">
        <div class="card-header" style="background:var(--blue-bg);border-bottom:1px solid var(--border)">
            <h3 style="color:var(--blue)"><i class="fas fa-edit"></i> Form Laporan Mengajar Terstruktur</h3>
        </div>
        <form method="POST" action="{{ route('guru.laporan.store') }}" style="padding:15px">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px">
                <div class="form-group">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:6px">Tipe
                        Laporan</label>
                    <div style="display:flex;gap:15px;align-items:center;height:40px">
                        <label style="display:inline-flex;align-items:center;font-size:14px;cursor:pointer">
                            <input type="radio" name="tipe" value="harian" x-model="tipe" style="margin-right:6px">
                            Harian
                        </label>
                        <label style="display:inline-flex;align-items:center;font-size:14px;cursor:pointer">
                            <input type="radio" name="tipe" value="mingguan" x-model="tipe"
                                style="margin-right:6px"> Mingguan
                        </label>
                        <label style="display:inline-flex;align-items:center;font-size:14px;cursor:pointer">
                            <input type="radio" name="tipe" value="bulanan" x-model="tipe"
                                style="margin-right:6px"> Bulanan
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--gray-500);margin-bottom:6px">Tanggal
                        Laporan</label>
                    <input type="date" name="tanggal" required x-model="tanggal"
                        style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;height:40px">
                </div>
            </div>
            <hr style="border:0;border-top:1px solid var(--border);margin:15px 0">
            <div x-show="tipe === 'harian'" style="display:flex;flex-direction:column;gap:20px">
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-check-double" style="color:var(--blue)"></i> 1. Aspek Monitoring Pembelajaran
                    </h4>
                    <div
                        style="overflow-x:auto;border:1px solid var(--border);border-radius:var(--radius-sm);background:#fafafa">
                        <table style="width:100%;border-collapse:collapse;font-size:13px">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border);background:#eee">
                                    <th style="text-align:left;padding:8px 12px">Aspek &amp; Indikator</th>
                                    <th style="width:70px;text-align:center;padding:8px">Ya</th>
                                    <th style="width:70px;text-align:center;padding:8px">Tidak</th>
                                    <th style="text-align:left;padding:8px 12px">Catatan Tambahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $indikators = [
                                        'modul_rpp' => ['Kesiapan Guru', 'Modul/RPP tersedia'],
                                        'media_siap' => ['Kesiapan Guru', 'Media pembelajaran siap'],
                                        'apersepsi' => ['Pembukaan', 'Guru membuka dengan apersepsi'],
                                        'tujuan_tampil' => ['Pembukaan', 'Menyampaikan tujuan pembelajaran'],
                                        'siswa_tanya' => ['Aktivitas Siswa', 'Siswa aktif bertanya'],
                                        'siswa_diskusi' => ['Aktivitas Siswa', 'Siswa aktif berdiskusi'],
                                        'siswa_tugas' => ['Aktivitas Siswa', 'Siswa mengerjakan tugas'],
                                        'metode_aktif' => ['Metode', 'Guru menggunakan metode aktif'],
                                        'hots_tanya' => ['HOTS', 'Terdapat pertanyaan HOTS'],
                                        'literasi_int' => ['Literasi', 'Integrasi literasi'],
                                        'numerasi_int' => ['Numerasi', 'Integrasi numerasi'],
                                        'media_efektif' => ['Penggunaan Media', 'Media digunakan efektif'],
                                        'kelas_kondusif' => ['Classroom Management', 'Kelas kondusif'],
                                        'guru_asesmen' => ['Penilaian', 'Guru melakukan asesmen'],
                                        'refleksi_kbm' => ['Penutup', 'Refleksi pembelajaran'],
                                    ];
                                @endphp
                                @foreach ($indikators as $key => $ind)
                                    <tr style="border-bottom:1px solid #eee">
                                        <td style="padding:8px 12px">
                                            <span
                                                style="font-size:11px;color:var(--gray-400);display:block">{{ $ind[0] }}</span>
                                            <strong>{{ $ind[1] }}</strong>
                                        </td>
                                        <td style="text-align:center;padding:8px">
                                            <input type="radio" name="isi[checklists][{{ $key }}][status]"
                                                value="ya" x-model="checklists.{{ $key }}.status"
                                                :required="tipe === 'harian'">
                                        </td>
                                        <td style="text-align:center;padding:8px">
                                            <input type="radio" name="isi[checklists][{{ $key }}][status]"
                                                value="tidak" x-model="checklists.{{ $key }}.status">
                                        </td>
                                        <td style="padding:4px 12px">
                                            <input type="text" name="isi[checklists][{{ $key }}][catatan]"
                                                x-model="checklists.{{ $key }}.catatan"
                                                placeholder="Catatan..."
                                                style="width:100%;padding:6px;border:1px solid var(--border);border-radius:3px;font-size:12px">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-exclamation-triangle" style="color:var(--orange)"></i> 2. Evaluasi Kendala
                        (Bila Ada)</h4>
                    <div style="margin-bottom:10px">
                        <button type="button"
                            @click="kendalas.push({bidang: 'Akademik', deskripsi: '', dampak: '', solusi: '', tindak_lanjut: ''})"
                            class="btn-small outline" style="cursor:pointer">
                            <i class="fas fa-plus"></i> Tambah Kendala
                        </button>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <template x-for="(k, index) in kendalas" :key="index">
                            <div
                                style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;background:#fff;position:relative">
                                <button type="button" @click="kendalas.splice(index, 1)"
                                    style="position:absolute;right:10px;top:10px;background:none;border:none;color:red;cursor:pointer">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                                <div style="display:grid;grid-template-columns:1fr 2fr;gap:10px;margin-bottom:8px">
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Bidang
                                            Kendala</label>
                                        <select :name="'isi[kendala][' + index + '][bidang]'" x-model="k.bidang"
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                            <option value="Akademik">Akademik</option>
                                            <option value="Disiplin">Disiplin</option>
                                            <option value="Konsentrasi">Konsentrasi</option>
                                            <option value="Media">Media</option>
                                            <option value="Waktu">Waktu</option>
                                            <option value="Bahasa/Literasi">Bahasa/Literasi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Deskripsi
                                            Kendala</label>
                                        <input type="text" :name="'isi[kendala][' + index + '][deskripsi]'"
                                            x-model="k.deskripsi" :required="tipe === 'harian'"
                                            placeholder="Deskripsi kendala..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Dampak</label>
                                        <input type="text" :name="'isi[kendala][' + index + '][dampak]'"
                                            x-model="k.dampak" :required="tipe === 'harian'"
                                            placeholder="Dampak..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Solusi
                                            Langsung Guru</label>
                                        <input type="text" :name="'isi[kendala][' + index + '][solusi]'"
                                            x-model="k.solusi" :required="tipe === 'harian'"
                                            placeholder="Solusi..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Tindak
                                            Lanjut</label>
                                        <input type="text" :name="'isi[kendala][' + index + '][tindak_lanjut]'"
                                            x-model="k.tindak_lanjut" :required="tipe === 'harian'"
                                            placeholder="Tindak lanjut..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-user-tag" style="color:var(--teal)"></i> 3. Pemetaan Masalah Siswa (Bila
                        Ada)</h4>
                    <div style="margin-bottom:10px">
                        <button type="button"
                            @click="pemetaan_masalah_siswa.push({nama_siswa: '', permasalahan: '', kategori: 'Akademik', solusi_pendek: '', monitoring_lanjutan: ''})"
                            class="btn-small outline" style="cursor:pointer">
                            <i class="fas fa-plus"></i> Tambah Masalah Siswa
                        </button>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <template x-for="(m, index) in pemetaan_masalah_siswa" :key="index">
                            <div
                                style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;background:#fff;position:relative">
                                <button type="button" @click="pemetaan_masalah_siswa.splice(index, 1)"
                                    style="position:absolute;right:10px;top:10px;background:none;border:none;color:red;cursor:pointer">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                                <div style="display:grid;grid-template-columns:2fr 2fr 1fr;gap:10px;margin-bottom:8px">
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Nama
                                            Siswa</label>
                                        <input type="text"
                                            :name="'isi[pemetaan_masalah_siswa][' + index + '][nama_siswa]'"
                                            x-model="m.nama_siswa" :required="tipe === 'harian'"
                                            placeholder="Nama lengkap..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Permasalahan</label>
                                        <input type="text"
                                            :name="'isi[pemetaan_masalah_siswa][' + index + '][permasalahan]'"
                                            x-model="m.permasalahan" :required="tipe === 'harian'"
                                            placeholder="Detail masalah..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Kategori</label>
                                        <select :name="'isi[pemetaan_masalah_siswa][' + index + '][kategori]'"
                                            x-model="m.kategori"
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                            <option value="Akademik">Akademik</option>
                                            <option value="Sikap">Sikap</option>
                                            <option value="Sosial">Sosial</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Solusi
                                            Pendek</label>
                                        <input type="text"
                                            :name="'isi[pemetaan_masalah_siswa][' + index + '][solusi_pendek]'"
                                            x-model="m.solusi_pendek" :required="tipe === 'harian'"
                                            placeholder="Solusi segera..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Monitoring
                                            Lanjutan</label>
                                        <input type="text"
                                            :name="'isi[pemetaan_masalah_siswa][' + index + '][monitoring_lanjutan]'"
                                            x-model="m.monitoring_lanjutan" :required="tipe === 'harian'"
                                            placeholder="Cara monitoring..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-brain" style="color:var(--purple)"></i> 4. Refleksi Guru</h4>
                    <div
                        style="display:flex;flex-direction:column;gap:10px;background:#fafafa;padding:12px;border-radius:var(--radius-sm);border:1px solid var(--border)">
                        <div>
                            <label style="font-size:12px;font-weight:600;color:var(--gray-500)">Materi
                                Tersampaikan</label>
                            <input type="text" name="isi[refleksi][materi_tersampaikan]"
                                x-model="refleksi.materi_tersampaikan" :required="tipe === 'harian'"
                                placeholder="Apakah materi tersampaikan seluruhnya? Jelaskan..."
                                style="width:100%;padding:8px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:var(--gray-500)">Target Tercapai</label>
                            <input type="text" name="isi[refleksi][target_tercapai]"
                                x-model="refleksi.target_tercapai" :required="tipe === 'harian'"
                                placeholder="Apakah target pembelajaran hari ini tercapai?..."
                                style="width:100%;padding:8px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:var(--gray-500)">Kendala
                                Terbesar</label>
                            <input type="text" name="isi[refleksi][kendala_terbesar]"
                                x-model="refleksi.kendala_terbesar" :required="tipe === 'harian'"
                                placeholder="Kendala terbesar KBM hari ini..."
                                style="width:100%;padding:8px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:var(--gray-500)">Strategi
                                Perbaikan</label>
                            <input type="text" name="isi[refleksi][strategi_perbaikan]"
                                x-model="refleksi.strategi_perbaikan" :required="tipe === 'harian'"
                                placeholder="Rencana/strategi untuk memperbaiki kendala..."
                                style="width:100%;padding:8px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:var(--gray-500)">Rencana Pertemuan
                                Berikutnya</label>
                            <input type="text" name="isi[refleksi][rencana_pertemuan]"
                                x-model="refleksi.rencana_pertemuan" :required="tipe === 'harian'"
                                placeholder="Fokus rencana untuk pertemuan berikutnya..."
                                style="width:100%;padding:8px;font-size:13px;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="tipe === 'mingguan'" style="display:flex;flex-direction:column;gap:20px">
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-calendar-alt" style="color:var(--green)"></i> 1. Rekapitulasi Pelaksanaan
                        Pembelajaran (Senin - Jumat)</h4>
                    <div style="overflow-x:auto;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:600px">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border);background:#fafafa">
                                    <th style="padding:8px;text-align:left">Hari</th>
                                    <th style="padding:8px;text-align:left">Materi</th>
                                    <th style="padding:8px;text-align:left;width:90px">Kehadiran</th>
                                    <th style="padding:8px;text-align:left;width:95px">Ketuntasan</th>
                                    <th style="padding:8px;text-align:left;width:90px">HOTS</th>
                                    <th style="padding:8px;text-align:left">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(day, index) in rekap_pembelajaran" :key="index">
                                    <tr style="border-bottom:1px solid #eee">
                                        <td style="padding:8px">
                                            <strong x-text="day.hari"></strong>
                                            <input type="hidden"
                                                :name="'isi[rekap_pembelajaran][' + index + '][hari]'"
                                                :value="day.hari">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[rekap_pembelajaran][' + index + '][materi]'"
                                                x-model="day.materi" :required="tipe === 'mingguan'"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[rekap_pembelajaran][' + index + '][kehadiran]'"
                                                x-model="day.kehadiran" :required="tipe === 'mingguan'"
                                                placeholder="e.g. 95%"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[rekap_pembelajaran][' + index + '][ketuntasan]'"
                                                x-model="day.ketuntasan" :required="tipe === 'mingguan'"
                                                placeholder="e.g. >= 75%"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[rekap_pembelajaran][' + index + '][hots]'"
                                                x-model="day.hots" :required="tipe === 'mingguan'"
                                                placeholder="e.g. Baik"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[rekap_pembelajaran][' + index + '][catatan]'"
                                                x-model="day.catatan" placeholder="Catatan..."
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-chart-line" style="color:var(--blue)"></i> 2. Evaluasi Akademik</h4>
                    <div style="overflow-x:auto;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:400px">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border);background:#fafafa">
                                    <th style="padding:8px;text-align:left">Aspek</th>
                                    <th style="padding:8px;text-align:left;width:110px">Target</th>
                                    <th style="padding:8px;text-align:left;width:110px">Capaian</th>
                                    <th style="padding:8px;text-align:left">Analisis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $evals = [
                                        'ketuntasan_materi' => ['Ketuntasan Materi', '>= 75%'],
                                        'keaktifan_siswa' => ['Keaktifan Siswa', '>= 80%'],
                                        'hots' => ['HOTS', 'Berkembang'],
                                        'literasi' => ['Literasi', 'Berkembang'],
                                        'numerasi' => ['Numerasi', 'Berkembang'],
                                    ];
                                @endphp
                                @foreach ($evals as $key => $val)
                                    <tr style="border-bottom:1px solid #eee">
                                        <td style="padding:8px"><strong>{{ $val[0] }}</strong></td>
                                        <td style="padding:8px">
                                            <span>{{ $val[1] }}</span>
                                            <input type="hidden"
                                                name="isi[evaluasi_akademik][{{ $key }}][target]"
                                                value="{{ $val[1] }}">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                name="isi[evaluasi_akademik][{{ $key }}][capaian]"
                                                x-model="evaluasi_akademik.{{ $key }}.capaian"
                                                :required="tipe === 'mingguan'"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                name="isi[evaluasi_akademik][{{ $key }}][analisis]"
                                                x-model="evaluasi_akademik.{{ $key }}.analisis"
                                                placeholder="Hasil analisis..."
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-exclamation-triangle" style="color:var(--orange)"></i> 3. Analisis Kendala
                        Pekanan (Bila Ada)</h4>
                    <div style="margin-bottom:10px">
                        <button type="button"
                            @click="analisis_kendala_mingguan.push({jenis_kendala: 'Akademik', uraian_masalah: '', frekuensi: '', dampak: '', solusi_yang_dilakukan: '', hasil: ''})"
                            class="btn-small outline" style="cursor:pointer">
                            <i class="fas fa-plus"></i> Tambah Kendala Pekanan
                        </button>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <template x-for="(k, index) in analisis_kendala_mingguan" :key="index">
                            <div
                                style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;background:#fff;position:relative">
                                <button type="button" @click="analisis_kendala_mingguan.splice(index, 1)"
                                    style="position:absolute;right:10px;top:10px;background:none;border:none;color:red;cursor:pointer">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                                <div style="display:grid;grid-template-columns:1fr 2fr 1fr;gap:10px;margin-bottom:8px">
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Jenis
                                            Kendala</label>
                                        <select :name="'isi[analisis_kendala][' + index + '][jenis_kendala]'"
                                            x-model="k.jenis_kendala"
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                            <option value="Akademik">Akademik</option>
                                            <option value="Perilaku">Perilaku</option>
                                            <option value="Motivasi">Motivasi</option>
                                            <option value="Media pembelajaran">Media pembelajaran</option>
                                            <option value="Orang tua">Orang tua</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Uraian
                                            Masalah</label>
                                        <input type="text"
                                            :name="'isi[analisis_kendala][' + index + '][uraian_masalah]'"
                                            x-model="k.uraian_masalah" :required="tipe === 'mingguan'"
                                            placeholder="Masalah..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Frekuensi</label>
                                        <input type="text"
                                            :name="'isi[analisis_kendala][' + index + '][frekuensi]'"
                                            x-model="k.frekuensi" :required="tipe === 'mingguan'"
                                            placeholder="Berapa kali..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Dampak</label>
                                        <input type="text" :name="'isi[analisis_kendala][' + index + '][dampak]'"
                                            x-model="k.dampak" :required="tipe === 'mingguan'"
                                            placeholder="Dampak..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Solusi Yang
                                            Dilakukan</label>
                                        <input type="text"
                                            :name="'isi[analisis_kendala][' + index + '][solusi_yang_dilakukan]'"
                                            x-model="k.solusi_yang_dilakukan" :required="tipe === 'mingguan'"
                                            placeholder="Solusi..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Hasil</label>
                                        <input type="text" :name="'isi[analisis_kendala][' + index + '][hasil]'"
                                            x-model="k.hasil" :required="tipe === 'mingguan'" placeholder="Hasil..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-users-cog" style="color:var(--teal)"></i> 4. Pemetaan Siswa Pekanan (Bila
                        Ada)</h4>
                    <div style="margin-bottom:10px">
                        <button type="button"
                            @click="pemetaan_siswa_mingguan.push({kategori: 'Akademik rendah', nama_siswa: '', permasalahan: '', program_solusi: '', target_pekan_berikutnya: ''})"
                            class="btn-small outline" style="cursor:pointer">
                            <i class="fas fa-plus"></i> Tambah Siswa
                        </button>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <template x-for="(s, index) in pemetaan_siswa_mingguan" :key="index">
                            <div
                                style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;background:#fff;position:relative">
                                <button type="button" @click="pemetaan_siswa_mingguan.splice(index, 1)"
                                    style="position:absolute;right:10px;top:10px;background:none;border:none;color:red;cursor:pointer">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                                <div
                                    style="display:grid;grid-template-columns:1.5fr 1.5fr 1fr;gap:10px;margin-bottom:8px">
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Nama
                                            Siswa</label>
                                        <input type="text" :name="'isi[pemetaan_siswa][' + index + '][nama_siswa]'"
                                            x-model="s.nama_siswa" :required="tipe === 'mingguan'"
                                            placeholder="Nama siswa..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Permasalahan</label>
                                        <input type="text"
                                            :name="'isi[pemetaan_siswa][' + index + '][permasalahan]'"
                                            x-model="s.permasalahan" :required="tipe === 'mingguan'"
                                            placeholder="Permasalahan..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Kategori</label>
                                        <select :name="'isi[pemetaan_siswa][' + index + '][kategori]'"
                                            x-model="s.kategori"
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                            <option value="Akademik rendah">Akademik rendah</option>
                                            <option value="Pasif">Pasif</option>
                                            <option value="HOTS rendah">HOTS rendah</option>
                                            <option value="Disiplin">Disiplin</option>
                                            <option value="Sosial">Sosial</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px">
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Program
                                            Solusi</label>
                                        <input type="text"
                                            :name="'isi[pemetaan_siswa][' + index + '][program_solusi]'"
                                            x-model="s.program_solusi" :required="tipe === 'mingguan'"
                                            placeholder="Program solusi..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Target
                                            Pekan Berikutnya</label>
                                        <input type="text"
                                            :name="'isi[pemetaan_siswa][' + index + '][target_pekan_berikutnya]'"
                                            x-model="s.target_pekan_berikutnya" :required="tipe === 'mingguan'"
                                            placeholder="Target..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-clipboard-check" style="color:var(--purple)"></i> 5. Program Tindak Lanjut
                    </h4>
                    <div style="margin-bottom:10px">
                        <button type="button"
                            @click="tindak_lanjut.push({program: 'Remedial', sasaran: '', bentuk_kegiatan: '', jadwal: ''})"
                            class="btn-small outline" style="cursor:pointer">
                            <i class="fas fa-plus"></i> Tambah Tindak Lanjut
                        </button>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <template x-for="(t, index) in tindak_lanjut" :key="index">
                            <div
                                style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;background:#fff;position:relative">
                                <button type="button" @click="tindak_lanjut.splice(index, 1)"
                                    style="position:absolute;right:10px;top:10px;background:none;border:none;color:red;cursor:pointer">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                                <div style="display:grid;grid-template-columns:1fr 1fr 1.5fr 1fr;gap:10px">
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Program</label>
                                        <select :name="'isi[tindak_lanjut][' + index + '][program]'"
                                            x-model="t.program"
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                            <option value="Remedial">Remedial</option>
                                            <option value="Pengayaan">Pengayaan</option>
                                            <option value="Pendampingan">Pendampingan</option>
                                            <option value="Konseling">Konseling</option>
                                            <option value="Home communication">Home communication</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Sasaran</label>
                                        <input type="text" :name="'isi[tindak_lanjut][' + index + '][sasaran]'"
                                            x-model="t.sasaran" :required="tipe === 'mingguan'"
                                            placeholder="Sasaran..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;font-weight:700;color:var(--gray-500)">Bentuk
                                            Kegiatan</label>
                                        <input type="text"
                                            :name="'isi[tindak_lanjut][' + index + '][bentuk_kegiatan]'"
                                            x-model="t.bentuk_kegiatan" :required="tipe === 'mingguan'"
                                            placeholder="Kegiatan..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div>
                                        <label
                                            style="font-size:11px;font-weight:700;color:var(--gray-500)">Jadwal</label>
                                        <input type="text" :name="'isi[tindak_lanjut][' + index + '][jadwal]'"
                                            x-model="t.jadwal" :required="tipe === 'mingguan'"
                                            placeholder="Jadwal..."
                                            style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div x-show="tipe === 'bulanan'" style="display:flex;flex-direction:column;gap:20px">
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-chart-bar" style="color:var(--blue)"></i> 1. Capaian Belajar Bulanan</h4>
                    <div style="overflow-x:auto;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:500px">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border);background:#fafafa">
                                    <th style="padding:8px;text-align:left">Elemen CP</th>
                                    <th style="padding:8px;text-align:left;width:95px">Target</th>
                                    <th style="padding:8px;text-align:left;width:95px">Capaian</th>
                                    <th style="padding:8px;text-align:left;width:100px">Persentase</th>
                                    <th style="padding:8px;text-align:left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(cp, index) in capaian_belajar_bulanan" :key="index">
                                    <tr style="border-bottom:1px solid #eee">
                                        <td style="padding:8px">
                                            <strong x-text="cp.elemen_cp"></strong>
                                            <input type="hidden"
                                                :name="'isi[capaian_belajar_bulanan][' + index + '][elemen_cp]'"
                                                :value="cp.elemen_cp">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[capaian_belajar_bulanan][' + index + '][target]'"
                                                x-model="cp.target" :required="tipe === 'bulanan'"
                                                placeholder="e.g. 75"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[capaian_belajar_bulanan][' + index + '][capaian]'"
                                                x-model="cp.capaian" :required="tipe === 'bulanan'"
                                                placeholder="e.g. 80"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[capaian_belajar_bulanan][' + index + '][persentase]'"
                                                x-model="cp.persentase" :required="tipe === 'bulanan'"
                                                placeholder="e.g. 100%"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[capaian_belajar_bulanan][' + index + '][keterangan]'"
                                                x-model="cp.keterangan" placeholder="Keterangan..."
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-exclamation-triangle" style="color:var(--orange)"></i> 2. Evaluasi dan
                        Kendala Bulanan</h4>
                    <div style="overflow-x:auto;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:600px">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border);background:#fafafa">
                                    <th style="padding:8px;text-align:left;width:110px">Bidang</th>
                                    <th style="padding:8px;text-align:left">Permasalahan</th>
                                    <th style="padding:8px;text-align:left">Analisis Penyebab</th>
                                    <th style="padding:8px;text-align:left">Dampak</th>
                                    <th style="padding:8px;text-align:left">Solusi Dilakukan</th>
                                    <th style="padding:8px;text-align:left;width:95px">Efektivitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(eb, index) in evaluasi_dan_kendala_bulanan" :key="index">
                                    <tr style="border-bottom:1px solid #eee">
                                        <td style="padding:8px">
                                            <strong x-text="eb.bidang"></strong>
                                            <input type="hidden"
                                                :name="'isi[evaluasi_dan_kendala][' + index + '][bidang]'"
                                                :value="eb.bidang">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[evaluasi_dan_kendala][' + index + '][permasalahan]'"
                                                x-model="eb.permasalahan" placeholder="Permasalahan..."
                                                style="width:100%;padding:5px;font-size:11px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[evaluasi_dan_kendala][' + index + '][analisis_penyebab]'"
                                                x-model="eb.analisis_penyebab" placeholder="Penyebab..."
                                                style="width:100%;padding:5px;font-size:11px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[evaluasi_dan_kendala][' + index + '][dampak]'"
                                                x-model="eb.dampak" placeholder="Dampak..."
                                                style="width:100%;padding:5px;font-size:11px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[evaluasi_dan_kendala][' + index + '][solusi_dilakukan]'"
                                                x-model="eb.solusi_dilakukan" placeholder="Solusi..."
                                                style="width:100%;padding:5px;font-size:11px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[evaluasi_dan_kendala][' + index + '][efektivitas]'"
                                                x-model="eb.efektivitas" placeholder="Efektif?"
                                                style="width:100%;padding:5px;font-size:11px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-users" style="color:var(--teal)"></i> 3. Analisis Kategori Siswa</h4>
                    <div style="overflow-x:auto;border:1px solid var(--border);border-radius:var(--radius-sm)">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:500px">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border);background:#fafafa">
                                    <th style="padding:8px;text-align:left;width:150px">Kategori Siswa</th>
                                    <th style="padding:8px;text-align:left;width:80px">Jumlah</th>
                                    <th style="padding:8px;text-align:left">Permasalahan Dominan</th>
                                    <th style="padding:8px;text-align:left">Program Solusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(asb, index) in analisis_siswa_bulanan" :key="index">
                                    <tr style="border-bottom:1px solid #eee">
                                        <td style="padding:8px">
                                            <strong x-text="asb.kategori_siswa"></strong>
                                            <input type="hidden"
                                                :name="'isi[analisis_siswa][' + index + '][kategori_siswa]'"
                                                :value="asb.kategori_siswa">
                                        </td>
                                        <td style="padding:4px"><input type="number"
                                                :name="'isi[analisis_siswa][' + index + '][jumlah]'"
                                                x-model="asb.jumlah" min="0" :required="tipe === 'bulanan'"
                                                placeholder="0"
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[analisis_siswa][' + index + '][permasalahan_dominan]'"
                                                x-model="asb.permasalahan_dominan" placeholder="Permasalahan utama..."
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                        <td style="padding:4px"><input type="text"
                                                :name="'isi[analisis_siswa][' + index + '][program_solusi]'"
                                                x-model="asb.program_solusi" placeholder="Program solusi..."
                                                style="width:100%;padding:6px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:10px"><i
                            class="fas fa-business-time" style="color:var(--purple)"></i> 4. Pemetaan Solusi Jangka
                        Pendek &amp; Menengah</h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
                        <div
                            style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;background:#fafafa">
                            <strong style="font-size:12px;display:block;margin-bottom:8px;color:var(--gray-600)">Jangka
                                Pendek (1 - 3 Bulan)</strong>
                            <div style="display:flex;flex-direction:column;gap:6px">
                                <div>
                                    <label style="font-size:10px;font-weight:700;color:var(--gray-500)">Masalah</label>
                                    <input type="text" name="isi[pemetaan_masalah_jangka_pendek][0][masalah]"
                                        x-model="pemetaan_masalah_jangka_pendek[0].masalah"
                                        :required="tipe === 'bulanan'" placeholder="Masalah..."
                                        style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700;color:var(--gray-500)">Solusi</label>
                                    <input type="text" name="isi[pemetaan_masalah_jangka_pendek][0][solusi]"
                                        x-model="pemetaan_masalah_jangka_pendek[0].solusi"
                                        :required="tipe === 'bulanan'" placeholder="Solusi..."
                                        style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                </div>
                                <div style="display:flex;gap:6px">
                                    <div style="flex:2">
                                        <label style="font-size:10px;font-weight:700;color:var(--gray-500)">PIC</label>
                                        <input type="text" name="isi[pemetaan_masalah_jangka_pendek][0][pic]"
                                            x-model="pemetaan_masalah_jangka_pendek[0].pic"
                                            :required="tipe === 'bulanan'" placeholder="PIC..."
                                            style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div style="flex:1">
                                        <label
                                            style="font-size:10px;font-weight:700;color:var(--gray-500)">Target</label>
                                        <input type="text"
                                            name="isi[pemetaan_masalah_jangka_pendek][0][target_waktu]"
                                            x-model="pemetaan_masalah_jangka_pendek[0].target_waktu"
                                            :required="tipe === 'bulanan'" placeholder="Waktu..."
                                            style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            style="border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px;background:#fafafa">
                            <strong style="font-size:12px;display:block;margin-bottom:8px;color:var(--gray-600)">Jangka
                                Menengah (3 - 12 Bulan)</strong>
                            <div style="display:flex;flex-direction:column;gap:6px">
                                <div>
                                    <label style="font-size:10px;font-weight:700;color:var(--gray-500)">Masalah</label>
                                    <input type="text" name="isi[pemetaan_masalah_jangka_menengah][0][masalah]"
                                        x-model="pemetaan_masalah_jangka_menengah[0].masalah"
                                        :required="tipe === 'bulanan'" placeholder="Masalah..."
                                        style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700;color:var(--gray-500)">Program
                                        Strategis</label>
                                    <input type="text"
                                        name="isi[pemetaan_masalah_jangka_menengah][0][program_strategis]"
                                        x-model="pemetaan_masalah_jangka_menengah[0].program_strategis"
                                        :required="tipe === 'bulanan'" placeholder="Program..."
                                        style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                </div>
                                <div style="display:flex;gap:6px">
                                    <div style="flex:1">
                                        <label
                                            style="font-size:10px;font-weight:700;color:var(--gray-500)">Target</label>
                                        <input type="text" name="isi[pemetaan_masalah_jangka_menengah][0][target]"
                                            x-model="pemetaan_masalah_jangka_menengah[0].target"
                                            :required="tipe === 'bulanan'" placeholder="Target..."
                                            style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                    <div style="flex:1">
                                        <label style="font-size:10px;font-weight:700;color:var(--gray-500)">Evaluasi
                                            Berkala</label>
                                        <input type="text"
                                            name="isi[pemetaan_masalah_jangka_menengah][0][evaluasi_berkala]"
                                            x-model="pemetaan_masalah_jangka_menengah[0].evaluasi_berkala"
                                            :required="tipe === 'bulanan'" placeholder="Evaluasi..."
                                            style="width:100%;padding:5px;font-size:12px;border:1px solid var(--border);border-radius:3px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-top:20px">
                <h4 style="font-size:13px;font-weight:700;color:var(--gray-500);margin-bottom:8px">Catatan Umum /
                    Keterangan Tambahan</h4>
                <textarea name="isi[catatan_umum]" x-model="catatan_umum" rows="3"
                    placeholder="Tuliskan catatan tambahan (atau isi teks laporan di sini jika Anda tidak ingin menggunakan form terstruktur)..."
                    style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;line-height:1.5;font-family:var(--font)"></textarea>
            </div>
            <button type="submit" class="btn-login"
                style="margin-top:20px;cursor:pointer;border:none;width:auto;padding:10px 24px"><i
                    class="fas fa-paper-plane"></i> Kirim Laporan Mengajar</button>
        </form>
    </div>
    <div class="card">
        <div class="card-header" style="background:#fcfcfc;border-bottom:1px solid var(--border)">
            <h3><i class="fas fa-history" style="color:var(--orange)"></i> Riwayat Laporan Mengajar Saya</h3>
        </div>
        <div class="table-wrap">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#fafafa">
                        <th style="padding:10px 12px;text-align:left">Tanggal</th>
                        <th style="padding:10px 12px;text-align:left">Tipe</th>
                        <th style="padding:10px 12px;text-align:left">Ringkasan Laporan</th>
                        <th style="width:100px;padding:10px 12px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporans as $l)
                        @php
                            $isiTeks = '';
                            if (is_array($l->isi)) {
                                if ($l->tipe === 'harian') {
                                    $kList = [];
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
                        @endphp
                        <tr style="border-bottom:1px solid #eee">
                            <td style="padding:10px 12px"><strong>{{ $l->tanggal->format('d M Y') }}</strong></td>
                            <td style="padding:10px 12px">
                                <span
                                    class="badge light {{ $l->tipe === 'harian' ? 'blue' : ($l->tipe === 'mingguan' ? 'green' : 'purple') }}">
                                    {{ ucfirst($l->tipe) }}
                                </span>
                            </td>
                            <td
                                style="font-size:12px;line-height:1.6;max-width:500px;white-space:pre-line;padding:10px 12px">
                                {{ $isiTeks }}</td>
                            <td style="padding:10px 12px">
                                <button type="button"
                                    @click="loadLaporan('{{ $l->tipe }}', '{{ $l->tanggal->format('Y-m-d') }}', {{ json_encode($l->isi) }})"
                                    class="btn-small outline"
                                    style="border-radius:var(--radius-sm);cursor:pointer;font-weight:600;padding:4px 8px">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:var(--gray-400);padding:20px">Belum ada
                                laporan mengajar yang dikirim.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@if (session('success'))
    <div
        style="position:fixed;bottom:20px;right:20px;background:var(--green);color:#fff;padding:14px 20px;border-radius:var(--radius-sm);font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
