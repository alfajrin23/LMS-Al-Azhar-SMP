<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';
    protected $fillable = [
        'siswa_id',
        'tugas_id',
        'mapel_id',
        'nilai',
        'nilai_bahasa',
        'jenis_nilai',
        'jenis_rapor',
        'tahun_ajaran',
        'semester',
        'lingkup_materi',
        'tujuan_pembelajaran',
        'tp_scores',
        'tugas_scores',
        'sumatif_scores',
        'nilai_sumatif',
        'capaian_kompetensi',
        'kompetensi_dikuasai',
        'kompetensi_perlu_ditingkatkan',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_bahasa' => 'decimal:2',
        'nilai_sumatif' => 'decimal:2',
        'tp_scores' => 'array',
        'tugas_scores' => 'array',
        'sumatif_scores' => 'array',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function banding()
    {
        return $this->hasOne(BandingNilai::class, 'nilai_id');
    }
}
