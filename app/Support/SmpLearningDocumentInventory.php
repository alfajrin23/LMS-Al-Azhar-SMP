<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SplFileInfo;
use ZipArchive;

class SmpLearningDocumentInventory
{
    public const STORAGE_DIRECTORY = 'materi-smp';

    private const SUBJECTS = [
        'Bahasa Indonesia' => 'B.IND',
        'Matematika' => 'MTK',
        'Ilmu Pengetahuan Alam' => 'IPA',
        'Ilmu Pengetahuan Sosial' => 'IPS',
        'Pendidikan Kewarganegaraan' => 'PKN',
        'Pendidikan Agama Islam' => 'PAI',
        'Al Quran Hadits' => 'AQH',
        'Aqidah/Akhlak' => 'AKHLAK',
        'Fiqh' => 'FQH',
        'PJOK' => 'PJOK',
        'Teknologi Digital' => 'TKD',
        'Project Based Learning' => 'PBL',
        'Seni Rupa' => 'SNR',
        'Seni Budaya dan Prakarya' => 'SBP',
    ];

    public static function categoryLabels(): array
    {
        return [
            'kompetensi_inti' => 'Kompetensi Inti',
            'kompetensi_dasar' => 'Kompetensi Dasar / KI-KD',
            'capaian_pembelajaran' => 'Capaian Pembelajaran',
            'alur_tujuan_pembelajaran' => 'Alur Tujuan Pembelajaran',
            'alur_tahapan_pembelajaran' => 'Alur Tahapan Pembelajaran',
            'program_tahunan_semester' => 'Program Tahunan/Semester',
            'modul_ajar' => 'Modul Ajar / RPPM',
            'lembar_kerja' => 'Lembar Kerja / LKPD',
            'soal_latihan' => 'Soal Latihan',
            'kisi_kisi' => 'Kisi-Kisi',
            'dokumen_lainnya' => 'Dokumen Lainnya',
        ];
    }

    public static function subjectCodes(): array
    {
        return self::SUBJECTS;
    }

    public function defaultSourceDirectory(): string
    {
        return env('SMP_LEARNING_DOCUMENT_SOURCE')
            ?: dirname(base_path(), 2).DIRECTORY_SEPARATOR.'MATERI SMP AL AZHAR';
    }

    public function inventory(?string $sourceDirectory = null): array
    {
        $sourceDirectory ??= $this->defaultSourceDirectory();

        if (!is_dir($sourceDirectory)) {
            return [];
        }

        return collect(File::allFiles($sourceDirectory))
            ->filter(fn (SplFileInfo $file) => $file->isFile())
            ->sortBy(fn (SplFileInfo $file) => $file->getFilename())
            ->values()
            ->map(fn (SplFileInfo $file) => $this->row($file))
            ->all();
    }

    public function writeMarkdown(string $outputPath, ?string $sourceDirectory = null): array
    {
        $rows = $this->inventory($sourceDirectory);
        File::ensureDirectoryExists(dirname($outputPath));

        $lines = [
            '# Inventaris Dokumen Pembelajaran SMP',
            '',
            'Sumber: `'.($sourceDirectory ?? $this->defaultSourceDirectory()).'`',
            '',
            'File asli tidak dikonversi dan tidak ditulis ulang. Checksum SHA-256 dihitung langsung dari file sumber.',
            '',
            '| No | Nama File Asli | Ekstensi | Ukuran | SHA-256 | Jenis Dokumen | Kelas | Mata Pelajaran | Semester | Tahun Ajaran | Status Klasifikasi | Sensitivitas | Catatan |',
            '| ---: | --- | --- | ---: | --- | --- | --- | --- | --- | --- | --- | --- | --- |',
        ];

        foreach ($rows as $index => $row) {
            $lines[] = implode(' | ', [
                (string) ($index + 1),
                $this->md($row['original_filename']),
                $this->md($row['extension']),
                (string) $row['file_size'],
                '`'.$row['checksum_sha256'].'`',
                $this->md($row['category_label']),
                $this->md($row['grade_label'] ?? 'PERLU VERIFIKASI'),
                $this->md($row['subject_name'] ?? 'PERLU VERIFIKASI'),
                $this->md($row['semester'] ?? 'PERLU VERIFIKASI'),
                $this->md($row['academic_year'] ?? 'PERLU VERIFIKASI'),
                $this->md($row['classification_status']),
                $this->md($row['sensitivity']),
                $this->md($row['notes'] ?: '-'),
            ]);
        }

        File::put($outputPath, implode(PHP_EOL, $lines).PHP_EOL);

        return $rows;
    }

