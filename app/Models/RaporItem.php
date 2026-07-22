<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaporItem extends Model
{
    protected $fillable = [
        'rapor_id',
        'mapel_id',
        'kategori',
        'komponen',
        'nilai',
        'predikat',
        'deskripsi',
        'metadata',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function rapor()
    {
        return $this->belongsTo(Rapor::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}
