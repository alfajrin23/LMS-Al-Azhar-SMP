<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CbtSoal extends Model
{
    protected $fillable = [
        'cbt_exam_id', 'nomor', 'soal', 'tipe',
        'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d',
        'jawaban_benar', 'bobot', 'kesulitan'
    ];
    public function exam()
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }
}
