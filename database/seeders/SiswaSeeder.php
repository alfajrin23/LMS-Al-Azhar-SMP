<?php
namespace Database\Seeders;
use App\Models\Kelas;
use App\Models\KelasQuran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelasRegulerGroups = [
            'Abu Bakar Ar Razi' => [
                'Alviandra Chesta Radityatama',
                'Aqilah Naurah',
                'Argia Raya Adz Dzikra',
                'Hanna Mufidah',
                'Khanza Izzatunnisa',
                'Muhammad Hafidz Alfahrezi',
                'Muhammad Al Farizy Setyanto',
                'Muhammad Al Fatih',
                'Muhammad Fadlan Adelmar',
                'Reval Rama Sulistiyo',
                'Sahla Azka Hafizah',
                'Ziya Zivana Zubi',
            ],
            'Ibnu Al Haytam' => [
                'Abdul Latif',
                'Abid Abyan Tsany Solihansyah',
                'Aldeen Hadwan Shirazi',
                'Chaidar Rafisqy Hermawan',
                'Muhammad Azzam Nufail',
                'Muhammad Falah Pratama Mulya',
                'Muhammad Maher',
                'Muhammad Quincy Satyamulya',
                'Muhammad Wafi Alkiram',
                'Rizky Maulana As Shidiq',
                'Tsabit Wafi Musyaffa',
                'Zahran Farid Firdaus',
            ],
            'Maryam Al Ijliyah' => [
                'Aila Zahwa Aqilah',
                'Aisyah Rahma Haniya',
                'Jillan Azra Hakimah',
                'Kalula Ayesha Alkanza',
                'Muhdatus Sa\'idah',
                'Ribkha Pramesti Wijaya',
                'Syaila Unatsa Putri',
                'Violine Atha Al-Husna',
            ],
            'Al Khawarizmi' => [
                'Daffa Zaidan Pratama',
                'Fauzi Abdu Rohman',
                'Galang Hernandes',
                'Hamzah Irfan Fadillah',
                'Kelvin Adel Pratama',
                'Muhammad Daffa Al Ghifari',
                'Maherdes Attaufiq Riziqlillah',
                'Muhammad Albyan',
                'Muhammad Alhafidz',
                'Muhammad Aslam Zayyan',
                'Muhammad Eldafi Satya Irawan',
                'Muhammad Rakha Solihansyah',
                'Naufal Djaky Ardani',
                'Pandu Bagus Damar Wiyono',
                'Zein Maulana Muaffin',
            ],
            'Fatimah Al Fihri' => [
                'Anindita Valencia Aurora',
                'Atsilah Azka Ramadhani',
                'Dara Medina Putri',
                'Elsa Sri Isyana',
                'Fadhilah Ramadhani',
                'Fakhirah Khansa Anditasyah',
                'Fauziah Adawiyah',
                'Gadis Bening Arsandi',
                'Gian Naifa Shofa',
                'Jihan Makaila Fakhirah',
                'Kayyasah Sholiha',
                'Khayla Almira Maritza H',
                'Nadia Aqila Shanum',
                'Naila Syahira Mumtazah',
                'Nasywa Maulida',
                'Nurul Hanis Nahilah',
                'Nurul Kasyfatul Mahjubiyyah',
                'Putri Yara',
                'Syakira Sausan Khairunnisa',
            ],
        ];
        $kelasBiasaMap = [];
        foreach ($kelasRegulerGroups as $namaKelas => $siswas) {
            foreach ($siswas as $namaSiswa) {
                $kelasBiasaMap[$namaSiswa] = $namaKelas;
            }
        }
        $dataSiswaQuran = [
            'Imam Nafi’ bin Abdurrahman' => [
                'jk' => 'L',
                'siswa' => [
                    'Abdul Latif',
                    'Abid Abyan Tsany Solihansyah',
                    'Aldeen Hadwan Shirazi',
                    'Chaidar Rafisqy Hermawan',
                    'Daffa Zaidan Pratama',
                    'Fauzi Abdu Rohman',
                    'Galang Hernandes',
                    'Hamzah Irfan Fadillah',
                    'Kelvin Adel Pratama',
                    'Maherdes Attaufiq Riziqlillah',
                    'Muhammad Albyan',
                    'Muhammad Alhafidz',
                    'Muhammad Aslam Zayyan',
                    'Rizky Maulana As Shidiq',
                ],
            ],
            'Imam Abdullah bin Katsir' => [
                'jk' => 'L',
                'siswa' => [
                    'Muhammad Azzam Nufail',
                    'Muhammad Daffa Al Ghifari',
                    'Muhammad Eldafi Satya Irawan',
                    'Muhammad Falah Pratama Mulya',
                    'Muhammad Maher',
                    'Muhammad Quincy Satyamulya',
                    'Muhammad Rakha Solihansyah',
                    'Muhammad Wafi Alkiram',
                    'Naufal Djaky Ardani',
                    'Pandu Bagus Damar Wiyono',
                    'Tsabit Wafi Musyaffa',
                    'Zahran Farid Firdaus',
                    'Zein Maulana Muaffin',
                ],
            ],
            'Imam Ashim bin Abi Al-Najud' => [
                'jk' => 'L',
                'siswa' => [
                    'Alviandra Chesta Radityatama',
                    'Argia Raya Adz Dzikra',
                    'Muhammad Hafidz Alfahrezi',
                    'Muhammad Al Farizy Setyanto',
                    'Muhammad Al Fatih',
                    'Muhammad Fadlan Adelmar',
                    'Reval Rama Sulistiyo',
                ],
            ],
            'Sutayta Al Mahamali' => [
                'jk' => 'P',
                'siswa' => [
                    'Aila Zahwa Aqilah',
                    'Aisyah Rahma Haniya',
                    'Anindita Valencia Aurora',
                    'Atsilah Azka Ramadhani',
                    'Dara Medina Putri',
                    'Elsa Sri Isyana',
                    'Fadhilah Ramadhani',
                    'Fakhirah Khansa Anditasyah',
                    'Fauziah Adawiyah',
                    'Gadis Bening Arsandi',
                    'Gian Naifa Shofa',
                    'Jihan Makaila Fakhirah',
                    'Jillan Azra Hakimah',
                ],
            ],
            'Zainab binti Ahmad' => [
                'jk' => 'P',
                'siswa' => [
                    'Kalula Ayesha Alkanza',
                    'Kayyasah Sholiha',
                    'Khayla Almira Maritza H',
                    'Muhdatus Sa\'idah',
                    'Nadia Aqila Shanum',
                    'Naila Syahira Mumtazah',
                    'Nasywa Maulida',
                    'Nurul Hanis Nahilah',
                    'Nurul Kasyfatul Mahjubiyyah',
                    'Putri Yara',
                    'Ribkha Pramesti Wijaya',
                    'Syaila Unatsa Putri',
                    'Syakira Sausan Khairunnisa',
                    'Violine Atha Al-Husna',
                ],
            ],
            'Bina Shaheen Siddiqui' => [
                'jk' => 'P',
                'siswa' => [
                    'Aqilah Naurah',
                    'Sahla Azka Hafizah',
                    'Hanna Mufidah',
                    'Khanza Izzatunnisa',
                    'Ziya Zivana Zubi',
                ],
            ],
        ];
        $quranNames = collect($dataSiswaQuran)->flatMap(fn ($group) => $group['siswa']);
        $regularNames = collect($kelasRegulerGroups)->flatten();
        $missingFromRegular = $quranNames->diff($regularNames);
        $missingFromQuran = $regularNames->diff($quranNames);
        if ($quranNames->count() !== 66 || $quranNames->unique()->count() !== 66) {
            throw new \RuntimeException('Data siswa SMP harus berisi 66 nama unik.');
        }
        if ($missingFromRegular->isNotEmpty() || $missingFromQuran->isNotEmpty()) {
            throw new \RuntimeException('Data kelas reguler dan kelas Quran SMP tidak sinkron.');
        }
        $sdSiswaIds = Siswa::query()
            ->whereHas('user', fn ($query) => $query->where('role', 'siswa_sd'))
            ->pluck('id');
        if ($sdSiswaIds->isNotEmpty()) {
            \App\Models\PengumpulanTugas::query()->whereIn('siswa_id', $sdSiswaIds)->delete();
            \App\Models\WorkbookJawaban::query()->whereIn('siswa_id', $sdSiswaIds)->delete();
            \App\Models\CbtJawaban::query()->whereIn('siswa_id', $sdSiswaIds)->delete();
            \App\Models\OlympiadJawaban::query()->whereIn('siswa_id', $sdSiswaIds)->delete();
            $sppIds = \App\Models\Spp::query()->whereIn('siswa_id', $sdSiswaIds)->pluck('id');
            if ($sppIds->isNotEmpty()) {
                \App\Models\Pembayaran::query()->whereIn('spp_id', $sppIds)->delete();
                \App\Models\Spp::query()->whereIn('id', $sppIds)->delete();
            }
        }
        User::query()->where('role', 'siswa_sd')->delete();
        Siswa::query()
            ->whereIn('nama', $quranNames->unique()->values())
            ->get()
            ->each(function (Siswa $siswa): void {
                $siswa->forceFill(['nis' => 'TMP-' . $siswa->id])->save();
            });
        $nisCounter = 26001;
        foreach ($dataSiswaQuran as $namaKelasQuran => $grup) {
            $kelasQuranId = KelasQuran::query()->where('nama_kelas', $namaKelasQuran)->value('id');
            foreach ($grup['siswa'] as $namaSiswa) {
                $email = strtolower(str_replace([' ', '\'', '’'], ['.', '', ''], $namaSiswa)) . '@alazharjayaindonesia.sch.id';
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $namaSiswa,
                        'password' => Hash::make('password123'),
                        'role' => 'siswa_smp',
                    ]
                );
                $kelasRegulerId = Kelas::query()
                    ->where('nama_kelas', $kelasBiasaMap[$namaSiswa])
                    ->value('id');
                Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nis' => (string) $nisCounter,
                        'nama' => $namaSiswa,
                        'kelas_id' => $kelasRegulerId,
                        'kelas_quran_id' => $kelasQuranId,
                        'jenis_kelamin' => $grup['jk'],
                        'status' => 'aktif',
                    ]
                );
                $nisCounter++;
            }
        }
    }
}
