<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\CbtExam;
use App\Models\CbtSoal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CbtExamGeneratorTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_cbt_exam_automatic_generation_and_printing()
    {
        $guruUser = User::where('role', 'guru')->first();
        $guru = Guru::where('user_id', $guruUser->id)->first();
        $mapel = Mapel::first();
        $kelas = Kelas::first();
        $examBase = CbtExam::create([
            'judul' => 'Base Exam Bank',
            'tipe' => 'ulangan',
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'durasi' => 60,
            'status' => 'approved',
        ]);
        for ($i = 1; $i <= 3; $i++) {
            CbtSoal::create([
                'cbt_exam_id' => $examBase->id,
                'nomor' => $i,
                'soal' => "Soal Mudah ke-$i",
                'tipe' => 'pg',
                'pilihan_a' => 'A', 'pilihan_b' => 'B', 'pilihan_c' => 'C', 'pilihan_d' => 'D',
                'jawaban_benar' => 'a',
                'kesulitan' => 'mudah',
            ]);
        }
        for ($i = 4; $i <= 8; $i++) {
            CbtSoal::create([
                'cbt_exam_id' => $examBase->id,
                'nomor' => $i,
                'soal' => "Soal Sedang ke-$i",
                'tipe' => 'pg',
                'pilihan_a' => 'A', 'pilihan_b' => 'B', 'pilihan_c' => 'C', 'pilihan_d' => 'D',
                'jawaban_benar' => 'b',
                'kesulitan' => 'sedang',
            ]);
        }
        for ($i = 9; $i <= 10; $i++) {
            CbtSoal::create([
                'cbt_exam_id' => $examBase->id,
                'nomor' => $i,
                'soal' => "Soal Sulit ke-$i",
                'tipe' => 'pg',
                'pilihan_a' => 'A', 'pilihan_b' => 'B', 'pilihan_c' => 'C', 'pilihan_d' => 'D',
                'jawaban_benar' => 'c',
                'kesulitan' => 'sulit',
            ]);
        }
        $response = $this->actingAs($guruUser)->post('/guru/cbt', [
            'judul' => 'Ujian Hasil Generator',
            'tipe' => 'uts',
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'durasi' => 90,
            'metode' => 'cetak',
            'generate_otomatis' => 'on',
            'jumlah_soal_gen' => 10,
            'persen_mudah' => 30,
            'persen_sedang' => 50,
            'persen_sulit' => 20,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('cbt_exams', [
            'judul' => 'Ujian Hasil Generator',
            'metode' => 'cetak',
            'jumlah_soal' => 10,
        ]);
        $newExam = CbtExam::where('judul', 'Ujian Hasil Generator')->first();
        $this->assertEquals(10, $newExam->soals()->count());
        $this->assertEquals(3, $newExam->soals()->where('kesulitan', 'mudah')->count());
        $this->assertEquals(5, $newExam->soals()->where('kesulitan', 'sedang')->count());
        $this->assertEquals(2, $newExam->soals()->where('kesulitan', 'sulit')->count());
        $printResponse = $this->actingAs($guruUser)->get("/guru/cbt/{$newExam->id}/print");
        $printResponse->assertOk();
        $printResponse->assertSee('Lembar Kunci Jawaban');
    }
}
