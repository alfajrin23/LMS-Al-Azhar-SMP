<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BandingNilai extends Model
{
    protected $table = 'banding_nilai';
    protected $fillable = [
        'nilai_id', 'siswa_id', 'alasan_siswa', 'status', 'catatan_guru'
    ];

    public function nilai()
    {
        return $this->belongsTo(Nilai::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
