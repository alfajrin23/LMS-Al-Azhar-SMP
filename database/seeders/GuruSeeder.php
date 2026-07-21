<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;
use App\Models\Mapel;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $gurus = [
            'Sahrial Pulungan, M.Pd' => ['Karya Tulis Ilmiah', 'Karya Tulis'],
            'Riva Riana, S.Pd' => ['Teknologi Digital', 'Ilmu Pengetahuan Alam', 'PJOK'],
            'Adinda Oktavhiani S.Pd' => ['Bahasa Arab', 'Bahasa Indonesia', 'Baca Tulis Quran'],
            'Intan Kusuma Dera, S.Ag' => ['Tahfidzul Qur\'an'],
            'Vika Wati Dzulciha, S.Ag' => ['Project Based Learning'],
            'Irnika Widiyan Dini, S.Li' => ['Bahasa Inggris'],
            'Ajeng Putyri Aryantika, S.Pd' => ['Matematika'],
            'Nurhayati, S.Ag' => ['Aqidah', 'Pendidikan Agama Islam', 'Fiqh'],
            // Guru Piket
            'Sri Wanti Maulani, S.Pd' => [],
            'Khairunisa' => [],
            'Ai Sunariah, S.Pd' => [],

            // Guru SD Dummy
            'Guru Pendamping SD' => [
                'Dhuha Time',
                'Upacara / Pentas Seni',
                'Tahfidzul Qur\'an',
                'Bahasa Indonesia',
                'Matematika',
                'Qailullah',
                'Sholat dan Makan',
                'Pulang / Penjemputan Orang Tua',
                'Fiqh',
                'Kegiatan Pramuka',
                'Transisi / Pindah ke Kelas',
                'Bahasa Inggris',
                'Aqidah/Akhlak',
                'PJOK',
                'Bina Pribadi Islam',
                'Qadhaya Rawa\'i',
                'IPAS',
                'Bahasa Arab'
            ]
        ];

        $nipCounter = 1001;

        foreach ($gurus as $namaGuru => $mapels) {
            $email = strtolower(str_replace([' ', ',', '.'], ['', '', ''], $namaGuru)) . '@alazharjayaindonesia.sch.id';

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $namaGuru,
                    'password' => Hash::make('password123'),
                    'role' => 'guru'
                ]
            );

            $guru = Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => (string) $nipCounter,
                    'nama' => $namaGuru,
                    'status' => 'aktif'
                ]
            );

            $nipCounter++;

            // Sync Mapels via pivot table
            $mapelIds = [];
            foreach ($mapels as $namaMapel) {
                $mapel = Mapel::query()->where('nama_mapel', $namaMapel)->first();
                if ($mapel) {
                    $mapelIds[] = $mapel->id;
                }
            }
            $guru->mapels()->sync($mapelIds);
        }
    }
}
