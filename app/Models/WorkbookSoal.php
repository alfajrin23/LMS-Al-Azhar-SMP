<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkbookSoal extends Model
{
    protected $fillable = [
        'workbook_id', 'nomor', 'soal', 'tipe',
        'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d',
        'jawaban_benar', 'bobot'
    ];

    public function workbook()
    {
        return $this->belongsTo(Workbook::class);
    }
}
