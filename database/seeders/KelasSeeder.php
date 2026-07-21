<?php
namespace Database\Seeders;
use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $sdKelasIds = Kelas::query()->where('jenjang', 'SD')->pluck('id');
        if ($sdKelasIds->isNotEmpty()) {
            \App\Models\Materi::query()->whereIn('kelas_id', $sdKelasIds)->update(['kelas_id' => null]);
            \App\Models\Workbook::query()->whereIn('kelas_id', $sdKelasIds)->update(['kelas_id' => null]);
            \App\Models\CbtExam::query()->whereIn('kelas_id', $sdKelasIds)->update(['kelas_id' => null]);
            \App\Models\OlympiadExam::query()->whereIn('kelas_id', $sdKelasIds)->update(['kelas_id' => null]);
            Kelas::query()->whereIn('id', $sdKelasIds)->delete();
        }
        $kelas = [
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
