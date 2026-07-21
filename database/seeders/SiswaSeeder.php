<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\KelasQuran;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Kelas Biasa (Reguler) untuk mapping
        $kelasBiasaMap = [
            'Alviandra Chesta Radityatama' => 'Abu Bakar Ar Razi',
            'Aqilah Naurah' => 'Abu Bakar Ar Razi',
            'Argia Raya Adz Dzikra' => 'Abu Bakar Ar Razi',
            'Hanna Mufidah' => 'Abu Bakar Ar Razi',
            'Khanza Izzatunnisa' => 'Abu Bakar Ar Razi',
            'Muhammad Hafidz Alfahrezi' => 'Abu Bakar Ar Razi',
            'Muhammad Al Farizy Setyanto' => 'Abu Bakar Ar Razi',
            'Muhammad Al Fatih' => 'Abu Bakar Ar Razi',
            'Muhammad Fadlan Adelmar' => 'Abu Bakar Ar Razi',
            'Reval Rama Sulistiyo' => 'Abu Bakar Ar Razi',
            'Sahla Azka Hafizah' => 'Abu Bakar Ar Razi',
            'Ziya Zivana Zubi' => 'Abu Bakar Ar Razi',
            'Abdul Latif' => 'Ibnu Al Haytam',
            'Abid Abyan Tsany Solihansyah' => 'Ibnu Al Haytam',
            'Aldeen Hadwan Shirazi' => 'Ibnu Al Haytam',
            'Chaidar Rafisqy Hermawan' => 'Ibnu Al Haytam',
            'Muhammad Azzam Nufail' => 'Ibnu Al Haytam',
            'Muhammad Falah Pratama Mulya' => 'Ibnu Al Haytam',
            'Muhammad Maher' => 'Ibnu Al Haytam',
            'Muhammad Quincy Satyamulya' => 'Ibnu Al Haytam',
            'Muhammad Wafi Alkiram' => 'Ibnu Al Haytam',
            'Rizky Maulana As Shidiq' => 'Ibnu Al Haytam',
            'Tsabit Wafi Musyaffa' => 'Ibnu Al Haytam',
            'Zahran Farid Firdaus' => 'Ibnu Al Haytam',
            'Aila Zahwa Aqilah' => 'Maryam Al Ijliyah',
            'Aisyah Rahma Haniya' => 'Maryam Al Ijliyah',
            'Jillan Azra Hakimah' => 'Maryam Al Ijliyah',
            'Kalula Ayesha Alkanza' => 'Maryam Al Ijliyah',
            'Muhdatus Sa\'idah' => 'Maryam Al Ijliyah',
            'Ribkha Pramesti Wijaya' => 'Maryam Al Ijliyah',
            'Syaila Unatsa Putri' => 'Maryam Al Ijliyah',
            'Violine Atha Al-Husna' => 'Maryam Al Ijliyah',
            'Daffa Zaidan Pratama' => 'Al Khawarizmi',
            'Fauzi Abdu Rohman' => 'Al Khawarizmi',
            'Galang Hernandes' => 'Al Khawarizmi',
            'Hamzah Irfan Fadillah' => 'Al Khawarizmi',
            'Kelvin Adel Pratama' => 'Al Khawarizmi',
            'Muhammad Daffa Al Ghifari' => 'Al Khawarizmi',
            'Maherdes Attaufiq Riziqlillah' => 'Al Khawarizmi',
            'Muhammad Albyan' => 'Al Khawarizmi',
            'Muhammad Alhafidz' => 'Al Khawarizmi',
            'Muhammad Aslam Zayyan' => 'Al Khawarizmi',
            'Muhammad Eldafi Satya Irawan' => 'Al Khawarizmi',
            'Muhammad Rakha Solihansyah' => 'Al Khawarizmi',
            'Naufal Djaky Ardani' => 'Al Khawarizmi',
            'Pandu Bagus Damar Wiyono' => 'Al Khawarizmi',
            'Zein Maulana Muaffin' => 'Al Khawarizmi',
            'Anindita Valencia Aurora' => 'Fatimah Al Fihri',
            'Atsilah Azka Ramadhani' => 'Fatimah Al Fihri',
            'Dara Medina Putri' => 'Fatimah Al Fihri',
            'Elsa Sri Isyana' => 'Fatimah Al Fihri',
            'Fadhilah Ramadhani' => 'Fatimah Al Fihri',
            'Fakhirah Khansa Anditasyah' => 'Fatimah Al Fihri',
            'Fauziah Adawiyah' => 'Fatimah Al Fihri',
            'Gadis Bening Arsandi' => 'Fatimah Al Fihri',
            'Gian Naifa Shofa' => 'Fatimah Al Fihri',
            'Jihan Makaila Fakhirah' => 'Fatimah Al Fihri',
            'Kayyasah Sholiha' => 'Fatimah Al Fihri',
            'Khayla Almira Maritza H' => 'Fatimah Al Fihri',
            'Nadia Aqila Shanum' => 'Fatimah Al Fihri',
            'Naila Syahira Mumtazah' => 'Fatimah Al Fihri',
            'Nasywa Maulida' => 'Fatimah Al Fihri',
            'Nurul Hanis Nahilah' => 'Fatimah Al Fihri',
            'Nurul Kasyfatul Mahjubiyyah' => 'Fatimah Al Fihri',
            'Putri Yara' => 'Fatimah Al Fihri',
            'Syakira Sausan Khairunnisa' => 'Fatimah Al Fihri'
        ];

        // 2. Data Kelas Quran untuk pengelompokan utama (SD & SMP digabung)
        $dataSiswaQuran = [
            // --- SMP ---
            'Imam Nafi bin Abdurrahman' => [
                'jk' => 'L',
                'role' => 'siswa_smp',
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
                    'Muhammad Daffa Al Ghifari',
                    'Maherdes Attaufiq Riziqlillah',
                    'Muhammad Albyan',
                    'Muhammad Alhafidz',
                    'Muhammad Aslam Zayyan'
                ]
            ],
            'Imam Abdullah bin Katsir' => [
                'jk' => 'L',
                'role' => 'siswa_smp',
                'siswa' => [
                    'Muhammad Azzam Nufail',
                    'Muhammad Eldafi Satya Irawan',
                    'Muhammad Falah Pratama Mulya',
                    'Muhammad Maher',
                    'Muhammad Quincy Satyamulya',
                    'Muhammad Rakha Solihansyah',
                    'Muhammad Wafi Alkiram',
                    'Naufal Djaky Ardani',
                    'Pandu Bagus Damar Wiyono',
                    'Rizky Maulana As Shidiq',
                    'Tsabit Wafi Musyaffa',
                    'Zahran Farid Firdaus',
                    'Zein Maulana Muaffin'
                ]
            ],
            'Imam Ashim bin Abi Al-Najud' => [
                'jk' => 'L',
                'role' => 'siswa_smp',
                'siswa' => [
                    'Alviandra Chesta Radityatama',
                    'Argia Raya Adz Dzikra',
                    'Muhammad Hafidz Alfahrezi',
                    'Muhammad Al Farizy Setyanto',
                    'Muhammad Al Fatih',
                    'Muhammad Fadlan Adelmar',
                    'Reval Rama Sulistiyo'
                ]
            ],
            'Sutayta Al-Mahamali' => [
                'jk' => 'P',
                'role' => 'siswa_smp',
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
                    'Jillan Azra Hakimah'
                ]
            ],
            'Zainab binti Ahmad' => [
                'jk' => 'P',
                'role' => 'siswa_smp',
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
                    'Violine Atha Al-Husna'
                ]
            ],
            'Bina Shaheen Siddiqui' => [
                'jk' => 'P',
                'role' => 'siswa_smp',
                'siswa' => [
                    'Aqilah Naurah',
                    'Sahla Azka Hafizah',
                    'Hanna Mufidah',
                    'Khanza Izzatunnisa',
                    'Ziya Zivana Zubi'
                ]
            ],

            // --- SD Ikhwan ---
            'Abu Ubaidah bin Al-Jarrah' => [
                'jk' => 'L',
                'role' => 'siswa_sd',
                'siswa' => [
                    'Abiyan Elsandra Aradea',
                    'Abizhar Alfarizki',
                    'Addeen Makaio Anargya Dikromo',
                    'Arfan Alfarizki Khairan',
                    'Atthar Rayyan Al Farizqi',
                    'Faliq Athallah Syaif',
                    'Fathian Zufar Haufanhazza Alfaqih',
                    'Harun Putra Al Hakim',
                    'Hersa Muhammad Ahsan Syauqi',
                    'Kalandra Bimo Saputro',
                    'Muhammad Rasya Oktaliano'
                ]
            ],
            "Sa'id bin Zaid" => [
                'jk' => 'L',
                'role' => 'siswa_sd',
                'siswa' => [
                    'Khairul Anam Ramadhan',
                    'Muhammad Arda Sharique',
                    'Muhammad Ghani Adi Putra',
                    'Muhammad Rasyantoro Harsoyo',
                    'Nu\'man Muhammad Al Fayyadh',
                    'Raditya Gunawan',
                    'Sakti Sulaiman Faruq Pasaribu',
                    'Shalahuddin Shabir Al Ayyubi',
                    'Wildan Danadyaksa',
                    'Yazid Abdurrahman Aufar',
                    'Abizhar Alfarizal'
                ]
            ],

            // --- SD Akhwat ---
            'Khadijah binti Khuwailid' => [
                'jk' => 'P',
                'role' => 'siswa_sd',
                'siswa' => [
                    'Almahyra Gatri Khalila',
                    'Alqisya Humaira Turidi',
                    'Annasya Qaireen Lashira',
                    'Aqila Nur Zahra',
                    'Ashaqueena Mahreen Kurniawan',
                    'Azalia Ar Rayyah',
                    'Azani Humairah Hidayat',
                    'Clemira Arrumaisha Akbar',
                    'Farzana Azzahra',
                    'Fathya Adhania Anwar',
                    'Fatimatuz Zahro',
                    'Ghania Bilqis Syarique'
                ]
            ],
            'Fatimah binti Muhammad' => [
                'jk' => 'P',
                'role' => 'siswa_sd',
                'siswa' => [
                    'Hanania Hafidzah',
                    'Hasna Almira Rahmah',
                    'Kinandita Shaqueen Al Amin',
                    'Laila Nataya Pamuji',
                    'Maulida Laili Fadhliyah',
                    'Mikasa Salsabila Syawalia',
                    'Misilana',
                    'Nadhira Syafara Al Fathunnisa',
                    'Nadira Khoirunisa Ramadhani',
                    'Naila Assyifa Zahro',
                    'Siti Yasmin Mumtazah',
                    'Tsania Ilya Syabani',
                    'Virnie Khairunnisa'
                ]
            ],
        ];

        $nisCounter = 26001;

        foreach ($dataSiswaQuran as $namaKelasQuran => $grup) {
            $kelasQuran = KelasQuran::where('nama_kelas', $namaKelasQuran)->first();
            $kelasQuranId = $kelasQuran ? $kelasQuran->id : null;

            foreach ($grup['siswa'] as $namaSiswa) {
                // 1. Buat atau Update User agar tidak double
                $email = strtolower(str_replace([' ', '\''], ['.', ''], $namaSiswa)) . '@alazharjayaindonesia.sch.id';

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $namaSiswa,
                        'password' => Hash::make('password123'),
                        'role' => $grup['role'] // Dinamis berdasarkan grup (SD/SMP)
                    ]
                );

                // 2. Cek Kelas Biasa (jika ada)
                $kelasRegulerId = null;
                if (isset($kelasBiasaMap[$namaSiswa])) {
                    $kelasReguler = Kelas::where('nama_kelas', $kelasBiasaMap[$namaSiswa])->first();
                    if ($kelasReguler) {
                        $kelasRegulerId = $kelasReguler->id;
                    }
                }

                // 3. Masukkan ke tabel Siswa menggunakan updateOrCreate berdasarkan user_id
                Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nis' => (string) $nisCounter,
                        'nama' => $namaSiswa,
                        'kelas_id' => $kelasRegulerId, // Hanya terisi jika ditemukan
                        'kelas_quran_id' => $kelasQuranId,
                        'jenis_kelamin' => $grup['jk'],
                        'status' => 'aktif'
                    ]
                );

                $nisCounter++;
            }
        }
    }
}
