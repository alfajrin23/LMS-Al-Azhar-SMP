<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KelasQuran;
class KelasQuranSeeder extends Seeder
{
    public function run(): void
    {
        KelasQuran::query()->where('jenjang', 'SD')->delete();
        KelasQuran::query()
            ->whereIn('nama_kelas', ['Imam Nafi bin Abdurrahman', 'Sutayta Al-Mahamali'])
            ->delete();
        $kelasQuran = [
            ['nama_kelas' => 'Imam Nafi’ bin Abdurrahman', 'jenjang' => 'SMP', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 8-9'],
            ['nama_kelas' => 'Imam Abdullah bin Katsir', 'jenjang' => 'SMP', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 8-9'],
            ['nama_kelas' => 'Imam Ashim bin Abi Al-Najud', 'jenjang' => 'SMP', 'kategori' => 'Ikhwan', 'tingkat' => 'Kelas 7'],
            ['nama_kelas' => 'Sutayta Al Mahamali', 'jenjang' => 'SMP', 'kategori' => 'Akhwat', 'tingkat' => 'Kelas 8-9'],
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
