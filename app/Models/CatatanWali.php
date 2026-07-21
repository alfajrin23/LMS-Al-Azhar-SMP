<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanWali extends Model
{
    protected $table = 'catatan_wali';
    protected $fillable = [
        'siswa_id', 'semester', 'catatan', 'created_by'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'created_by');
    }
}
