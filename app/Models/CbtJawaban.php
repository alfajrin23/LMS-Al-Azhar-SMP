<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CbtJawaban extends Model
{
    protected $fillable = [
        'cbt_exam_id', 'cbt_soal_id', 'siswa_id',
        'jawaban', 'nilai', 'dinilai'
    ];
    public function exam()
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }
    public function soal()
    {
        return $this->belongsTo(CbtSoal::class, 'cbt_soal_id');
    }
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
