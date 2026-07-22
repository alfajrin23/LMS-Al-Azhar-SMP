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
        'tingkat_ummi',
        'posisi_tilawah',
        'hafalan_terakhir',
        'kelancaran',
        'tajwid',
        'makhroj',
        'adab',
        'predikat',
        'target_berikutnya',
        'tanggal_pertemuan_berikutnya',
        'status',
        'catatan',
        'updated_by',
    ];

    protected $casts = [
        'progress_percent' => 'decimal:2',
        'tanggal_pertemuan_berikutnya' => 'date',
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
