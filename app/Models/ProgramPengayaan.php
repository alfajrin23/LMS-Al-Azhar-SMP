<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPengayaan extends Model
{
    protected $fillable = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'kompetensi_dasar',
        'materi',
        'bentuk_pengayaan',
        'keterangan',
        'tahun_ajaran',
        'semester',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
