<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\BandingNilai;
use App\Models\Guru;
use Tests\TestCase;

class BilingualAppealTest extends TestCase
{
    public function test_siswa_can_submit_appeal()
    {
        $siswaUser = User::where('email', 'ahmad.rizky@alazharjayaindonesia.sch.id')->first();
        $siswa = Siswa::where('user_id', $siswaUser->id)->first();
        $mapel = Mapel::where('kode', 'MTK')->first();

        // Create a grade with bilingual component
        $nilai = Nilai::updateOrCreate(
            ['siswa_id' => $siswa->id, 'mapel_id' => $mapel->id, 'jenis_nilai' => 'unggulan'],
            ['nilai' => 85.00, 'nilai_bahasa' => 70.00]
        );

        // Delete any existing banding record
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
        $siswaUser = User::where('email', 'ahmad.rizky@alazharjayaindonesia.sch.id')->first();
        $siswa = Siswa::where('user_id', $siswaUser->id)->first();
        $mapel = Mapel::where('kode', 'MTK')->first();
        $guruUser = User::where('email', 'dewi.sartika@alazharjayaindonesia.sch.id')->first(); // MTK Teacher

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
        $siswaUser = User::where('email', 'ahmad.rizky@alazharjayaindonesia.sch.id')->first();
        $siswa = Siswa::where('user_id', $siswaUser->id)->first();
        $mapel = Mapel::where('kode', 'MTK')->first();
        $otherGuruUser = User::where('email', 'linda.wijaya@alazharjayaindonesia.sch.id')->first(); // ING Teacher (not MTK)

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
