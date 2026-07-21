<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahfidzAyatNilai extends Model
{
    protected $table = 'tahfidz_ayat_nilai';
    protected $fillable = [
        'tahfidz_setoran_id', 'guru_id', 'nomor_ayat', 'makhroj', 'tajwid', 'kelancaran'
    ];

    public function setoran()
    {
        return $this->belongsTo(TahfidzSetoran::class, 'tahfidz_setoran_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
