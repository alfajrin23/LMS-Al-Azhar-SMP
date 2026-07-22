<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workbook extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'mapel_id', 'kelas_id', 'guru_id', 'tipe',
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

    public function soals()
    {
        return $this->hasMany(WorkbookSoal::class);
    }
}
