<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badges';
    protected $fillable = ['nama', 'deskripsi', 'icon'];

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'siswa_badge')
            ->withPivot('achieved_at');
    }
}
