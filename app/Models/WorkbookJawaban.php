<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkbookJawaban extends Model
{
    protected $fillable = [
        'workbook_soal_id', 'siswa_id', 'jawaban', 'nilai'
    ];
    public function soal()
    {
        return $this->belongsTo(WorkbookSoal::class, 'workbook_soal_id');
    }
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
