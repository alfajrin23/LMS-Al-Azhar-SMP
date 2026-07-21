<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlympiadJawaban extends Model
{
    protected $fillable = [
        'olympiad_exam_id', 'olympiad_soal_id', 'siswa_id',
        'jawaban', 'nilai', 'dinilai'
    ];

    public function exam()
    {
        return $this->belongsTo(OlympiadExam::class, 'olympiad_exam_id');
    }

    public function soal()
    {
        return $this->belongsTo(OlympiadSoal::class, 'olympiad_soal_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
