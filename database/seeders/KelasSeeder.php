<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = [
            // Jenjang SD
            ['kode_kelas' => '1', 'nama_kelas' => 'Kelas 1', 'jenjang' => 'SD'],
            ['kode_kelas' => '2', 'nama_kelas' => 'Kelas 2', 'jenjang' => 'SD'],
            ['kode_kelas' => '3', 'nama_kelas' => 'Kelas 3', 'jenjang' => 'SD'],
            ['kode_kelas' => '4', 'nama_kelas' => 'Kelas 4', 'jenjang' => 'SD'],
            ['kode_kelas' => '5A', 'nama_kelas' => 'Kelas 5A', 'jenjang' => 'SD'],
            ['kode_kelas' => '5B', 'nama_kelas' => 'Kelas 5B', 'jenjang' => 'SD'],
            ['kode_kelas' => '6A', 'nama_kelas' => 'Kelas 6A', 'jenjang' => 'SD'],
            ['kode_kelas' => '6B', 'nama_kelas' => 'Kelas 6B', 'jenjang' => 'SD'],

            // Jenjang SMP
            ['kode_kelas' => '7', 'nama_kelas' => 'Abu Bakar Ar Razi', 'jenjang' => 'SMP', 'guru_id' => \App\Models\Guru::query()->where('nama', 'Vika Wati Dzulciha, S.Ag')->value('id')],
            ['kode_kelas' => '8A', 'nama_kelas' => 'Ibnu Al Haytam', 'jenjang' => 'SMP'],
            ['kode_kelas' => '8B', 'nama_kelas' => 'Maryam Al Ijliyah', 'jenjang' => 'SMP'],
            ['kode_kelas' => '9A', 'nama_kelas' => 'Al Khawarizmi', 'jenjang' => 'SMP'],
            ['kode_kelas' => '9B', 'nama_kelas' => 'Fatimah Al Fihri', 'jenjang' => 'SMP'],
        ];

        foreach ($kelas as $item) {
            Kelas::updateOrCreate(
                ['kode_kelas' => $item['kode_kelas']],
                $item
            );
        }
    }
}
