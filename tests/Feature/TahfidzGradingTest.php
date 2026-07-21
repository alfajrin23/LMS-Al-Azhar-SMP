<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\TahfidzSetoran;
use App\Models\TahfidzAyatNilai;
use Tests\TestCase;

class TahfidzGradingTest extends TestCase
{
    public function test_two_teachers_can_grade_tahfidz_per_verse()
    {
        $guru1 = User::where('email', 'hadi.prasetyo@alazharjayaindonesia.sch.id')->first();
        $guru2 = User::where('email', 'fitri.handayani@alazharjayaindonesia.sch.id')->first();
        $siswa = Siswa::where('nis', '2024001')->first();

        // Bersihkan data setoran terdahulu untuk siswa ini agar test bersih
        TahfidzSetoran::where('siswa_id', $siswa->id)->delete();

        // 1. Guru 1 membuat setoran baru dan menginput nilai per ayat
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

        // Ambil setoran yang baru dibuat
        $setoran = TahfidzSetoran::where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($setoran);

        // Nilai sementara harus berupa rata-rata dari Guru 1 saja: (100 + 75) / 2 = 87.5% -> round ke 88
        $this->assertEquals(88, $setoran->nilai);
        $this->assertEquals('2026-07-11', $setoran->tanggal_berikutnya);

        // 2. Guru 2 memberikan nilai pembanding
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

        // Refresh data setoran
        $setoran->refresh();

        // Rata-rata Guru 1 = 87.5%
        // Rata-rata Guru 2 = 100%
        // Rata-rata Gabungan = (87.5 + 100) / 2 = 93.75% -> round ke 94
        $this->assertEquals(94, $setoran->nilai);
    }
}
