<?php
use App\Models\Materi;
use App\Support\SmpLearningDocumentInventory;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('smp-documents:inventory {--source=} {--output=docs/inventaris-dokumen-pembelajaran-smp.md}', function () {
    $inventory = new SmpLearningDocumentInventory();
    $source = $this->option('source') ?: null;
    $output = base_path($this->option('output'));
    $rows = $inventory->writeMarkdown($output, $source);

    $this->info('Inventaris dokumen SMP dibuat: '.$output);
    $this->line('Total file: '.count($rows));
    $this->line('Terkategorikan: '.collect($rows)->where('classification_status', 'TERKLASIFIKASI')->count());
    $this->line('Perlu verifikasi: '.collect($rows)->where('classification_status', 'PERLU VERIFIKASI')->count());
})->purpose('Membaca folder dokumen SMP dan membuat laporan inventaris checksum.');

Artisan::command('smp-documents:import', function () {
    $this->call('db:seed', ['--class' => \Database\Seeders\SmpLearningDocumentSeeder::class]);
})->purpose('Mengimpor dokumen pembelajaran SMP terklasifikasi ke tabel materi.');

Artisan::command('smp-documents:verify {--source=}', function () {
    $inventory = new SmpLearningDocumentInventory();
    $rows = collect($inventory->inventory($this->option('source') ?: null))->where('importable', true);
    $failed = 0;

    foreach ($rows as $row) {
        $target = storage_path('app/public/'.$row['storage_path']);
        $materiExists = Materi::query()
            ->where('file_path', $row['storage_path'])
            ->where('status', 'approved')
            ->exists();

        if (!is_file($target) || hash_file('sha256', $target) !== $row['checksum_sha256'] || !$materiExists) {
            $failed++;
            $this->error('Gagal verifikasi: '.$row['original_filename']);
        }
    }

    if ($failed === 0) {
        $this->info('Semua dokumen SMP terimpor lolos verifikasi checksum.');
    }

    return $failed === 0 ? Command::SUCCESS : Command::FAILURE;
})->purpose('Memastikan file hasil import sama dengan sumber dan record materi tersedia.');
