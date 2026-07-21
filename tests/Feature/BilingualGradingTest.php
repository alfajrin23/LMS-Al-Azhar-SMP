<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Nilai;
use Tests\TestCase;

class BilingualGradingTest extends TestCase
{
    public function test_guru_can_save_bilingual_grades()
    {
        $guruUser = User::where('email', 'dewi.sartika@alazharjayaindonesia.sch.id')->first();
        $siswa = Siswa::where('nis', '2024001')->first();
        $mapel = Mapel::where('kode', 'MTK')->first();

        // Hapus nilai yang ada untuk menjamin test berjalan bersih
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
