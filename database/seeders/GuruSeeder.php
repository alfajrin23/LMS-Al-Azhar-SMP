<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;
use App\Models\Mapel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $this->correctKhoirunnisa();

        $gurus = [
            'Sahrial Pulungan, M.Pd' => ['Karya Tulis Ilmiah', 'Karya Tulis'],
            'Riva Riana, S.Pd' => ['Teknologi Digital', 'Ilmu Pengetahuan Alam', 'PJOK'],
            'Adinda Oktavhiani S.Pd' => ['Bahasa Arab', 'Bahasa Indonesia', 'Baca Tulis Quran'],
            'Intan Kusuma Dera, S.Ag' => ['Tahfidzul Qur\'an'],
            'Vika Wati Dzulciha, S.Ag' => ['Project Based Learning'],
            'Irnika Widiyan Dini, S.Li' => ['Bahasa Inggris'],
            'Ajeng Putyri Aryantika, S.Pd' => ['Matematika'],
            'Nurhayati, S.Ag' => ['Aqidah', 'Pendidikan Agama Islam', 'Fiqh'],
            'Sri Wanti Maulani, S.Pd' => [],
            'Khoirunnisa, S.Ag' => [],
            'Ai Sunariah, S.Pd' => [],
        ];
        $sdGuru = Guru::query()->where('nama', 'Guru Pendamping SD')->first();
        if ($sdGuru) {
            $fallbackGuruId = Guru::query()
                ->where('id', '!=', $sdGuru->id)
                ->value('id');
            \App\Models\Tugas::query()->where('guru_id', $sdGuru->id)->delete();
            \App\Models\Jadwal::query()->where('guru_id', $sdGuru->id)->delete();
            if ($fallbackGuruId) {
                \App\Models\CatatanWali::query()
                    ->where('created_by', $sdGuru->id)
                    ->update(['created_by' => $fallbackGuruId]);
                \App\Models\Workbook::query()
                    ->where('guru_id', $sdGuru->id)
                    ->update(['guru_id' => $fallbackGuruId]);
                \App\Models\Materi::query()
                    ->where('guru_id', $sdGuru->id)
                    ->update(['guru_id' => $fallbackGuruId]);
                \App\Models\CbtExam::query()
                    ->where('guru_id', $sdGuru->id)
                    ->update(['guru_id' => $fallbackGuruId]);
                \App\Models\OlympiadExam::query()
                    ->where('guru_id', $sdGuru->id)
                    ->update(['guru_id' => $fallbackGuruId]);
            }
            $sdGuru->user?->delete();
        }
        $nipCounter = 1001;
        foreach ($gurus as $namaGuru => $mapels) {
            $email = $this->emailFor($namaGuru);
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $namaGuru,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'role' => 'guru',
                ]);
            } else {
                $user->update([
                    'name' => $namaGuru,
                    'role' => 'guru',
                ]);
            }

            $guru = Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => (string) $nipCounter,
                    'nama' => $namaGuru,
                    'status' => 'aktif'
                ]
            );
            $nipCounter++;
            $mapelIds = [];
            foreach ($mapels as $namaMapel) {
                $mapel = Mapel::query()->where('nama_mapel', $namaMapel)->first();
            if ($mapel) {
                    $mapelIds[] = $mapel->id;
                }
            }
            $guru->mapels()->sync($mapelIds);
            if (!$guru->mapel_id && !empty($mapelIds)) {
                $guru->update(['mapel_id' => $mapelIds[0]]);
            }
        }
    }

    private function correctKhoirunnisa(): void
    {
        $user = User::where('email', 'khairunisa@alazharjayaindonesia.sch.id')
            ->orWhere('email', 'khoirunnisa@alazharjayaindonesia.sch.id')
            ->orWhere('email', 'khoirunnisa.sag@alazharjayaindonesia.sch.id')
            ->orWhere('name', 'Khairunisa')
            ->first();

        if (!$user) {
            return;
        }

        $user->update([
            'name' => 'Khoirunnisa, S.Ag',
            'email' => 'khoirunnisa@alazharjayaindonesia.sch.id',
            'role' => 'guru',
        ]);

        Guru::where('user_id', $user->id)
            ->orWhere('nama', 'Khairunisa')
            ->update(['nama' => 'Khoirunnisa, S.Ag']);
    }

    private function emailFor(string $name): string
    {
        if ($name === 'Khoirunnisa, S.Ag') {
            return 'khoirunnisa@alazharjayaindonesia.sch.id';
        }

        return Str::of($name)
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]/', '')
            ->squish()
            ->replace(' ', '.')
            ->append('@alazharjayaindonesia.sch.id')
            ->toString();
    }
}
