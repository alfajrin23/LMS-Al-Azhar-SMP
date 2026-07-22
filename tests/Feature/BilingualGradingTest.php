<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Nilai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualGradingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guru_can_save_bilingual_grades()
    {
        $guruUser = User::where('email', 'guru.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('nis', 'SISWA-DEMO-001')->firstOrFail();
        $mapel = Mapel::where('kode', 'ING')->firstOrFail();

        Nilai::where([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'jenis_nilai' => 'unggulan',
        ])->delete();

        $response = $this
            ->actingAs($guruUser)
            ->post('/guru/nilai', [
                'siswa_id' => $siswa->id,
                'mapel_id' => $mapel->id,
                'nilai' => 92.50,
                'jenis_nilai' => 'unggulan',
                'nilai_bahasa' => 78.00,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('nilai', [
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'jenis_nilai' => 'unggulan',
            'nilai' => 92.50,
            'nilai_bahasa' => 78.00,
        ]);
    }
}
