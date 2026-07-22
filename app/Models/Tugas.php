<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'mapel_id', 'kelas_id',
        'guru_id', 'tipe', 'tanggal_deadline', 'file_path',
        'tahun_ajaran', 'semester'
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }
}
