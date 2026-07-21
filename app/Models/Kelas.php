<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['kode_kelas', 'nama_kelas', 'jenjang', 'guru_id'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