    public function copyToPublicStorage(array $row): void
    {
        if (!is_file($row['source_path'])) {
            return;
        }

        $target = storage_path('app/public/'.$row['storage_path']);
        File::ensureDirectoryExists(dirname($target));

        if (is_file($target) && hash_file('sha256', $target) === $row['checksum_sha256']) {
            return;
        }

        File::copy($row['source_path'], $target);
    }

    private function row(SplFileInfo $file): array
    {
        $path = $file->getRealPath() ?: $file->getPathname();
        $name = $file->getFilename();
        $extension = strtolower($file->getExtension());
        $officeText = $this->extractOfficeText($path, $extension);
        $fileGrade = $this->detectFilenameGrade($name);
        $contentGrade = $this->detectContentGrade($officeText);
        $grade = $fileGrade ?? $contentGrade;
        $subjectFromName = $this->detectSubjectIn($name);
        $subjectFromContent = $this->detectSubjectIn($officeText);
        $subject = $subjectFromName ?? $subjectFromContent;
        $category = $this->detectCategory($name, $officeText);
        $academicYear = $this->detectAcademicYear($officeText) ?? '2026/2027';
        $semester = $this->detectSemester($name, $officeText);
        $notes = [];
        $looksLikeSdDocument = $this->looksLikeSdDocument($officeText);

        if ($looksLikeSdDocument) {
            $notes[] = 'Isi dokumen menyebut SD/MI atau kelas SD; perlu validasi sebelum dipakai di SMP.';
            if ($fileGrade === null) {
                $grade = null;
            }
        }

        if ($contentGrade !== null && $fileGrade !== null && $contentGrade !== $fileGrade) {
            $notes[] = 'Kelas dari nama file berbeda dengan isi; klasifikasi memakai nama file eksplisit.';
        }

        if ($contentGrade !== null && $fileGrade === null && preg_match('/\bSMP\s+(XI|XII|XIII)\b/i', $name)) {
            $notes[] = 'Nama file memakai angka Romawi di luar kelas SMP; isi dokumen dipakai untuk kelas.';
        }

        if (($fileGrade === null && $contentGrade !== null) || ($subjectFromName === null && $subjectFromContent !== null)) {
            $notes[] = 'Mapel/kelas dikonfirmasi dari isi dokumen.';
        }

        if ($subjectFromName && $subjectFromContent && $subjectFromName !== $subjectFromContent) {
            $notes[] = 'Mata pelajaran dari nama file berbeda dengan isi awal; klasifikasi memakai nama file eksplisit.';
        }

        $classificationStatus = ($grade && $subject && $category !== 'dokumen_lainnya')
            ? 'TERKLASIFIKASI'
            : 'PERLU VERIFIKASI';

        if (!$grade) {
            $notes[] = 'Kelas SMP tidak dapat ditentukan dengan aman.';
        }

        if (!$subject) {
            $notes[] = 'Mata pelajaran tidak dapat ditentukan dengan aman.';
        }

        if ($category === 'dokumen_lainnya') {
            $notes[] = 'Jenis dokumen belum cukup jelas dari nama atau isi awal.';
        }

        $checksum = hash_file('sha256', $path);

        return [
            'original_filename' => $name,
            'source_path' => $path,
            'extension' => $extension,
            'file_size' => $file->getSize(),
            'checksum_sha256' => $checksum,
            'title' => $this->titleFromFilename($name),
            'category' => $category,
            'category_label' => self::categoryLabels()[$category] ?? Str::headline($category),
            'grade_level' => $grade,
            'grade_label' => $grade ? 'Kelas '.$grade : null,
            'subject_name' => $subject,
            'subject_code' => $subject ? self::SUBJECTS[$subject] : null,
            'semester' => $semester,
            'academic_year' => $academicYear,
            'classification_status' => $classificationStatus,
            'sensitivity' => $this->sensitivityFor($category),
            'notes' => implode(' ', array_unique($notes)),
            'importable' => $classificationStatus === 'TERKLASIFIKASI',
            'storage_path' => self::STORAGE_DIRECTORY.'/'.($grade ? 'kelas-'.$grade : 'perlu-verifikasi').'/'.$name,
            'code_prefix' => 'SMPDOC-'.strtoupper(substr($checksum, 0, 12)),
        ];
    }

