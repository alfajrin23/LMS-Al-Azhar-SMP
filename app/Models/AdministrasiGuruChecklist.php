<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdministrasiGuruChecklist extends Model
{
    protected $table = 'administrasi_guru_checklists';

    protected $fillable = [
        'guru_id',
        'dokumen',
        'status',
        'tanggal_dilengkapi',
        'reviewed_by',
        'reviewed_at',
        'catatan_reviewer',
        'tahun_ajaran',
        'semester',
    ];

    protected $casts = [
        'tanggal_dilengkapi' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
