<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    protected $fillable = [
        'siswa_id',
        'tahun_ajaran',
        'semester',
        'jenis_rapor',
        'status',
        'published_at',
        'created_by',
        'updated_by',
        'catatan',
        'snapshot',
        'signature_metadata',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'snapshot' => 'array',
        'signature_metadata' => 'array',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function items()
    {
        return $this->hasMany(RaporItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
