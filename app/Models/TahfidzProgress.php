<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahfidzProgress extends Model
{
    protected $table = 'tahfidz_progress';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'kelas_quran_id',
        'surah',
        'ayat_mulai',
        'ayat_selesai',
        'juz_dihafal',
        'total_ayat',
        'progress_percent',
        'target_deskripsi',
        'status',
        'catatan',
        'updated_by',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function kelasQuran()
    {
        return $this->belongsTo(KelasQuran::class, 'kelas_quran_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
