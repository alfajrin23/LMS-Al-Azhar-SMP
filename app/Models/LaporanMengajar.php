<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMengajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'tipe',
        'tanggal',
        'hari',
        'jam_ke',
        'bahasan_materi',
        'keterangan',
        'tahun_ajaran',
        'semester',
        'isi',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'isi' => 'array',
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
