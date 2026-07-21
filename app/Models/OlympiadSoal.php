<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlympiadSoal extends Model
{
    protected $fillable = [
        'olympiad_exam_id', 'nomor', 'soal', 'tipe',
        'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d',
        'jawaban_benar', 'bobot'
    ];

    public function exam()
    {
        return $this->belongsTo(OlympiadExam::class, 'olympiad_exam_id');
    }
}
