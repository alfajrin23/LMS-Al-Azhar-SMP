<?php

namespace App\Models;

use App\Support\SmpLearningDocumentInventory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
        'tipe',
        'kategori',
        'kode',
        'isi',
        'mapel_id',
        'kelas_id',
        'guru_id',
        'tahun_ajaran',
        'semester',
        'versi',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'catatan_reviewer',
        'skor_kauniyah',
        'skor_bilingual',
        'skor_ai',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'versi' => 'integer',
    ];

    public function mapel() { return $this->belongsTo(Mapel::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function guru() { return $this->belongsTo(Guru::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function approvalHistories() { return $this->hasMany(MateriApprovalHistory::class); }

    public function checklist()
    {
        $hasJudul = !empty($this->judul);
        $hasFile = !empty($this->file_path);
        $hasMapel = !empty($this->mapel_id);
        $hasKelas = !empty($this->kelas_id);
        $hasDeskripsi = !empty($this->deskripsi) && strlen(trim($this->deskripsi)) >= 20;
        $hasKategori = array_key_exists($this->kategori, SmpLearningDocumentInventory::categoryLabels());

        return [
            'judul' => $hasJudul,
            'file' => $hasFile,
            'mapel' => $hasMapel,
            'kelas' => $hasKelas,
            'deskripsi' => $hasDeskripsi,
            'kategori' => $hasKategori,
            'is_ready' => ($hasJudul && $hasFile && $hasMapel && $hasKelas && $hasDeskripsi && $hasKategori)
        ];
    }

    public function isEditableByGuru(): bool
    {
        return in_array($this->status, ['draft', 'rejected', 'revision_requested'], true);
    }
}
