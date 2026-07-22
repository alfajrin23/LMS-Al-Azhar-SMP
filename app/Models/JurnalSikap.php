<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalSikap extends Model
{
    protected $table = 'jurnal_sikaps';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'guru_id',
        'tanggal',
        'kejadian',
        'tindakan',
        'paraf',
        'tahun_ajaran',
        'semester',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
