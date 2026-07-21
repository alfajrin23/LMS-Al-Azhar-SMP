<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KelasQuran;

class KelasQuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasQuran = [
            // SD Ikhwan
            ['nama_kelas' => 'Abu Ubaidah bin Al-Jarrah', 'jenjang' => 'SD', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 1-3'],
            ['nama_kelas' => "Sa'id bin Zaid", 'jenjang' => 'SD', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 1-3'],
            ['nama_kelas' => 'Abdurrahman bin Auf', 'jenjang' => 'SD', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 4'],
            ['nama_kelas' => 'Zubair bin Awwam', 'jenjang' => 'SD', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 5-6'],
            ['nama_kelas' => 'Thalhah bin Ubaidillah', 'jenjang' => 'SD', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 5-6'],

            // SD Akhwat
            ['nama_kelas' => 'Khadijah binti Khuwailid', 'jenjang' => 'SD', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 1-3'],
            ['nama_kelas' => 'Fatimah binti Muhammad', 'jenjang' => 'SD', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 1-3'],
            ['nama_kelas' => 'Maryam binti Imran', 'jenjang' => 'SD', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 4'],
            ['nama_kelas' => 'Aisyah binti Abu Bakar', 'jenjang' => 'SD', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 4'],
            ['nama_kelas' => 'Hafsah binti Umar', 'jenjang' => 'SD', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 5-6'],
            ['nama_kelas' => 'Sumayyah binti Khayyat', 'jenjang' => 'SD', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 5-6'],

            // SMP Ikhwan
            ['nama_kelas' => 'Imam Nafi bin Abdurrahman', 'jenjang' => 'SMP', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 8-9'],
            ['nama_kelas' => 'Imam Abdullah bin Katsir', 'jenjang' => 'SMP', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 8-9'],
            ['nama_kelas' => 'Imam Ashim bin Abi Al-Najud', 'jenjang' => 'SMP', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 7'],

            // SMP Akhwat
            ['nama_kelas' => 'Sutayta Al-Mahamali', 'jenjang' => 'SMP', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 8-9'],
            ['nama_kelas' => 'Zainab binti Ahmad', 'jenjang' => 'SMP', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 8-9'],
            ['nama_kelas' => 'Bina Shaheen Siddiqui', 'jenjang' => 'SMP', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 7'],
        ];

        foreach ($kelasQuran as $item) {
            KelasQuran::updateOrCreate(
                ['nama_kelas' => $item['nama_kelas']], 
                $item
            );
        }
    }
}
