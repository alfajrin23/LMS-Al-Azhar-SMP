<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MateriChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_materi_checklist_and_approval_workflow()
    {
        $guruUser = User::where('email', 'guru.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $guru = Guru::where('user_id', $guruUser->id)->firstOrFail();
        $mapel = Mapel::where('kode', 'MTK')->firstOrFail();
        $kelas = Kelas::where('kode_kelas', '7-DEMO')->firstOrFail();

        $file = UploadedFile::fake()->create('modul_matematika.pdf', 100);
        $response1 = $this->actingAs($guruUser)->post('/guru/materi', [
            'judul' => 'Bab 1 Aljabar',
            'kategori' => 'kompetensi_dasar',
            'tipe' => 'materi',
            'mapel_id' => $mapel->id,
            'kelas_id' => '',
            'deskripsi' => '', // Empty
            'file' => $file,
        ]);

        $response1->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'judul' => 'Bab 1 Aljabar',
            'guru_id' => $guru->id,
            'status' => 'draft', // Stays draft since it's incomplete
        ]);

        $materi = Materi::where('judul', 'Bab 1 Aljabar')->first();

        $response2 = $this->actingAs($guruUser)->post("/guru/materi/{$materi->id}/update", [
            'judul' => 'Bab 1 Aljabar Revision',
            'kategori' => 'kompetensi_dasar',
            'tipe' => 'materi',
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'deskripsi' => 'Deskripsi ringkasan materi aljabar linear kelas 7 yang sangat lengkap.', // >= 20 chars
        ]);

        $response2->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'id' => $materi->id,
            'judul' => 'Bab 1 Aljabar Revision',
            'kelas_id' => $kelas->id,
            'status' => 'draft', // Explicit workflow: complete material remains draft until requested
        ]);

        $responseRequest = $this->actingAs($guruUser)->post("/guru/materi/{$materi->id}/request-approval");
        $responseRequest->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'id' => $materi->id,
            'status' => 'pending',
        ]);

        $adminUser = User::where('role', 'admin')->firstOrFail();
        $response3 = $this->actingAs($adminUser)->post("/admin/materi/{$materi->id}/approve");

        $response3->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'id' => $materi->id,
            'status' => 'approved',
        ]);

        $siswaUser = User::where('email', 'siswa.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $siswa = Siswa::where('user_id', $siswaUser->id)->firstOrFail();
        $siswa->update(['kelas_id' => $kelas->id]);

        $response4 = $this->actingAs($siswaUser)->get('/dashboard?tab=mapel');
        $response4->assertOk();
        $response4->assertSee('Bab 1 Aljabar Revision');
    }
}
