<?php

namespace Tests\Feature;

use App\Models\Materi;
use App\Models\User;
use App\Support\SmpLearningDocumentInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmpLearningDocumentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_smp_learning_documents_are_imported_without_changing_original_file(): void
    {
        $inventory = new SmpLearningDocumentInventory();

        if (!is_dir($inventory->defaultSourceDirectory())) {
            $this->markTestSkipped('Folder sumber dokumen SMP tidak tersedia di environment test.');
        }

        $this->seed();

        $rows = collect($inventory->inventory());
        $this->assertCount(95, $rows);
        $this->assertSame(87, $rows->where('classification_status', 'TERKLASIFIKASI')->count());
        $this->assertSame(8, $rows->where('classification_status', 'PERLU VERIFIKASI')->count());

        $row = $rows->firstWhere('original_filename', 'KELAS 7 CP MTK.docx');
        $this->assertNotNull($row);

        $materi = Materi::query()
            ->where('kode', 'like', $row['code_prefix'].'-%')
            ->whereHas('kelas', fn ($query) => $query->where('kode_kelas', '7-DEMO'))
            ->firstOrFail();

        $storedPath = storage_path('app/public/'.$materi->file_path);
        $this->assertFileExists($storedPath);
        $this->assertSame($row['checksum_sha256'], hash_file('sha256', $storedPath));
        $this->assertSame($row['checksum_sha256'], hash_file('sha256', $row['source_path']));

        $siswa = User::query()->where('email', 'siswa.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $response = $this->actingAs($siswa)->get(route('materi.download', $materi));

        $response->assertOk();
        $this->assertSame($row['checksum_sha256'], hash('sha256', $response->streamedContent()));
        $this->assertStringContainsString($row['original_filename'], $response->headers->get('content-disposition'));
    }
}
