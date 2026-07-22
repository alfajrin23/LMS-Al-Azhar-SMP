<?php

namespace Database\Seeders;

use App\Models\AdministrasiGuruChecklist;
use App\Models\Badge;
use App\Models\CatatanWali;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\JurnalSikap;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\KelasQuran;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\MateriApprovalHistory;
use App\Models\Nilai;
use App\Models\OrangTua;
use App\Models\Pengumuman;
use App\Models\Pesan;
use App\Models\ProgramPengayaan;
use App\Models\ProgramRemedial;
use App\Models\Rapor;
use App\Models\RaporItem;
use App\Models\Remedial;
use App\Models\Siswa;
use App\Models\SiswaBadge;
use App\Models\TahfidzProgress;
use App\Models\TahfidzSetoran;
use App\Models\TahsinSetoran;
use App\Models\Tugas;
use App\Models\User;
use App\Models\Workbook;
use App\Models\WorkbookJawaban;
use App\Models\WorkbookSoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Revisi1Seeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::firstOrCreate(
            ['kode_kelas' => '7-DEMO'],
            ['nama_kelas' => 'Kelas 7 Demo', 'jenjang' => 'SMP']
        );
        $kelasQuran = KelasQuran::firstOrCreate(
            ['nama_kelas' => 'Kelas Quran Demo SMPIT'],
            ['jenjang' => 'SMP', 'kategori' => 'Ikhwan', 'tingkat' => 'Demo']
        );

        $mapelMtk = Mapel::firstOrCreate(['nama_mapel' => 'Matematika'], ['kode' => 'MTK']);
        $mapelIng = Mapel::firstOrCreate(['nama_mapel' => 'Bahasa Inggris'], ['kode' => 'ING']);
        $mapelTah = Mapel::firstOrCreate(['nama_mapel' => 'Tahfidzul Qur\'an'], ['kode' => 'TAH']);

        $guruUser = $this->user('Guru Demo SMPIT', 'guru.demo@alazharjayaindonesia.sch.id', 'guru');
        $guru = Guru::updateOrCreate(
            ['user_id' => $guruUser->id],
            [
                'nip' => 'GURU-DEMO-001',
                'nama' => 'Guru Demo SMPIT',
                'mapel_id' => $mapelMtk->id,
                'status' => 'aktif',
            ]
        );
        $guru->mapels()->syncWithoutDetaching([$mapelMtk->id, $mapelIng->id, $mapelTah->id]);
        $kelas->update(['guru_id' => $guru->id]);

        $siswaUser = $this->user('Ahmad Fikri Demo', 'siswa.demo@alazharjayaindonesia.sch.id', 'siswa_smp');
        $siswa = Siswa::updateOrCreate(
            ['user_id' => $siswaUser->id],
            [
                'nis' => 'SISWA-DEMO-001',
                'nama' => 'Ahmad Fikri Demo',
                'kelas_id' => $kelas->id,
                'kelas_quran_id' => $kelasQuran->id,
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => '2012-07-10',
                'alamat' => 'Alamat Demo LMS Al Azhar',
                'nama_ayah' => 'Ayah Demo',
                'nama_ibu' => 'Ibu Demo',
                'status' => 'aktif',
            ]
        );

        $ortuUser = $this->user('Orang Tua Ahmad Fikri Demo', 'ortu.demo@alazharjayaindonesia.sch.id', 'orang_tua');
        $ortu = OrangTua::updateOrCreate(
            ['user_id' => $ortuUser->id],
            ['nama' => 'Orang Tua Ahmad Fikri Demo', 'no_telp' => '080000000000', 'alamat' => 'Alamat Orang Tua Demo']
        );
        $ortu->siswa()->syncWithoutDetaching([$siswa->id]);

        foreach ([
            ['Senin', '07:30', '08:10', $mapelMtk->id],
            ['Selasa', '09:00', '09:40', $mapelIng->id],
            ['Rabu', '10:00', '10:40', $mapelTah->id],
        ] as [$hari, $mulai, $selesai, $mapelId]) {
            Jadwal::updateOrCreate(
                ['kelas_id' => $kelas->id, 'hari' => $hari, 'jam_mulai' => $mulai],
                ['mapel_id' => $mapelId, 'guru_id' => $guru->id, 'jam_selesai' => $selesai]
            );
        }

        $this->seedBahanAjar($guru, $kelas, $mapelMtk, $guruUser);
        $this->seedAttendance($siswa, $kelas, $mapelMtk, $guru);
        $this->seedGradesAndReports($siswa, $kelas, $guruUser, $mapelMtk, $mapelIng, $mapelTah);
        $this->seedQuran($siswa, $kelas, $kelasQuran, $guru, $guruUser);
        $this->seedCommunication($siswa, $guruUser, $siswaUser, $ortuUser);
        $this->seedTeacherAdministration($guru, $siswa, $kelas, $mapelMtk, $guruUser);
        $this->seedAssignments($guru, $siswa, $kelas, $mapelMtk, $guruUser);
    }

    private function user(string $name, string $email, string $role): User
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => $role,
            ]);
        }

        $user->update(['name' => $name, 'role' => $role]);

        return $user;
    }

    private function seedBahanAjar(Guru $guru, Kelas $kelas, Mapel $mapel, User $actor): void
    {
        Storage::disk('public')->put('demo/bahan-ajar-demo.pdf', "%PDF-1.4\n% Demo Bahan Ajar LMS Al Azhar\n");

        $rows = [
            ['Kompetensi Inti Demo Matematika', 'kompetensi_inti', 'KI-DEMO-01', 'draft'],
            ['Kompetensi Dasar Demo Matematika', 'kompetensi_dasar', 'KD-DEMO-01', 'pending'],
            ['Alur Tahapan Pembelajaran Demo Matematika', 'alur_tahapan_pembelajaran', 'ATP-DEMO-01', 'approved'],
        ];

        foreach ($rows as [$judul, $kategori, $kode, $status]) {
            $materi = Materi::updateOrCreate(
                ['guru_id' => $guru->id, 'kode' => $kode],
                [
                    'judul' => $judul,
                    'deskripsi' => 'Dokumen Demo untuk '.$judul.' tahun ajaran 2026/2027.',
                    'file_path' => 'demo/bahan-ajar-demo.pdf',
                    'tipe' => 'bahan_ajar',
                    'kategori' => $kategori,
                    'isi' => 'Isi ringkas Demo yang dapat direview oleh Kepala Sekolah.',
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'tahun_ajaran' => '2026/2027',
                    'semester' => 'Ganjil',
                    'versi' => 1,
                    'status' => $status,
                    'submitted_at' => $status === 'draft' ? null : now(),
                    'reviewed_at' => $status === 'approved' ? now() : null,
                    'reviewed_by' => $status === 'approved' ? $actor->id : null,
                    'catatan_reviewer' => $status === 'approved' ? 'Demo approved untuk publikasi.' : null,
                ]
            );

            MateriApprovalHistory::firstOrCreate(
                ['materi_id' => $materi->id, 'action' => $status === 'draft' ? 'created' : 'submitted', 'status_to' => $status === 'draft' ? 'draft' : 'pending'],
                ['actor_id' => $actor->id, 'status_from' => null, 'catatan' => 'Seeder Demo']
            );

            if ($status === 'approved') {
                MateriApprovalHistory::firstOrCreate(
                    ['materi_id' => $materi->id, 'action' => 'approved', 'status_to' => 'approved'],
                    ['actor_id' => $actor->id, 'status_from' => 'pending', 'catatan' => 'Seeder Demo approved']
                );
            }
        }
    }

    private function seedAttendance(Siswa $siswa, Kelas $kelas, Mapel $mapel, Guru $guru): void
    {
        $statuses = [
            '2026-07-01' => 'hadir',
            '2026-07-02' => 'hadir',
            '2026-07-03' => 'alpha',
            '2026-07-06' => 'hadir',
            '2026-07-07' => 'hadir',
            '2026-07-08' => 'sakit',
            '2026-07-09' => 'hadir',
            '2026-07-10' => 'hadir',
            '2026-07-13' => 'hadir',
            '2026-07-14' => 'izin',
            '2026-07-15' => 'hadir',
            '2026-07-16' => 'hadir',
            '2026-07-17' => 'hadir',
            '2026-07-20' => 'alpha',
            '2026-07-21' => 'hadir',
            '2026-07-22' => 'hadir',
            '2026-07-23' => 'hadir',
            '2026-07-24' => 'hadir',
        ];

        foreach ($statuses as $date => $status) {
            Kehadiran::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => $date, 'mapel_id' => $mapel->id, 'pertemuan' => 'Demo'],
                [
                    'kelas_id' => $kelas->id,
                    'guru_id' => $guru->id,
                    'status' => $status,
                    'keterangan' => $status === 'hadir' ? 'Hadir Demo' : ucfirst($status).' Demo',
                    'tahun_ajaran' => '2026/2027',
                    'semester' => 'Ganjil',
                ]
            );
        }
    }

    private function seedGradesAndReports(Siswa $siswa, Kelas $kelas, User $actor, Mapel ...$mapels): void
    {
        foreach ($mapels as $i => $mapel) {
            Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'mapel_id' => $mapel->id,
                    'jenis_nilai' => 'biasa',
                    'jenis_rapor' => 'akademik',
                    'tahun_ajaran' => '2025/2026',
                    'semester' => 'Genap',
                    'lingkup_materi' => 'Lingkup Materi Demo '.($i + 1),
                ],
                [
                    'nilai' => 86 + $i,
                    'nilai_sumatif' => 84 + $i,
                    'tujuan_pembelajaran' => 'Tujuan Pembelajaran Demo '.$mapel->nama_mapel,
                    'tp_scores' => ['TP 1' => 86, 'TP 2' => 88, 'TP 3' => 90, 'TP 4' => 87],
                    'tugas_scores' => ['Tugas 1' => 86, 'Tugas 2' => 88, 'Tugas 3' => 90],
                    'sumatif_scores' => ['Sumatif 1' => 84, 'Sumatif 2' => 87],
                    'capaian_kompetensi' => 'Ananda Demo menunjukkan pemahaman baik pada '.$mapel->nama_mapel.'.',
                    'kompetensi_dikuasai' => 'Mampu menyelesaikan tugas utama.',
                    'kompetensi_perlu_ditingkatkan' => 'Perlu meningkatkan ketelitian pada soal cerita.',
                    'catatan' => 'Nilai akademik Demo.',
                ]
            );
        }

        $academic = $this->rapor($siswa, 'akademik', '2025/2026', 'Genap', $actor, 'Ananda Demo menunjukkan perkembangan akademik yang baik.');
        foreach ($mapels as $i => $mapel) {
            RaporItem::updateOrCreate(
                ['rapor_id' => $academic->id, 'kategori' => 'akademik', 'komponen' => $mapel->nama_mapel],
                ['mapel_id' => $mapel->id, 'nilai' => 86 + $i, 'predikat' => 'B', 'deskripsi' => 'Capaian kompetensi Demo untuk '.$mapel->nama_mapel.'.']
            );
        }
        RaporItem::updateOrCreate(
            ['rapor_id' => $academic->id, 'kategori' => 'ekstrakurikuler', 'komponen' => 'Pramuka Demo'],
            ['nilai' => null, 'predikat' => 'Berkembang', 'deskripsi' => 'Aktif mengikuti kegiatan Demo.']
        );

        $english = $this->rapor($siswa, 'english', '2026/2027', 'Ganjil', $actor, 'Teacher comment Demo: active and confident.');
        foreach (['Listening', 'Speaking', 'Reading', 'Writing', 'Vocabulary', 'Grammar', 'Pronunciation', 'Participation', 'Final Score', 'Teacher Comment'] as $i => $component) {
            RaporItem::updateOrCreate(
                ['rapor_id' => $english->id, 'kategori' => 'english', 'komponen' => $component],
                [
                    'nilai' => $component === 'Teacher Comment' ? null : 82 + min($i, 8),
                    'predikat' => $component === 'Teacher Comment' ? null : 'Good',
                    'deskripsi' => $component === 'Teacher Comment' ? 'Ahmad Fikri Demo participates well in English class.' : 'Komponen English Demo.',
                ]
            );
        }
    }

    private function seedQuran(Siswa $siswa, Kelas $kelas, KelasQuran $kelasQuran, Guru $guru, User $actor): void
    {
        TahfidzProgress::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'kelas_id' => $kelas->id,
                'kelas_quran_id' => $kelasQuran->id,
                'surah' => 'Al-Falaq',
                'ayat_mulai' => 1,
                'ayat_selesai' => 5,
                'juz_dihafal' => 1,
                'total_ayat' => 42,
                'progress_percent' => 77.78,
                'target_deskripsi' => 'Murojaah An-Naas sampai Al-Falaq',
                'tingkat_ummi' => 'UMMI Jilid 2',
                'posisi_tilawah' => 'Jilid 2 Halaman 16',
                'hafalan_terakhir' => 'Al-Falaq',
                'kelancaran' => 88,
                'tajwid' => 86,
                'makhroj' => 84,
                'adab' => 90,
                'predikat' => 'Jayyid',
                'target_berikutnya' => 'Al-Ikhlas',
                'tanggal_pertemuan_berikutnya' => '2026-07-27',
                'status' => 'berproses',
                'catatan' => 'Demo: bacaan semakin lancar, perlu murojaah pada makhroj tertentu.',
                'updated_by' => $actor->id,
            ]
        );

        foreach ([
            ['2026-07-08', 'An-Naas', 1, 6, 88],
            ['2026-07-15', 'Al-Falaq', 1, 5, 90],
        ] as [$date, $surah, $start, $end, $nilai]) {
            TahfidzSetoran::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => $date, 'surah' => $surah, 'ayat_mulai' => $start],
                [
                    'guru_id' => $guru->id,
                    'tanggal_berikutnya' => '2026-07-27',
                    'ayat_selesai' => $end,
                    'jumlah_ayat' => $end - $start + 1,
                    'status' => 'baru',
                    'nilai' => $nilai,
                    'catatan_guru' => 'Setoran Tahfidz Demo.',
                    'tahun_ajaran' => '2026/2027',
                    'semester' => 'Ganjil',
                ]
            );
        }

        TahsinSetoran::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tanggal' => '2026-07-16', 'materi_tahsin' => 'Makharijul huruf Demo'],
            [
                'guru_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'jilid_halaman' => 'Jilid 2 Halaman 16',
                'nilai' => 88,
                'catatan' => 'Tahsin Demo: kelancaran baik.',
                'status' => 'proses',
                'tahun_ajaran' => '2026/2027',
                'semester' => 'Ganjil',
            ]
        );

        $quran = $this->rapor($siswa, 'quran', '2026/2027', 'Ganjil', $actor, 'Alhamdulillah, capaian Quran Ananda Demo semakin baik. Tetap semangat murojaah.');
        foreach (['Ketelitian', 'Kelancaran', 'Makhorijul Huruf dan Sifatul Huruf', 'Mad/Bacaan Panjang', 'Ghunnah/Bacaan Dengung', 'Qolqolah/Bacaan Memantul', 'Idzhar/Bacaan Jelas', 'Waqof Wal Ibtida', 'Materi Ghorib', 'Materi Tajwid', 'Kehadiran', 'Adab dan Perilaku'] as $component) {
            RaporItem::updateOrCreate(
                ['rapor_id' => $quran->id, 'kategori' => 'quran_reading', 'komponen' => $component],
                ['predikat' => in_array($component, ['Kehadiran', 'Adab dan Perilaku'], true) ? 'A' : 'B', 'deskripsi' => 'Penilaian Quran Demo.']
            );
        }

        foreach ($this->juz30Surahs() as $i => $surah) {
            RaporItem::updateOrCreate(
                ['rapor_id' => $quran->id, 'kategori' => 'quran_surah', 'komponen' => $surah],
                ['predikat' => $i < 3 ? 'A' : ($i < 8 ? 'B' : null), 'deskripsi' => 'Hafalan Juz 30 Demo.']
            );
        }
    }

    private function seedCommunication(Siswa $siswa, User $guruUser, User $siswaUser, User $ortuUser): void
    {
        $messages = [
            [$guruUser->id, $ortuUser->id, 'Kehadiran Demo', 'Kehadiran', 'Ahmad Fikri Demo tercatat dua kali terlambat. Mohon pendampingan dari rumah.'],
            [$ortuUser->id, $guruUser->id, 'Re: Kehadiran Demo', 'Kehadiran', 'Terima kasih informasinya, kami akan mengatur keberangkatan lebih awal.'],
            [$guruUser->id, $ortuUser->id, 'Perkembangan Tahfidz Demo', 'Tahsin/Tahfidz', 'Setoran Al-Falaq Ahmad Fikri Demo semakin lancar. Target berikutnya Al-Ikhlas.'],
            [$guruUser->id, $siswaUser->id, 'Tugas Matematika Demo', 'Tugas', 'Jangan lupa menyelesaikan tugas Matematika Demo sebelum Jumat.'],
            [$ortuUser->id, $guruUser->id, 'Hasil Belajar Demo', 'Akademik', 'Mohon informasi perkembangan belajar Ahmad Fikri Demo pekan ini.'],
            [$guruUser->id, $ortuUser->id, 'Re: Hasil Belajar Demo', 'Akademik', 'Ahmad Fikri Demo aktif dan nilai formatifnya stabil. Latihan soal cerita tetap perlu ditambah.'],
        ];

        $threadId = 'demo-buku-penghubung-'.$siswa->id;
        $parentId = null;
        foreach ($messages as $idx => [$from, $to, $subject, $category, $body]) {
            $pesan = Pesan::updateOrCreate(
                ['thread_id' => $threadId, 'isi' => $body],
                [
                    'pengirim_id' => $from,
                    'penerima_id' => $to,
                    'siswa_id' => $siswa->id,
                    'subjek' => $subject,
                    'kategori' => $category,
                    'tanggal' => now()->subDays(6 - $idx),
                    'dibaca' => $idx % 2 === 0,
                    'parent_message_id' => $parentId,
                ]
            );
            $parentId ??= $pesan->id;
        }
    }

    private function seedTeacherAdministration(Guru $guru, Siswa $siswa, Kelas $kelas, Mapel $mapel, User $actor): void
    {
        JurnalSikap::firstOrCreate(
            ['siswa_id' => $siswa->id, 'guru_id' => $guru->id, 'tanggal' => '2026-07-10', 'kejadian' => 'Demo membantu teman memahami tugas.'],
            ['kelas_id' => $kelas->id, 'tindakan' => 'Diberi apresiasi.', 'paraf' => 'GD', 'tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil']
        );

        ProgramPengayaan::firstOrCreate(
            ['guru_id' => $guru->id, 'kelas_id' => $kelas->id, 'mapel_id' => $mapel->id, 'materi' => 'Soal cerita bilangan Demo'],
            ['kompetensi_dasar' => 'KD Demo', 'bentuk_pengayaan' => 'Latihan proyek mini Demo', 'keterangan' => 'Untuk siswa cepat selesai.', 'tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil']
        );

        $nilai = Nilai::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->first();
        $remedial = null;
        if ($nilai) {
            $remedial = Remedial::firstOrCreate(
                ['siswa_id' => $siswa->id, 'mapel_id' => $mapel->id, 'nilai_id' => $nilai->id],
                ['nilai_asal' => 68, 'deadline' => '2026-07-29', 'status' => 'pending']
            );
        }

        ProgramRemedial::firstOrCreate(
            ['guru_id' => $guru->id, 'siswa_id' => $siswa->id, 'mapel_id' => $mapel->id, 'materi' => 'Perbaikan operasi hitung Demo'],
            ['remedial_id' => $remedial?->id, 'kelas_id' => $kelas->id, 'kompetensi_dasar' => 'KD Remedial Demo', 'nilai_sebelum' => 68, 'nilai_sesudah' => 78, 'keterangan' => 'Demo selesai latihan ulang.', 'paraf' => 'GD', 'status' => 'selesai', 'tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil']
        );

        foreach (['Cover', 'Kalender Pendidikan', 'Silabus', 'Program Tahunan', 'Program Semester 1', 'RPP Semester 1', 'Remedial dan Pengayaan'] as $dokumen) {
            AdministrasiGuruChecklist::updateOrCreate(
                ['guru_id' => $guru->id, 'dokumen' => $dokumen, 'tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil'],
                ['status' => 'lengkap', 'tanggal_dilengkapi' => '2026-07-20', 'reviewed_by' => $actor->id, 'reviewed_at' => now(), 'catatan_reviewer' => 'Checklist Demo.']
            );
        }
    }

    private function seedAssignments(Guru $guru, Siswa $siswa, Kelas $kelas, Mapel $mapel, User $actor): void
    {
        $pengumuman = Pengumuman::firstOrCreate(
            ['judul' => 'Pengumuman Demo SMPIT'],
            ['konten' => 'Informasi sekolah Demo untuk akun dummy.', 'created_by' => $actor->id, 'target_role' => 'semua']
        );

        $tugas = Tugas::updateOrCreate(
            ['judul' => 'Tugas Matematika Demo', 'kelas_id' => $kelas->id],
            ['deskripsi' => 'Tugas Demo operasi hitung.', 'mapel_id' => $mapel->id, 'guru_id' => $guru->id, 'tipe' => 'tugas', 'tanggal_deadline' => '2026-07-31', 'tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil']
        );

        $workbook = Workbook::updateOrCreate(
            ['judul' => 'Workbook Demo Matematika', 'kelas_id' => $kelas->id],
            ['deskripsi' => 'Workbook Demo untuk siswa dummy.', 'mapel_id' => $mapel->id, 'guru_id' => $guru->id, 'tipe' => 'penugasan_di_rumah', 'tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil']
        );
        $soal = WorkbookSoal::updateOrCreate(
            ['workbook_id' => $workbook->id, 'nomor' => 1],
            ['soal' => 'Berapakah 12 + 8?', 'tipe' => 'pg', 'pilihan_a' => '18', 'pilihan_b' => '20', 'pilihan_c' => '22', 'pilihan_d' => '24', 'jawaban_benar' => 'b', 'bobot' => 1]
        );
        WorkbookJawaban::updateOrCreate(
            ['workbook_soal_id' => $soal->id, 'siswa_id' => $siswa->id],
            ['jawaban' => 'b', 'nilai' => 100]
        );

        $badge = Badge::firstOrCreate(
            ['nama' => 'Demo Rajin Belajar'],
            ['deskripsi' => 'Badge Demo untuk akun siswa dummy.', 'icon' => '*']
        );
        SiswaBadge::firstOrCreate(['siswa_id' => $siswa->id, 'badge_id' => $badge->id], ['achieved_at' => now()]);

        CatatanWali::updateOrCreate(
            ['siswa_id' => $siswa->id, 'semester' => 'Ganjil 2026/2027'],
            ['catatan' => 'Catatan wali Demo: Ananda aktif mengikuti pembelajaran.', 'created_by' => $guru->id]
        );
    }

    private function rapor(Siswa $siswa, string $jenis, string $tahun, string $semester, User $actor, string $catatan): Rapor
    {
        return Rapor::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran' => $tahun, 'semester' => $semester, 'jenis_rapor' => $jenis],
            [
                'status' => 'published',
                'published_at' => now(),
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
                'catatan' => $catatan,
                'snapshot' => ['source' => 'Revisi1Seeder', 'siswa' => $siswa->nama],
                'signature_metadata' => [
                    'tempat_tanggal' => 'Bekasi, 24 Juli 2026',
                    'wali_kelas' => 'Guru Demo SMPIT',
                    'kepala_sekolah' => 'Kepala Sekolah',
                    'koordinator_quran' => 'Koordinator Quran',
                    'guru_tahfidz' => 'Guru Demo SMPIT',
                ],
            ]
        );
    }

    private function juz30Surahs(): array
    {
        return [
            'Al-Fatihah', 'An-Naas', 'Al-Falaq', 'Al-Ikhlash', 'Al-Lahab', 'An-Nashr',
            'Al-Kafirun', 'Al-Kautsar', 'Al-Maun', 'Quraisy', 'Al-Fiil', 'Al-Humazah',
            'Al-Ashr', 'At-Takatsur', 'Al-Qariah', 'Al-Adiyat', 'Az-Zalzalah',
            'Al-Bayyinah', 'Al-Qadr', 'Al-Alaq', 'At-Tiin', 'Al-Insyirah', 'Adh-Dhuha',
            'Al-Lail', 'Asy-Syams', 'Al-Balad', 'Al-Fajr', 'Al-Ghasyiyah', 'Al-Ala',
            'At-Thariq', 'Al-Buruj', 'Al-Insyiqaq', 'Al-Muthaffifin', 'Al-Infithar',
            'At-Takwir', 'Abasa', 'An-Naziat', 'An-Naba',
        ];
    }
}
