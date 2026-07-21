<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Kehadiran extends Model
{
    protected $table = 'kehadiran';
    protected $fillable = [
        'siswa_id', 'tanggal', 'status', 'keterangan'
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
