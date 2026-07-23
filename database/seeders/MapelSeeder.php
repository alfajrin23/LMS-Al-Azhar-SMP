<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Mapel;
class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapels = [
            ['nama' => 'Matematika', 'kode' => 'MTK'],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'B.IND'],
            ['nama' => 'Bahasa Inggris', 'kode' => 'ING'],
            ['nama' => 'Bahasa Arab', 'kode' => 'ARB'],
            ['nama' => 'Ilmu Pengetahuan Alam', 'kode' => 'IPA'],
            ['nama' => 'Ilmu Pengetahuan Sosial', 'kode' => 'IPS'],
            ['nama' => 'Pendidikan Agama Islam', 'kode' => 'PAI'],
            ['nama' => 'Al Quran Hadits', 'kode' => 'AQH'],
            ['nama' => 'Tahfidzul Qur\'an', 'kode' => 'TAH'],
            ['nama' => 'Aqidah', 'kode' => 'AQD'],
            ['nama' => 'Fiqh', 'kode' => 'FQH'],
            ['nama' => 'PJOK', 'kode' => 'PJOK'],
            ['nama' => 'Teknologi Digital', 'kode' => 'TKD'],
            ['nama' => 'Seni Rupa', 'kode' => 'SNR'],
            ['nama' => 'Seni Budaya dan Prakarya', 'kode' => 'SBP'],
            ['nama' => 'Project Based Learning', 'kode' => 'PBL'],
            ['nama' => 'Karya Tulis', 'kode' => 'KT'],
            ['nama' => 'Karya Tulis Ilmiah', 'kode' => 'KTI'],
            ['nama' => 'BPI', 'kode' => 'BPI'],
            ['nama' => 'Baca Tulis Quran', 'kode' => 'BTQ'],
            ['nama' => 'Pramuka', 'kode' => 'PRM'],
            ['nama' => 'Istirahat', 'kode' => 'IST'],
            ['nama' => 'Dzuhur Time', 'kode' => 'DZH'],
            ['nama' => 'Ashar Time', 'kode' => 'ASHR_SMP'],
            ['nama' => 'Upacara / Flash', 'kode' => 'UPCR_SMP'],
            ['nama' => 'Upacara / PAS Mantap', 'kode' => 'UPCR_PAS'],
            ['nama' => 'Apel, Dhuha & Muroja\'ah', 'kode' => 'ADM'],
            ['nama' => 'Shalat Ashar dan Dzikir', 'kode' => 'ASHR_DZK'],
            ['nama' => 'Ekskul', 'kode' => 'EKS'],
            ['nama' => 'Dhuha Time', 'kode' => 'DHUHA'],
            ['nama' => 'Upacara / Pentas Seni', 'kode' => 'UPCR'],
            ['nama' => 'Qailullah', 'kode' => 'QAIL'],
            ['nama' => 'Sholat dan Makan', 'kode' => 'ISHOMA'],
            ['nama' => 'Pulang / Penjemputan Orang Tua', 'kode' => 'PLG'],
            ['nama' => 'Snack Time', 'kode' => 'SNCK'],
            ['nama' => 'IPAS', 'kode' => 'IPAS'],
            ['nama' => 'Qadhaya Rawa\'i', 'kode' => 'QRW'],
            ['nama' => 'Transisi / Pindah ke Kelas', 'kode' => 'TRNS'],
            ['nama' => 'Aqidah/Akhlak', 'kode' => 'AKHLAK'],
            ['nama' => 'Kegiatan Pramuka', 'kode' => 'KPRM'],
            ['nama' => 'Bina Pribadi Islam', 'kode' => 'BPI'],
            ['nama' => 'TIK', 'kode' => 'TIK'],
            ['nama' => 'Pendidikan Kewarganegaraan', 'kode' => 'PKN'],
            ['nama' => 'Shalat Ashar', 'kode' => 'ASHR'],
        ];
        foreach ($mapels as $mapel) {
            Mapel::updateOrCreate(
                ['nama_mapel' => $mapel['nama']],
                ['kode' => $mapel['kode']]
            );
        }
    }
}
