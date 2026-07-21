<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $fillable = [
        'judul', 'deskripsi', 'file_path', 'tipe', 'mapel_id', 'kelas_id', 'guru_id', 'status',
        'skor_kauniyah', 'skor_bilingual', 'skor_ai'
    ];

    public function mapel() { return $this->belongsTo(Mapel::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function guru() { return $this->belongsTo(Guru::class); }

    public function checklist()
    {
        $hasJudul = !empty($this->judul);
        $hasFile = !empty($this->file_path);
        $hasMapel = !empty($this->mapel_id);
        $hasKelas = !empty($this->kelas_id);
        $hasDeskripsi = !empty($this->deskripsi) && strlen(trim($this->deskripsi)) >= 20;

        return [
            'judul' => $hasJudul,
            'file' => $hasFile,
            'mapel' => $hasMapel,
            'kelas' => $hasKelas,
            'deskripsi' => $hasDeskripsi,
            'is_ready' => ($hasJudul && $hasFile && $hasMapel && $hasKelas && $hasDeskripsi)
        ];
    }
}
