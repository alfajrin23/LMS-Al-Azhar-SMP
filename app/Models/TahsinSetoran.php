<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahsinSetoran extends Model
{
    protected $fillable = [
        'siswa_id',
        'guru_id',
        'kelas_id',
        'tanggal',
        'materi_tahsin',
        'jilid_halaman',
        'nilai',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
