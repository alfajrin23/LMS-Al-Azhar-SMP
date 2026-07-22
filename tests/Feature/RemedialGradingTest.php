<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Remedial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemedialGradingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_automatic_remedial_created_when_grade_below_kkm()
    {
        $guruUser = User::where('email', 'guru.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('nis', 'SISWA-DEMO-001')->firstOrFail();
        $mapel = Mapel::where('kode', 'MTK')->firstOrFail();

        Remedial::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->delete();
        Nilai::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->delete();

        $response = $this
            ->actingAs($guruUser)
            ->post('/guru/nilai', [
                'siswa_id' => $siswa->id,
                'mapel_id' => $mapel->id,
                'nilai' => 60,
                'jenis_nilai' => 'biasa',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('nilai', [
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'nilai' => 60.00,
        ]);

        $this->assertDatabaseHas('remedials', [
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'nilai_asal' => 60.00,
            'status' => 'pending',
        ]);
    }

    public function test_remedial_grade_capping_and_resolution()
    {
        $guruUser = User::where('email', 'guru.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('nis', 'SISWA-DEMO-001')->firstOrFail();
        $mapel = Mapel::where('kode', 'MTK')->firstOrFail();

        Remedial::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->delete();
        Nilai::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->delete();

        $nilaiRecord = Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'nilai' => 60.00,
            'jenis_nilai' => 'biasa',
        ]);

        $remedial = Remedial::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'nilai_id' => $nilaiRecord->id,
            'nilai_asal' => 60.00,
            'deadline' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($guruUser)
            ->post('/guru/nilai', [
                'siswa_id' => $siswa->id,
                'mapel_id' => $mapel->id,
                'nilai' => 85,
                'jenis_nilai' => 'biasa',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('nilai', [
            'id' => $nilaiRecord->id,
            'nilai' => 75.00,
        ]);

        $this->assertDatabaseHas('remedials', [
            'id' => $remedial->id,
            'status' => 'selesai',
        ]);
    }
}
