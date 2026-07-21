<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KehadiranGuru extends Model
{
    protected $fillable = ['guru_id', 'tanggal', 'waktu_masuk', 'waktu_pulang', 'status', 'keterangan'];
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
