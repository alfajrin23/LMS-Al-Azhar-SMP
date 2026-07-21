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
        $guruUser = User::where('role', 'guru')->first();
        $guru = Guru::where('user_id', $guruUser->id)->first();
        $mapel = Mapel::first();
        $kelas = Kelas::first();

        // 1. Guru uploads incomplete material (no description, no class)
        $file = UploadedFile::fake()->create('modul_matematika.pdf', 100);
        $response1 = $this->actingAs($guruUser)->post('/guru/materi', [
            'judul' => 'Bab 1 Aljabar',
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

        // 2. Guru updates the material to meet all checklist criteria (has class & desc >= 20 chars)
        $response2 = $this->actingAs($guruUser)->post("/guru/materi/{$materi->id}/update", [
            'judul' => 'Bab 1 Aljabar Revision',
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
            'status' => 'pending', // Becomes pending (waiting for review)
        ]);

        // 3. Admin approves the material
        $adminUser = User::where('role', 'admin')->first();
        $response3 = $this->actingAs($adminUser)->post("/admin/materi/{$materi->id}/approve");

        $response3->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'id' => $materi->id,
            'status' => 'approved',
        ]);

        // 4. Student in the same class views the mapel page and sees the approved material
        $siswaUser = User::where('role', 'siswa')->first();
        $siswa = Siswa::where('user_id', $siswaUser->id)->first();
        // Set student class to match material class
        $siswa->update(['kelas_id' => $kelas->id]);

        $response4 = $this->actingAs($siswaUser)->get('/dashboard?tab=mapel');
        $response4->assertOk();
        $response4->assertSee('Bab 1 Aljabar Revision');
    }
}
