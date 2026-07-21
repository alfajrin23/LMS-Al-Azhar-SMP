<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Guru;
use App\Models\LaporanMengajar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class LaporanMengajarTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_guru_can_submit_and_update_teaching_reports()
    {
        $guruUser = User::query()->where('role', 'guru')->first();
        $guru = Guru::query()->where('user_id', $guruUser->id)->first();
        $isiHarian1 = [
            'checklists' => [
                'modul_rpp' => ['status' => 'ya', 'catatan' => 'OK'],
                'media_siap' => ['status' => 'ya', 'catatan' => 'OK'],
            ],
            'kendala' => [
                ['bidang' => 'Media', 'deskripsi' => 'Proyektor mati di kelas 9A.', 'dampak' => 'Materi tertunda', 'solusi' => 'Tulis di papan', 'tindak_lanjut' => 'Lapor sarpras']
            ],
            'pemetaan_masalah_siswa' => [],
            'refleksi' => [
                'materi_tersampaikan' => 'Selesai setengah',
                'target_tercapai' => 'Belum',
                'kendala_terbesar' => 'Proyektor mati',
                'strategi_perbaikan' => 'Gunakan spidol',
                'rencana_pertemuan' => 'Lanjutkan materi'
            ]
        ];
        $response1 = $this
            ->actingAs($guruUser)
            ->post('/guru/laporan', [
                'tipe' => 'harian',
                'tanggal' => '2026-07-04',
                'isi' => $isiHarian1,
            ]);
        $response1->assertRedirect();
        $this->assertTrue(
            LaporanMengajar::query()->where('guru_id', $guru->id)
                ->where('tipe', 'harian')
                ->whereDate('tanggal', '2026-07-04')
                ->exists()
        );
        $savedReport = LaporanMengajar::query()->where('guru_id', $guru->id)
            ->where('tipe', 'harian')
            ->whereDate('tanggal', '2026-07-04')
            ->first();
        $this->assertEquals('Proyektor mati di kelas 9A.', $savedReport->isi['kendala'][0]['deskripsi']);
        $isiHarian2 = $isiHarian1;
        $isiHarian2['kendala'][0]['deskripsi'] = 'Proyektor mati di kelas 9A dan ada 2 siswa izin.';
        $response2 = $this
            ->actingAs($guruUser)
            ->post('/guru/laporan', [
                'tipe' => 'harian',
                'tanggal' => '2026-07-04',
                'isi' => $isiHarian2,
            ]);
        $response2->assertRedirect();
        $this->assertEquals(1, LaporanMengajar::query()->where('guru_id', $guru->id)->where('tipe', 'harian')->count());
        $updatedReport = LaporanMengajar::query()->where('guru_id', $guru->id)
            ->where('tipe', 'harian')
            ->whereDate('tanggal', '2026-07-04')
            ->first();
        $this->assertEquals('Proyektor mati di kelas 9A dan ada 2 siswa izin.', $updatedReport->isi['kendala'][0]['deskripsi']);
        $isiMingguan = [
            'rekap_pembelajaran' => [
                ['hari' => 'Senin', 'materi' => 'Menyelesaikan Bab 3 SPLDV', 'kehadiran' => '95%', 'ketuntasan' => '75%', 'hots' => 'Cukup', 'catatan' => 'Lancar']
            ],
            'evaluasi_akademik' => [],
            'analisis_kendala' => [],
            'pemetaan_siswa' => [],
            'tindak_lanjut' => []
        ];
        $isiBulanan = [
            'capaian_belajar_bulanan' => [
                ['elemen_cp' => 'Pengetahuan', 'target' => '75', 'capaian' => '80', 'persentase' => '100%', 'keterangan' => 'Rata-rata pemahaman materi siswa baik.']
            ],
            'evaluasi_dan_kendala' => [],
            'analisis_siswa' => [],
            'pemetaan_masalah_jangka_pendek' => [],
            'pemetaan_masalah_jangka_menengah' => [],
            'monitoring_kinerja_guru' => [],
            'rekomendasi_supervisor' => []
        ];
        $this->actingAs($guruUser)->post('/guru/laporan', [
            'tipe' => 'mingguan',
            'tanggal' => '2026-07-04',
            'isi' => $isiMingguan,
        ]);
        $this->actingAs($guruUser)->post('/guru/laporan', [
            'tipe' => 'bulanan',
            'tanggal' => '2026-07-04',
            'isi' => $isiBulanan,
        ]);
        $reportMingguan = LaporanMengajar::query()->where('guru_id', $guru->id)->where('tipe', 'mingguan')->first();
        $this->assertEquals('Menyelesaikan Bab 3 SPLDV', $reportMingguan->isi['rekap_pembelajaran'][0]['materi']);
        $reportBulanan = LaporanMengajar::query()->where('guru_id', $guru->id)->where('tipe', 'bulanan')->first();
        $this->assertEquals('Rata-rata pemahaman materi siswa baik.', $reportBulanan->isi['capaian_belajar_bulanan'][0]['keterangan']);
    }
    public function test_admin_can_view_teaching_report_status()
    {
        $adminUser = User::query()->where('role', 'admin')->first();
        $response = $this
            ->actingAs($adminUser)
            ->get('/dashboard?tab=audit_guru');
        $response->assertOk();
        $response->assertViewHas('guruReportsData');
    }
}
