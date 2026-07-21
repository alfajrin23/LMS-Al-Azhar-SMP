<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapel';
    protected $fillable = ['nama_mapel', 'kode'];

    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_mapel', 'mapel_id', 'guru_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function scopeAkademik($query)
    {
        return $query->whereNotIn('kode', [
            'IST', 'DZH', 'ASHR_SMP', 'UPCR_SMP', 'DHUHA', 
            'UPCR', 'UPCR_PAS', 'QAIL', 'ISHOMA', 'PLG', 'SNCK',
            'TRNS', 'ASHR', 'ADM', 'ASHR_DZK', 'EKS'
        ]);
    }
}
