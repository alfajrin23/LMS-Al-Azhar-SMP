<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KtiBimbingan extends Model
{
    protected $table = 'kti_bimbingans';
    protected $fillable = [
        'siswa_id', 'bab', 'file_draft', 'catatan_siswa', 'status', 'catatan_guru'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
