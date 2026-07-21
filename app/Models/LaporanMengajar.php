<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class LaporanMengajar extends Model
{
    use HasFactory;
    protected $fillable = ['guru_id', 'tipe', 'tanggal', 'isi'];
    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'isi' => 'array',
    ];
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
