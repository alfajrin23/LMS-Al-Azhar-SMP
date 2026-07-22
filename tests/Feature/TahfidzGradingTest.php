<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\TahfidzSetoran;
use App\Models\TahfidzAyatNilai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahfidzGradingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_two_teachers_can_grade_tahfidz_per_verse()
    {
        $guru1 = User::where('email', 'guru.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $guru2 = User::where('email', 'intan.kusuma.dera.sag@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('nis', 'SISWA-DEMO-001')->firstOrFail();
        $siswa->update(['kelas_id' => null]);

        TahfidzSetoran::where('siswa_id', $siswa->id)->delete();

        $response = $this
            ->actingAs($guru1)
            ->post('/guru/tahfidz', [
                'siswa_id' => $siswa->id,
                'surah' => 'An-Naba\'',
                'ayat_mulai' => 1,
                'ayat_selesai' => 2,
                'jumlah_ayat' => 2,
                'status' => 'baru',
                'tanggal' => '2026-07-04',
                'tanggal_berikutnya' => '2026-07-11',
                'ayat_nilai' => [
                    [
                        'nomor_ayat' => 1,
                        'makhroj' => 4, // 100%
                        'tajwid' => 4,
                        'kelancaran' => 4,
                    ],
                    [
                        'nomor_ayat' => 2,
                        'makhroj' => 3, // 75%
                        'tajwid' => 3,
                        'kelancaran' => 3,
                    ]
                ]
            ]);

        $response->assertRedirect();

        $setoran = TahfidzSetoran::where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($setoran);

        $this->assertEquals(88, $setoran->nilai);
        $this->assertEquals('2026-07-11', $setoran->tanggal_berikutnya->toDateString());

        $response2 = $this
            ->actingAs($guru2)
            ->post("/guru/tahfidz/{$setoran->id}/nilai-pembanding", [
                'ayat_nilai' => [
                    [
                        'nomor_ayat' => 1,
                        'makhroj' => 4, // 100%
                        'tajwid' => 4,
                        'kelancaran' => 4,
                    ],
                    [
                        'nomor_ayat' => 2,
                        'makhroj' => 4, // 100%
                        'tajwid' => 4,
                        'kelancaran' => 4,
                    ]
                ]
            ]);

        $response2->assertRedirect();

        $setoran->refresh();

        $this->assertEquals(94, $setoran->nilai);
    }
}
