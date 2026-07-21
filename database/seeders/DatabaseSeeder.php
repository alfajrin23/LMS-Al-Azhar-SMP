<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\OrangTua;
use App\Models\Jadwal;
use App\Models\Tugas;
use App\Models\Nilai;
use App\Models\Kehadiran;
use App\Models\CatatanWali;
use App\Models\Badge;
use App\Models\SiswaBadge;
use App\Models\Workbook;
use App\Models\WorkbookSoal;
use App\Models\Spp;
use App\Models\Pembayaran;
use App\Models\LogAktivitas;
use App\Models\Pengaturan;
use App\Models\TahfidzSetoran;
use App\Models\Pengumuman;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GuruSeeder::class,
            KelasSeeder::class,
            KelasQuranSeeder::class,
            SiswaSeeder::class,
            MapelSeeder::class,
            JadwalSeeder::class,
        ]);


        // === USER & ORANG TUA ===
        $user = User::firstOrCreate(
            ['email' => 'sari.rohmah@email.com'],
            ['name' => 'Ibu Sari Rahmawati', 'password' => Hash::make('password123'), 'role' => 'orang_tua']
        );
        OrangTua::firstOrCreate(
            ['user_id' => $user->id],
            ['nama' => 'Ibu Sari Rahmawati']
        );

        // === USER: ADMIN ===
        User::firstOrCreate(
            ['email' => 'admin@alazharjayaindonesia.sch.id'],
            ['name' => 'Admin Sekolah', 'password' => Hash::make('password123'), 'role' => 'admin']
        );

        // === USER: KEPALA SEKOLAH ===
        User::firstOrCreate(
            ['email' => 'kepala@alazharjayaindonesia.sch.id'],
            ['name' => 'Kepala Sekolah', 'password' => Hash::make('password123'), 'role' => 'kepala_sekolah']
        );

        // === KUMPULKAN ID UNTUK SEEDING ===
        $siswas = \App\Models\Siswa::all();
        $mapels = \App\Models\Mapel::whereNotIn('nama_mapel', ['Istirahat', 'Dzuhur Time', 'Ashar Time', 'Upacara / Flash', 'Upacara / PAS Mantap', 'Dhuha Time', 'Apel, Dhuha & Muroja\'ah', 'Upacara / Pentas Seni', 'Qailullah', 'Sholat dan Makan', 'Pulang / Penjemputan Orang Tua', 'Snack Time', 'Transisi / Pindah ke Kelas', 'Shalat Ashar', 'Shalat Ashar dan Dzikir', 'Kegiatan Pramuka', 'Ekskul'])->get();
        $kelas = \App\Models\Kelas::all();
        $gurus = \App\Models\Guru::all();
        $adminUser = User::query()->where('role', 'admin')->first();
        $guruUser = User::query()->where('role', 'guru')->first();
        $siswaUser = User::query()->where('role', 'siswa_smp')->first();

        // Helpers untuk mencegah error array key
        $randomSiswa = fn() => $siswas->random()->id;
        $randomMapel = fn() => $mapels->random()->id;
        $randomKelas = fn() => $kelas->random()->id;
        $randomGuru = fn() => $gurus->random()->id;

        // === TUGAS ===
        $tugasData = [
            ['judul' => 'Tugas Praktek Sholat', 'tipe' => 'tugas', 'deadline' => '2026-04-02'],
            ['judul' => 'PR Persamaan Linear', 'tipe' => 'tugas', 'deadline' => '2026-04-05'],
            ['judul' => 'Laporan Pengamatan', 'tipe' => 'tugas', 'deadline' => '2026-04-10'],
            ['judul' => 'Esai Liburan', 'tipe' => 'tugas', 'deadline' => '2026-04-15'],
            ['judul' => 'Vocabulary Quiz', 'tipe' => 'tugas', 'deadline' => '2026-04-12'],
            ['judul' => 'Ulangan Harian Bab 4', 'tipe' => 'ulangan', 'deadline' => '2026-04-08'],
            ['judul' => 'UTS Genap', 'tipe' => 'ulangan', 'deadline' => '2026-04-20'],
            ['judul' => 'Ulangan Harian Bab 3', 'tipe' => 'ulangan', 'deadline' => '2026-03-15'],
        ];
        foreach ($tugasData as $td) {
            Tugas::firstOrCreate(
                ['judul' => $td['judul']],
                ['mapel_id' => $randomMapel(), 'kelas_id' => $randomKelas(), 'guru_id' => $randomGuru(), 'tipe' => $td['tipe'], 'tanggal_deadline' => $td['deadline']]
            );
        }

        // === NILAI ===
        // Beri nilai random untuk setiap siswa dan mapel (Ambil 5 mapel saja per siswa agar tidak terlalu berat)
        foreach ($siswas as $siswa) {
            foreach ($mapels->take(5) as $mapel) {
                Nilai::firstOrCreate(
                    ['siswa_id' => $siswa->id, 'mapel_id' => $mapel->id],
                    ['nilai' => rand(75, 100)]
                );
            }
        }

        // === KEHADIRAN ===
        $statusDistribution = ['hadir' => 90, 'sakit' => 3, 'izin' => 3, 'alpha' => 4];
        $date = Carbon::create(2026, 3, 1);
        $end = Carbon::create(2026, 3, 10); // 10 hari saja
        while ($date->lte($end)) {
            if ($date->isWeekday()) {
                foreach ($siswas->take(10) as $siswa) { // Sample 10 siswa
                    $pick = 'hadir';
                    $rand = rand(1, 100);
                    $cum = 0;
                    foreach ($statusDistribution as $s => $pct) {
                        $cum += $pct;
                        if ($rand <= $cum) {
                            $pick = $s;
                            break;
                        }
                    }
                    Kehadiran::firstOrCreate(
                        ['siswa_id' => $siswa->id, 'tanggal' => $date->format('Y-m-d')],
                        ['status' => $pick]
                    );
                }
            }
            $date->addDay();
        }

        // === CATATAN WALI KELAS ===
        $catatanSamples = [
            'Ananda adalah siswa yang rajin dan memiliki semangat belajar tinggi.',
            'Ananda memiliki potensi besar di bidang agama. Pertahankan!',
            'Ananda aktif dalam kegiatan kelas, namun perlu lebih teliti.',
            'Ananda siswi yang kreatif dan disiplin. Terus kembangkan bakat.',
        ];
        foreach ($siswas->take(10) as $siswa) {
            CatatanWali::firstOrCreate(
                ['siswa_id' => $siswa->id, 'semester' => 'Genap 2025/2026'],
                ['catatan' => collect($catatanSamples)->random(), 'created_by' => $randomGuru()]
            );
        }

        // === TAHFIDZ SETORAN ===
        foreach ($siswas->take(10) as $siswa) {
            for ($i = 0; $i < 3; $i++) {
                TahfidzSetoran::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $randomGuru(),
                    'tanggal' => '2026-03-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'surah' => collect(['An-Naba\'', 'An-Nazi\'at', 'Abasa'])->random(),
                    'ayat_mulai' => 1,
                    'ayat_selesai' => rand(5, 15),
                    'jumlah_ayat' => rand(5, 15),
                    'status' => collect(['baru', 'murojaah'])->random(),
                    'nilai' => rand(80, 100),
                ]);
            }
        }

        // === PENGUMUMAN ===
        $pengumumanData = [
            ['judul' => 'Libur Hari Raya Nyepi', 'konten' => 'Sekolah libur pada tanggal 30 Maret 2026.', 'tanggal' => '2026-03-25'],
            ['judul' => 'Pesantren Kilat Ramadhan', 'konten' => 'Kegiatan pesantren kilat akan diadakan pada minggu kedua April.', 'tanggal' => '2026-03-20'],
        ];
        if ($adminUser) {
            foreach ($pengumumanData as $pd) {
                Pengumuman::firstOrCreate(
                    ['judul' => $pd['judul']],
                    ['konten' => $pd['konten'], 'created_by' => $adminUser->id, 'created_at' => $pd['tanggal'], 'updated_at' => $pd['tanggal']]
                );
            }
        }

        // === PESAN ===
        if ($guruUser && $siswaUser) {
            Pesan::firstOrCreate(
                ['pengirim_id' => $guruUser->id, 'penerima_id' => $siswaUser->id, 'subjek' => 'PR Matematika'],
                ['isi' => 'Jangan lupa kumpulkan PR besok!']
            );
        }

        // === BADGES ===
        $badgeData = [
            ['nama' => 'Rajin Belajar', 'deskripsi' => 'Aktif 30 hari berturut-turut', 'icon' => '⭐'],
            ['nama' => 'Juara Quiz', 'deskripsi' => 'Nilai quiz di atas 90', 'icon' => '🎯'],
            ['nama' => 'Pembaca Aktif', 'deskripsi' => 'Baca 20 materi', 'icon' => '📖'],
            ['nama' => 'Hafidz Cilik', 'deskripsi' => 'Hafal 1 juz Al-Qur\'an', 'icon' => '📿'],
        ];
        $badgeIdsArray = [];
        foreach ($badgeData as $bd) {
            $badgeIdsArray[] = Badge::firstOrCreate(
                ['nama' => $bd['nama']],
                ['deskripsi' => $bd['deskripsi'], 'icon' => $bd['icon']]
            )->id;
        }

        // === SISWA BADGE ===
        foreach ($siswas->take(5) as $siswa) {
            SiswaBadge::firstOrCreate(
                ['siswa_id' => $siswa->id, 'badge_id' => collect($badgeIdsArray)->random()]
            );
        }

        // === WORKBOOKS ===
        $wbData = [
            ['judul' => 'Latihan Soal Matematika Bab 5', 'tipe' => 'penugasan_di_rumah', 'soals' => [
                ['soal' => 'Berapakah hasil dari 25 × 4?', 'a' => '80', 'b' => '100', 'c' => '120', 'd' => '90', 'benar' => 'b', 'bobot' => 1],
                ['soal' => 'Sebutkan rumus luas persegi panjang!', 'tipe' => 'essay', 'bobot' => 2],
            ]],
            ['judul' => 'PR Bahasa Indonesia', 'tipe' => 'tugas_pengganti', 'soals' => [
                ['soal' => 'Apa sinonim dari kata "rajin"?', 'a' => 'Malas', 'b' => 'Tekun', 'c' => 'Cepat', 'd' => 'Lambat', 'benar' => 'b', 'bobot' => 1],
            ]],
        ];
        foreach ($wbData as $wb) {
            $wbModel = Workbook::firstOrCreate(
                ['judul' => $wb['judul']],
                ['mapel_id' => $randomMapel(), 'kelas_id' => $randomKelas(), 'guru_id' => $randomGuru(), 'tipe' => $wb['tipe']]
            );
            foreach ($wb['soals'] as $i => $s) {
                WorkbookSoal::firstOrCreate(
                    ['workbook_id' => $wbModel->id, 'nomor' => $i + 1],
                    [
                        'soal' => $s['soal'],
                        'tipe' => $s['tipe'] ?? 'pg', // menggunakan valid enum (pg / essay)
                        'pilihan_a' => $s['a'] ?? null,
                        'pilihan_b' => $s['b'] ?? null,
                        'pilihan_c' => $s['c'] ?? null,
                        'pilihan_d' => $s['d'] ?? null,
                        'jawaban_benar' => $s['benar'] ?? null,
                        'bobot' => $s['bobot'] ?? 1,
                    ]
                );
            }
        }

        // === SPP & PEMBAYARAN ===
        $bulanSekarang = (int)now()->format('m');
        $tahunSekarang = (int)now()->format('Y');
        foreach ($siswas->take(3) as $siswa) {
            for ($b = 1; $b <= $bulanSekarang; $b++) {
                $spp = Spp::firstOrCreate(
                    ['siswa_id' => $siswa->id, 'bulan' => $b, 'tahun' => $tahunSekarang],
                    ['jumlah' => 250000, 'tenggat' => "{$tahunSekarang}-" . str_pad($b, 2, '0', STR_PAD_LEFT) . '-10', 'status' => $b < $bulanSekarang ? 'lunas' : 'belum']
                );
                if ($b < $bulanSekarang) {
                    Pembayaran::firstOrCreate(
                        ['spp_id' => $spp->id],
                        ['orang_tua_id' => 1, 'tanggal_bayar' => "{$tahunSekarang}-" . str_pad($b, 2, '0', STR_PAD_LEFT) . '-05', 'jumlah' => 250000, 'metode' => 'transfer', 'status' => 'confirmed']
                    );
                }
            }
        }

        // === LOG AKTIVITAS ===
        if ($adminUser) {
            LogAktivitas::firstOrCreate(['deskripsi' => 'Login admin dashboard'], ['user_id' => $adminUser->id, 'tipe' => 'User', 'created_at' => now()]);
        }

        // === PENGATURAN ===
        $pengaturanData = [
            ['key' => 'nama_sekolah', 'value' => 'SMPIT Al Azhar Jaya Indonesia'],
            ['key' => 'tahun_ajaran', 'value' => '2025/2026'],
            ['key' => 'semester', 'value' => 'Genap'],
            ['key' => 'alamat_sekolah', 'value' => 'Jl. Raya Cendana No. 123, Kota Tangerang Selatan, Banten'],
            ['key' => 'kkm_default', 'value' => '70'],
        ];
        foreach ($pengaturanData as $pd) {
            Pengaturan::firstOrCreate(['key' => $pd['key']], ['value' => $pd['value']]);
        }

        $this->command->info('Database seeded successfully!');
    }
}
