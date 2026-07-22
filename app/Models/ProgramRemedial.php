<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramRemedial extends Model
{
    protected $fillable = [
        'remedial_id',
        'guru_id',
        'siswa_id',
        'mapel_id',
        'kelas_id',
        'kompetensi_dasar',
        'materi',
        'nilai_sebelum',
        'nilai_sesudah',
        'keterangan',
        'paraf',
        'status',
        'tahun_ajaran',
        'semester',
    ];

    public function remedial()
    {
        return $this->belongsTo(Remedial::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
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
