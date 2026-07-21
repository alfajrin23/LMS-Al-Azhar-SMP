<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Spp extends Model
{
    protected $table = 'spps';
    protected $fillable = [
        'siswa_id', 'bulan', 'tahun', 'jumlah', 'tenggat', 'status'
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'spp_id');
    }
}
