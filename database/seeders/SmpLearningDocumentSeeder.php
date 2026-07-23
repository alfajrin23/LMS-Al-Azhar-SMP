<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\MateriApprovalHistory;
use App\Models\User;
use App\Support\SmpLearningDocumentInventory;
use Illuminate\Database\Seeder;

class SmpLearningDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $inventory = new SmpLearningDocumentInventory();
        $rows = collect($inventory->inventory());

        if ($rows->isEmpty()) {
            $this->command?->warn('Folder sumber dokumen SMP tidak ditemukan; import dokumen dilewati.');
            return;
        }

        $this->ensureSubjects($rows->where('importable', true));

        $actor = User::query()
            ->whereIn('role', ['kepala_sekolah', 'admin'])
            ->orderByRaw("case when role = 'kepala_sekolah' then 0 else 1 end")
            ->first();

        foreach ($rows->where('importable', true) as $row) {
            $mapel = Mapel::query()->where('nama_mapel', $row['subject_name'])->first();
            $classes = $this->classesForGrade((int) $row['grade_level']);

            if (!$mapel || $classes->isEmpty()) {
                continue;
            }

            $guru = $this->teacherForSubject($row['subject_name'], $mapel);
            if (!$guru) {
                continue;
            }

            $guru->mapels()->syncWithoutDetaching([$mapel->id]);
            $inventory->copyToPublicStorage($row);

            foreach ($classes as $kelas) {
                $materi = Materi::updateOrCreate(
                    [
                        'kode' => $row['code_prefix'].'-'.$kelas->kode_kelas,
                    ],
                    [
                        'judul' => $row['title'],
                        'deskripsi' => $this->descriptionFor($row, $kelas),
                        'file_path' => $row['storage_path'],
                        'tipe' => 'dokumen_pembelajaran_smp',
                        'kategori' => $row['category'],
                        'isi' => 'Dokumen asli dari folder MATERI SMP AL AZHAR. File tidak dikonversi dan tidak diubah.',
                        'mapel_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'guru_id' => $guru->id,
                        'tahun_ajaran' => $row['academic_year'] ?? '2026/2027',
                        'semester' => $row['semester'] ?? 'Ganjil & Genap',
                        'versi' => 1,
                        'status' => 'approved',
                        'submitted_at' => now(),
                        'reviewed_at' => now(),
                        'reviewed_by' => $actor?->id,
                        'catatan_reviewer' => 'Import otomatis dokumen pembelajaran SMP final.',
                    ]
                );

                MateriApprovalHistory::firstOrCreate(
                    [
                        'materi_id' => $materi->id,
                        'action' => 'imported',
                        'status_to' => 'approved',
                    ],
                    [
                        'actor_id' => $actor?->id,
                        'status_from' => null,
                        'catatan' => 'Seeder import dokumen SMP dari file asli.',
                    ]
                );
            }
        }

        $this->command?->info('Dokumen SMP berhasil diimpor: '.$rows->where('importable', true)->count().' file sumber terklasifikasi.');
    }

    private function ensureSubjects($rows): void
    {
        foreach ($rows->pluck('subject_name')->filter()->unique() as $subjectName) {
            Mapel::updateOrCreate(
                ['nama_mapel' => $subjectName],
                ['kode' => SmpLearningDocumentInventory::subjectCodes()[$subjectName] ?? null]
            );
        }
    }

    private function classesForGrade(int $grade)
    {
        return Kelas::query()
            ->where('jenjang', 'SMP')
            ->where(function ($query) use ($grade) {
                $query->where('kode_kelas', (string) $grade)
                    ->orWhere('kode_kelas', 'like', $grade.'%')
                    ->orWhere('nama_kelas', 'like', 'Kelas '.$grade.'%');
            })
            ->orderBy('kode_kelas')
            ->get();
    }

    private function teacherForSubject(string $subjectName, Mapel $mapel): ?Guru
    {
        $preferred = [
            'Bahasa Indonesia' => 'Adinda Oktavhiani S.Pd',
            'Al Quran Hadits' => 'Adinda Oktavhiani S.Pd',
            'Aqidah/Akhlak' => 'Nurhayati, S.Ag',
            'Fiqh' => 'Nurhayati, S.Ag',
            'Pendidikan Agama Islam' => 'Nurhayati, S.Ag',
            'Matematika' => 'Ajeng Putyri Aryantika, S.Pd',
            'Ilmu Pengetahuan Alam' => 'Riva Riana, S.Pd',
            'Teknologi Digital' => 'Riva Riana, S.Pd',
            'PJOK' => 'Riva Riana, S.Pd',
            'Project Based Learning' => 'Vika Wati Dzulciha, S.Ag',
            'Ilmu Pengetahuan Sosial' => 'Sri Wanti Maulani, S.Pd',
            'Pendidikan Kewarganegaraan' => 'Ai Sunariah, S.Pd',
            'Seni Rupa' => 'Ai Sunariah, S.Pd',
            'Seni Budaya dan Prakarya' => 'Ai Sunariah, S.Pd',
        ];

        $guru = isset($preferred[$subjectName])
            ? Guru::query()->where('nama', $preferred[$subjectName])->first()
            : null;

        return $guru
            ?? Guru::query()->whereHas('mapels', fn ($query) => $query->where('mapel.id', $mapel->id))->first()
            ?? Guru::query()->where('status', 'aktif')->first()
            ?? Guru::query()->first();
    }

    private function descriptionFor(array $row, Kelas $kelas): string
    {
        $parts = [
            'Dokumen pembelajaran SMP final untuk '.$kelas->nama_kelas.'.',
            'File asli: '.$row['original_filename'].'.',
            'Kategori: '.$row['category_label'].'.',
            'Checksum SHA-256: '.$row['checksum_sha256'].'.',
        ];

        if ($row['notes']) {
            $parts[] = 'Catatan: '.$row['notes'];
        }

        return implode(' ', $parts);
    }
}
