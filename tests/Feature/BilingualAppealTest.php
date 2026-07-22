<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\BandingNilai;
use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualAppealTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_siswa_can_submit_appeal()
    {
        $siswaUser = User::where('email', 'siswa.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('user_id', $siswaUser->id)->firstOrFail();
        $mapel = Mapel::where('kode', 'MTK')->firstOrFail();

        $nilai = Nilai::updateOrCreate(
            ['siswa_id' => $siswa->id, 'mapel_id' => $mapel->id, 'jenis_nilai' => 'unggulan'],
            ['nilai' => 85.00, 'nilai_bahasa' => 70.00]
        );

        BandingNilai::where('nilai_id', $nilai->id)->delete();

        $response = $this
            ->actingAs($siswaUser)
            ->post('/siswa/banding', [
                'nilai_id' => $nilai->id,
                'alasan_siswa' => 'Saya merasa nilai bahasa Inggris saya harus ditinjau kembali.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('banding_nilai', [
            'nilai_id' => $nilai->id,
            'siswa_id' => $siswa->id,
            'alasan_siswa' => 'Saya merasa nilai bahasa Inggris saya harus ditinjau kembali.',
            'status' => 'pending',
        ]);
    }

    public function test_guru_can_approve_appeal_and_update_grade()
    {
        $siswaUser = User::where('email', 'siswa.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('user_id', $siswaUser->id)->firstOrFail();
        $mapel = Mapel::where('kode', 'MTK')->firstOrFail();
        $guruUser = User::where('email', 'guru.demo@alazharjayaindonesia.sch.id')->firstOrFail();

        $nilai = Nilai::updateOrCreate(
            ['siswa_id' => $siswa->id, 'mapel_id' => $mapel->id, 'jenis_nilai' => 'unggulan'],
            ['nilai' => 85.00, 'nilai_bahasa' => 70.00]
        );

        $banding = BandingNilai::updateOrCreate(
            ['nilai_id' => $nilai->id],
            [
                'siswa_id' => $siswa->id,
                'alasan_siswa' => 'Tolong ditinjau Ustadzah.',
                'status' => 'pending'
            ]
        );

        $response = $this
            ->actingAs($guruUser)
            ->post("/guru/banding/{$banding->id}/proses", [
                'status' => 'disetujui',
                'catatan_guru' => 'Nilai bahasa Inggris disesuaikan.',
                'nilai_bahasa' => 80.00,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('banding_nilai', [
            'id' => $banding->id,
            'status' => 'disetujui',
            'catatan_guru' => 'Nilai bahasa Inggris disesuaikan.',
        ]);
        $this->assertDatabaseHas('nilai', [
            'id' => $nilai->id,
            'nilai_bahasa' => 80.00,
        ]);
    }

    public function test_other_guru_cannot_process_appeal()
    {
        $siswaUser = User::where('email', 'siswa.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('user_id', $siswaUser->id)->firstOrFail();
        $mapel = Mapel::where('kode', 'MTK')->firstOrFail();
        $otherGuruUser = Guru::whereDoesntHave('jadwal', fn ($query) => $query->where('kelas_id', $siswa->kelas_id))
            ->with('user')
            ->firstOrFail()
            ->user;

        $nilai = Nilai::updateOrCreate(
            ['siswa_id' => $siswa->id, 'mapel_id' => $mapel->id, 'jenis_nilai' => 'unggulan'],
            ['nilai' => 85.00, 'nilai_bahasa' => 70.00]
        );

        $banding = BandingNilai::updateOrCreate(
            ['nilai_id' => $nilai->id],
            [
                'siswa_id' => $siswa->id,
                'alasan_siswa' => 'Tolong ditinjau Ustadzah.',
                'status' => 'pending'
            ]
        );

        $response = $this
            ->actingAs($otherGuruUser)
            ->post("/guru/banding/{$banding->id}/proses", [
                'status' => 'disetujui',
                'catatan_guru' => 'Mencoba merubah nilai mapel lain.',
                'nilai_bahasa' => 90.00,
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('banding_nilai', [
            'id' => $banding->id,
            'status' => 'pending',
        ]);
    }
}
