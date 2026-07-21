<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    protected $fillable = [
        'tugas_id', 'siswa_id', 'file_path', 'catatan_siswa',
        'nilai', 'catatan_guru', 'dikumpulkan_at'
    ];

    protected $casts = [
        'dikumpulkan_at' => 'datetime',
    ];

    public function tugas() { return $this->belongsTo(Tugas::class); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
}