    private function extractOfficeText(string $path, string $extension): string
    {
        if (!in_array($extension, ['docx', 'xlsx'], true) || !class_exists(ZipArchive::class)) {
            return '';
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }

        $text = $extension === 'docx'
            ? $this->extractDocxText($zip)
            : $this->extractXlsxText($zip);

        $zip->close();

        return trim(preg_replace('/\s+/', ' ', html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8')));
    }

    private function extractDocxText(ZipArchive $zip): string
    {
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            return '';
        }

        return strip_tags(str_replace(['</w:p>', '</w:tr>'], ' ', $xml));
    }

    private function extractXlsxText(ZipArchive $zip): string
    {
        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');

        if ($sharedXml !== false && preg_match_all('/<si\b[^>]*>(.*?)<\/si>/s', $sharedXml, $matches)) {
            foreach ($matches[1] as $sharedStringXml) {
                $sharedStrings[] = trim(strip_tags(str_replace(['</t>', '</r>'], ' ', $sharedStringXml)));
            }
        }

        $values = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (!preg_match('#^xl/worksheets/sheet\d+\.xml$#', $entry)) {
                continue;
            }

            $sheetXml = $zip->getFromName($entry);
            if ($sheetXml === false) {
                continue;
            }

            if (!preg_match_all('/<c\b([^>]*)>(.*?)<\/c>/s', $sheetXml, $cells, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($cells as $cell) {
                $attributes = $cell[1];
                $body = $cell[2];
                $type = preg_match('/\bt="([^"]+)"/', $attributes, $typeMatch) ? $typeMatch[1] : null;

                if ($type === 'inlineStr') {
                    $value = trim(strip_tags($body));
                } elseif (preg_match('/<v>(.*?)<\/v>/s', $body, $valueMatch)) {
                    $raw = trim($valueMatch[1]);
                    if ($type === 's' && isset($sharedStrings[(int) $raw])) {
                        $value = $sharedStrings[(int) $raw];
                    } elseif (ctype_digit($raw) && isset($sharedStrings[(int) $raw])) {
                        $value = $raw.' '.$sharedStrings[(int) $raw];
                    } else {
                        $value = $raw;
                    }
                } else {
                    $value = '';
                }

                if ($value !== '') {
                    $values[] = $value;
                }
            }
        }

        return implode(' ', $values);
    }

    private function detectFilenameGrade(string $name): ?int
    {
        $upper = Str::upper($name);

        if (preg_match('/\bK(?:E)?LAS[\s_]*(7|8|9)(?!\d)/', $upper, $match)) {
            return (int) $match[1];
        }

        if (preg_match('/KELAS[\s_]*(7|8|9)(?!\d)/', $upper, $match)) {
            return (int) $match[1];
        }

        if (preg_match('/\bKELAS[\s_]*(VII|VIII|IX)\b/', $upper, $match)) {
            return ['VII' => 7, 'VIII' => 8, 'IX' => 9][$match[1]];
        }

        if (preg_match('/\bKELAS(7|8|9)\b/', $upper, $match)) {
            return (int) $match[1];
        }

        if (preg_match('/\bKLS[\s_]*(7|8|9)(?!\d)/', $upper, $match)) {
            return (int) $match[1];
        }

        if (preg_match('/\bSMP\s+(VII|VIII|IX)\b/', $upper, $match)) {
            return ['VII' => 7, 'VIII' => 8, 'IX' => 9][$match[1]];
        }

        return null;
    }

    private function detectContentGrade(string $text): ?int
    {
        $upper = Str::upper($text);

        if (preg_match('/\bKELAS(?:\/SEMESTER)?\s*:?\s*(VII|VIII|IX|7|8|9)\b/', $upper, $match)) {
            return $this->gradeTokenToInt($match[1]);
        }

        return null;
    }

    private function gradeTokenToInt(string $token): ?int
    {
        return match (Str::upper($token)) {
            'VII', '7' => 7,
            'VIII', '8' => 8,
            'IX', '9' => 9,
            default => null,
        };
    }

    private function detectSubject(string $name, string $text): ?string
    {
        return $this->detectSubjectIn($name) ?? $this->detectSubjectIn($text);
    }

    private function detectSubjectIn(string $value): ?string
    {
        $haystack = Str::upper(str_replace(['_', '-', '.'], ' ', $value));

        $patterns = [
            'Bahasa Indonesia' => '/\b(B\.?\s*INDO?|BAHASA\s+INDONESIA)\b/',
            'Al Quran Hadits' => '/\b(AL\s+QURAN|AL\s+QUR\'?AN|QUR\'?AN\s+HADITS?|QURAN\s+HADISTS?)\b/',
            'Aqidah/Akhlak' => '/\b(AKIDAH|AQIDAH|AKHLAK)\b/',
            'Fiqh' => '/\b(FIQIH|FIQIK|FIKIH|FIQH)/',
            'Pendidikan Kewarganegaraan' => '/\b(PKN|PANCASILA|KEWARGANEGARAAN)\b/',
            'Matematika' => '/\b(MTK|MATEMATIKA)\b/',
            'Teknologi Digital' => '/\b(INFORMATIKA|TEKNOLOGI\s+DIGITAL|DIGITAL\s+TEKNOLOGI|TIK)\b/',
            'Ilmu Pengetahuan Alam' => '/\b(IPA|ILMU\s+PENGETAHUAN\s+ALAM|SAINS|MAKHLUK\s+HIDUP|ZAT|UNSUR|SENYAWA|CAMPURAN|TATA\s+SURYA|SISTEM\s+ORGAN|USAHA\s+ENERGI|PESAWAT\s+SEDERHANA)\b/',
            'Ilmu Pengetahuan Sosial' => '/\b(IPS|ILMU\s+PENGETAHUAN\s+SOSIAL)\b/',
            'PJOK' => '/\bPJOK\b/',
            'Seni Rupa' => '/\bSENI\s+RUPA\b/',
            'Seni Budaya dan Prakarya' => '/\b(SENBUD|SENI\s+BUDAYA|PRAKARYA)\b/',
            'Project Based Learning' => '/\b(PJBL|PROJECT\s+BASED)\b/',
        ];

        foreach ($patterns as $subject => $pattern) {
            if (preg_match($pattern, $haystack)) {
                return $subject;
            }
        }

        return null;
    }

    private function detectCategory(string $name, string $text): string
    {
        $haystack = Str::upper($name.' '.$text);

        if (str_contains($haystack, 'KISI')) {
            return 'kisi_kisi';
        }

        if (str_contains($haystack, 'PROGRAM TAHUNAN') || str_contains($haystack, 'PROGRAM SEMESTER')) {
            return 'program_tahunan_semester';
        }

        if (preg_match('/\bKI[-_ ]?KD\b/', $haystack)) {
            return 'kompetensi_dasar';
        }

        if (str_contains($haystack, 'RPPM') || preg_match('/\bRPM\b/', $haystack)) {
            return 'modul_ajar';
        }

        if (str_contains($haystack, 'LKPD')) {
            return 'lembar_kerja';
        }

        if (str_contains($haystack, 'LATIHAN SOAL') || str_contains($haystack, 'PILIHAN GANDA')) {
            return 'soal_latihan';
        }

        if (str_contains($haystack, 'CAPAIAN PEMBELAJARAN') && str_contains($haystack, 'ALUR TUJUAN PEMBELAJARAN')) {
            return 'alur_tujuan_pembelajaran';
        }

        if (preg_match('/\bCP\b/', $haystack) || str_contains($haystack, 'CAPAIAN PEMBELAJARAN')) {
            return 'capaian_pembelajaran';
        }

        return 'dokumen_lainnya';
    }

    private function detectSemester(string $name, string $text): ?string
    {
        $haystack = Str::upper($name.' '.$text);

        if (str_contains($haystack, 'SEMESTER GANJIL') && str_contains($haystack, 'SEMESTER GENAP')) {
            return 'Ganjil & Genap';
        }

        if (preg_match('/\b(SMT|SEMESTER)\s*1\b/', $haystack) || str_contains($haystack, 'SEMESTER GANJIL')) {
            return 'Ganjil';
        }

        if (preg_match('/\b(SMT|SEMESTER)\s*2\b/', $haystack) || str_contains($haystack, 'SEMESTER GENAP')) {
            return 'Genap';
        }

        return null;
    }

    private function detectAcademicYear(string $text): ?string
    {
        if (preg_match('/\b(20\d{2})\s*\/\s*(20\d{2})\b/', $text, $match)) {
            return $match[1].'/'.$match[2];
        }

        return null;
    }

    private function sensitivityFor(string $category): string
    {
        return in_array($category, ['kisi_kisi'], true) ? 'Sensitif' : 'Internal';
    }

    private function looksLikeSdDocument(string $text): bool
    {
        $upper = Str::upper($text);

        return str_contains($upper, 'SD/MI')
            || str_contains($upper, 'KELAS I SD')
            || str_contains($upper, 'KELAS 1 SD');
    }

    private function classificationFromContent(string $name, string $text): bool
    {
        if ($text === '') {
            return false;
        }

        return $this->detectFilenameGrade($name) === null
            || $this->detectSubject($name, '') === null;
    }

    private function titleFromFilename(string $name): string
    {
        $withoutExtension = preg_replace('/\.(docx|xlsx|pdf|doc|xls|pptx?|zip|rar)$/i', '', $name);

        return trim((string) $withoutExtension);
    }

    private function md(string $value): string
    {
        return str_replace('|', '\|', $value);
    }
}
